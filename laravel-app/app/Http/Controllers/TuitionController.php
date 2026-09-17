<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\TuitionPeriod;
use App\Models\TuitionSettings;
use App\Models\AuditLog;
use App\Services\TuitionCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TuitionController extends Controller
{
    protected TuitionCalculatorService $calculator;

    public function __construct(TuitionCalculatorService $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Get active tuition settings.
     */
    public function getSettings()
    {
        $settings = TuitionSettings::first();
        if (!$settings) {
            $settings = TuitionSettings::create([
                'id' => (string) Str::uuid(),
                'isBaseTuitionEqualForMarried' => false,
                'singleBaseTuition' => 1200000,
                'marriedBaseTuition' => 1800000,
                'hasMarriageBonus' => true,
                'marriageBonusType' => 'fixed',
                'marriageBonusAmount' => 200000,
                'hasChildAllowance' => true,
                'childAllowance' => 150000,
                'hasTurbanAllowance' => true,
                'turbanAllowance' => 300000,
                'hasHousingAllowance' => true,
                'housingAllowanceRented' => 400000,
                'housingAllowanceDorm' => 100000,
                'studyBonusEnabled' => true,
                'studyBonusPerHour' => 25000,
            ]);
        }

        return response()->json([
            'settings' => $settings
        ]);
    }

    /**
     * Update tuition settings.
     */
    public function updateSettings(Request $request)
    {
        $user = $request->user();
        $settings = TuitionSettings::firstOrFail();
        $settings->update($request->all());

        AuditLog::create([
            'userId' => $user?->id,
            'userName' => $user?->name,
            'roleTitle' => $user?->roleTitle,
            'action' => 'بروزرسانی فرمول و تنظیمات شهریه',
            'module' => 'مالی و شهریه',
            'details' => $request->all(),
            'ip' => $request->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return response()->json([
            'settings' => $settings,
            'message' => 'تنظیمات محاسباتی شهریه بروزرسانی شد.'
        ]);
    }

    /**
     * Calculate and generate a new tuition period.
     */
    public function calculatePeriod(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'title' => 'required|string',
            'startDate' => 'required|string',
            'endDate' => 'required|string',
        ]);

        $settings = TuitionSettings::firstOrFail();
        $students = Student::where('isActive', true)->with('financialProfile')->get();

        $calculations = [];
        $totalPayout = 0;

        foreach ($students as $student) {
            // If exempt from tuition, skip
            if ($student->financialProfile && $student->financialProfile->isExemptFromTuition) {
                continue;
            }

            $calc = $this->calculator->calculateForStudent($student, $settings);
            $calculations[] = $calc;
            $totalPayout += $calc['netTotal'];
        }

        $period = TuitionPeriod::create([
            'id' => (string) Str::uuid(),
            'title' => $request->title,
            'startDate' => $request->startDate,
            'endDate' => $request->endDate,
            'status' => 'finalized',
            'totalStudentsCalculated' => count($calculations),
            'totalPayoutAmount' => $totalPayout,
            'calculations' => $calculations,
        ]);

        AuditLog::create([
            'userId' => $user?->id,
            'userName' => $user?->name,
            'roleTitle' => $user?->roleTitle,
            'action' => "محاسبه و نهایی‌سازی دوره پرداخت شهریه: {$period->title}",
            'module' => 'مالی و شهریه',
            'details' => [
                'totalStudents' => count($calculations),
                'totalPayout' => $totalPayout,
            ],
            'ip' => $request->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return response()->json([
            'period' => $period,
            'message' => 'محاسبات دوره شهریه با موفقیت صادر گردید.'
        ], 201);
    }

    /**
     * Get all tuition periods list.
     */
    public function getPeriods()
    {
        $periods = TuitionPeriod::orderBy('created_at', 'desc')->get();
        return response()->json([
            'periods' => $periods
        ]);
    }
}
