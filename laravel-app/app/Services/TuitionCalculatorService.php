<?php

namespace App\Services;

use App\Models\Student;
use App\Models\TuitionSettings;
use App\Models\FinanceLoan;

class TuitionCalculatorService
{
    /**
     * Calculate tuition details for a single student based on active settings and study hours.
     */
    public function calculateForStudent(Student $student, TuitionSettings $settings, array $studyStats = []): array
    {
        // 1. Base Tuition
        $isMarried = in_array($student->maritalStatus, ['متاهل', 'married']);
        $baseAmount = $settings->isBaseTuitionEqualForMarried 
            ? $settings->singleBaseTuition 
            : ($isMarried ? $settings->marriedBaseTuition : $settings->singleBaseTuition);

        // 2. Marriage Bonus
        $marriageBonus = 0;
        if ($isMarried && $settings->hasMarriageBonus) {
            if ($settings->marriageBonusType === 'percentage') {
                $marriageBonus = ($baseAmount * $settings->marriageBonusPercent) / 100;
            } else {
                $marriageBonus = $settings->marriageBonusAmount;
            }
        }

        // 3. Children Allowance
        $childrenBonus = 0;
        if ($settings->hasChildAllowance && $student->childrenCount > 0) {
            $childrenBonus = $student->childrenCount * $settings->childAllowance;
        }

        // 4. Turban (Talammoz / Talanbos) Allowance
        $turbanBonus = 0;
        $isMoammam = in_array($student->tammomStatus, ['معمم', 'تلبس دائم', 'yes']);
        if ($settings->hasTurbanAllowance && $isMoammam) {
            $turbanBonus = $settings->turbanAllowance;
        }

        // 5. Housing Allowance
        $housingBonus = 0;
        if ($settings->hasHousingAllowance) {
            if (in_array($student->livingStatus, ['مستاجر', 'rented'])) {
                $housingBonus = $settings->housingAllowanceRented;
            } elseif (in_array($student->livingStatus, ['خوابگاهی', 'dormitory'])) {
                $housingBonus = $settings->housingAllowanceDorm;
            }
        }

        // 6. Study Hours Bonus / Penalty calculation
        $studyBonus = 0;
        $totalStudyHours = $studyStats['totalHours'] ?? 0;
        $mandatoryHours = $studyStats['mandatoryHours'] ?? 40;

        if ($settings->studyBonusEnabled) {
            $diffHours = $totalStudyHours - $mandatoryHours;
            if ($settings->studyBonusTiered && !empty($settings->studyBonusTiers)) {
                // Tiered calculation
                foreach ($settings->studyBonusTiers as $tier) {
                    if ($totalStudyHours >= ($tier['minHours'] ?? 0)) {
                        $studyBonus = $tier['amount'] ?? 0;
                    }
                }
            } else {
                // Per hour calculation
                if ($diffHours > 0) {
                    $studyBonus = $diffHours * $settings->studyBonusPerHour;
                }
            }
        }

        // 7. Custom allowances and deductions from StudentFinancialProfile
        $financialProfile = $student->financialProfile;
        $customAllowance = $financialProfile ? (float)$financialProfile->customAllowanceAmount : 0;
        $customDeduction = $financialProfile ? (float)$financialProfile->customDeductionAmount : 0;

        // 8. Active Loan Installment Auto-Deduction
        $loanDeductions = 0;
        $activeLoans = FinanceLoan::where('studentId', $student->id)
            ->where('status', 'active')
            ->where('remainingInstallments', '>', 0)
            ->get();

        foreach ($activeLoans as $loan) {
            $loanDeductions += (float)$loan->installmentAmount;
        }

        // Gross and Net calculation
        $grossTotal = $baseAmount + $marriageBonus + $childrenBonus + $turbanBonus + $housingBonus + $studyBonus + $customAllowance;
        $netTotal = max(0, $grossTotal - $customDeduction - $loanDeductions);

        return [
            'studentId' => $student->id,
            'studentName' => $student->name,
            'grade' => $student->grade,
            'isMarried' => $isMarried,
            'childrenCount' => $student->childrenCount,
            'isMoammam' => $isMoammam,
            'livingStatus' => $student->livingStatus,
            'baseAmount' => $baseAmount,
            'marriageBonus' => $marriageBonus,
            'childrenBonus' => $childrenBonus,
            'turbanBonus' => $turbanBonus,
            'housingBonus' => $housingBonus,
            'studyBonus' => $studyBonus,
            'customAllowance' => $customAllowance,
            'customDeduction' => $customDeduction,
            'loanDeductions' => $loanDeductions,
            'grossTotal' => $grossTotal,
            'netTotal' => $netTotal,
            'activeLoansCount' => $activeLoans->count(),
        ];
    }
}
