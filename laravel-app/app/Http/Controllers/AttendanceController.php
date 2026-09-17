<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSessionLog;
use App\Models\StudentAttendanceDetail;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    /**
     * Get attendance session logs for a specific program or date range.
     */
    public function index(Request $request)
    {
        $query = AttendanceSessionLog::query();

        if ($request->filled('programId')) {
            $query->where('programId', $request->programId);
        }

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        $logs = $query->with('details.student')->orderBy('date', 'desc')->get();

        return response()->json([
            'sessionLogs' => $logs
        ]);
    }

    /**
     * Store or update an attendance session log with details for all students.
     */
    public function saveSession(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'programId' => 'required|string',
            'programTitle' => 'required|string',
            'date' => 'required|string',
            'students' => 'required|array',
            'students.*.studentId' => 'required|string',
            'students.*.status' => 'required|string', // present, absent, late, excused
        ]);

        $sessionId = $request->id ?: (string) Str::uuid();

        // Save or update session log
        $sessionLog = AttendanceSessionLog::updateOrCreate(
            ['id' => $sessionId],
            [
                'programId' => $request->programId,
                'programTitle' => $request->programTitle,
                'grade' => $request->grade,
                'date' => $request->date,
                'dayOfWeek' => $request->dayOfWeek,
                'isCancelled' => $request->isCancelled ?? false,
                'cancellationReason' => $request->cancellationReason,
                'hasSubstituteTeacher' => $request->hasSubstituteTeacher ?? false,
                'substituteTeacherId' => $request->substituteTeacherId,
                'substituteTeacherName' => $request->substituteTeacherName,
                'substituteTeacherNotes' => $request->substituteTeacherNotes,
                'notes' => $request->notes,
                'recordedByUserId' => $user?->id,
                'recordedByName' => $user?->name,
                'recordedAt' => now()->toIso8601String(),
            ]
        );

        // Delete old details for this session log
        StudentAttendanceDetail::where('sessionLogId', $sessionLog->id)->delete();

        // Insert fresh attendance details
        foreach ($request->students as $st) {
            StudentAttendanceDetail::create([
                'sessionLogId' => $sessionLog->id,
                'studentId' => $st['studentId'],
                'studentName' => $st['studentName'] ?? '',
                'nationalId' => $st['nationalId'] ?? null,
                'status' => $st['status'],
                'note' => $st['note'] ?? null,
                'lateMinutes' => $st['lateMinutes'] ?? 0,
                'isExcused' => $st['isExcused'] ?? false,
                'excuseReason' => $st['excuseReason'] ?? null,
                'hasEducationalWarning' => $st['hasEducationalWarning'] ?? false,
                'warningRegisteredBy' => $st['hasEducationalWarning'] ? $user?->name : null,
                'warningRegisteredAt' => $st['hasEducationalWarning'] ? now()->toIso8601String() : null,
            ]);
        }

        AuditLog::create([
            'userId' => $user?->id,
            'userName' => $user?->name,
            'roleTitle' => $user?->roleTitle,
            'action' => "ثبت حضور و غیاب کلاس {$request->programTitle} مورخ {$request->date}",
            'module' => 'حضور و غیاب',
            'details' => ['sessionLogId' => $sessionLog->id, 'count' => count($request->students)],
            'ip' => $request->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return response()->json([
            'sessionLog' => $sessionLog->load('details'),
            'message' => 'حضور و غیاب جلسه با موفقیت به ثبت رسید.'
        ]);
    }

    /**
     * Excuse an absence for a student.
     */
    public function excuseAbsence(Request $request, $detailId)
    {
        $user = $request->user();
        $detail = StudentAttendanceDetail::findOrFail($detailId);

        $request->validate([
            'isExcused' => 'required|boolean',
            'excuseReason' => 'nullable|string',
        ]);

        $detail->isExcused = $request->isExcused;
        $detail->excuseReason = $request->excuseReason;
        $detail->save();

        AuditLog::create([
            'userId' => $user?->id,
            'userName' => $user?->name,
            'roleTitle' => $user?->roleTitle,
            'action' => "توجیه غیبت طلبه {$detail->studentName}",
            'module' => 'حضور و غیاب',
            'studentId' => $detail->studentId,
            'studentName' => $detail->studentName,
            'details' => ['reason' => $request->excuseReason],
            'ip' => $request->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return response()->json([
            'detail' => $detail,
            'message' => 'وضعیت توجیه غیبت بروزرسانی شد.'
        ]);
    }
}
