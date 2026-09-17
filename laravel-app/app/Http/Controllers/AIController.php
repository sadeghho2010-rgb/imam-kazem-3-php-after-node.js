<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use App\Models\Student;
use Illuminate\Http\Request;

class AIController extends Controller
{
    protected GeminiService $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    /**
     * AI Educational and Behavioral consultation.
     */
    public function consult(Request $request)
    {
        $request->validate(['prompt' => 'required|string']);

        $systemPrompt = "شما مشاور و دستیار هوشمند مدیران و اساتید راهنمای حوزه علمیه هستید. با لحنی محترمانه، عالمانه و کاربردی پاسخ دهید.";
        $response = $this->gemini->generate($request->prompt, $systemPrompt);

        return response()->json(['response' => $response]);
    }

    /**
     * Analyze a student's educational and attendance record using AI.
     */
    public function analyzeStudentRecord(Request $request, $studentId)
    {
        $student = Student::with(['attendanceDetails', 'studyStats', 'enrollments.program'])->findOrFail($studentId);

        $prompt = "تحلیل پرونده طلبه گرامی:\nنام: {$student->name}\nپایه: {$student->grade}\nتعداد سوابق حضور و غیاب: " . $student->attendanceDetails->count() . "\nلطفاً ارزیابی آموزشی و مشاوره‌ای خود را ارائه دهید.";
        $systemPrompt = "تحلیلگر ارشد آموزشی حوزه علمیه جهت بررسی پیشرفت تحصیلی طلاب.";

        $response = $this->gemini->generate($prompt, $systemPrompt);

        return response()->json(['analysis' => $response]);
    }
}
