<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Enrollment;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProgramController extends Controller
{
    /**
     * Display a listing of educational programs/classes.
     */
    public function index(Request $request)
    {
        $query = Program::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        if ($request->filled('mentorId')) {
            $query->where('mentorId', $request->mentorId);
        }

        $programs = $query->with('students')->orderBy('title', 'asc')->get();

        return response()->json([
            'programs' => $programs
        ]);
    }

    /**
     * Store a new program.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'grade' => 'nullable|string',
        ]);

        $data = $request->all();
        if (empty($data['id'])) {
            $data['id'] = (string) Str::uuid();
        }

        $program = Program::create($data);

        AuditLog::create([
            'userId' => $user?->id,
            'userName' => $user?->name,
            'roleTitle' => $user?->roleTitle,
            'action' => "ایجاد برنامه/درس جدید: {$program->title}",
            'module' => 'آموزش',
            'details' => ['type' => $program->type, 'grade' => $program->grade],
            'ip' => $request->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return response()->json([
            'program' => $program,
            'message' => 'برنامه درسی با موفقیت تعریف شد.'
        ], 201);
    }

    /**
     * Enroll students in a program.
     */
    public function enrollStudents(Request $request, $id)
    {
        $program = Program::findOrFail($id);

        $request->validate([
            'studentIds' => 'required|array',
            'studentIds.*' => 'string|exists:students,id',
        ]);

        // Sync enrollments
        Enrollment::where('programId', $id)->delete();

        foreach ($request->studentIds as $studentId) {
            Enrollment::create([
                'id' => (string) Str::uuid(),
                'programId' => $id,
                'studentId' => $studentId,
            ]);
        }

        return response()->json([
            'message' => 'لیست طلاب ثبت‌نام شده در کلاس بروزرسانی گردید.',
            'enrolledCount' => count($request->studentIds),
        ]);
    }

    /**
     * Update the specified program.
     */
    public function update(Request $request, $id)
    {
        $program = Program::findOrFail($id);
        $program->update($request->all());

        return response()->json([
            'program' => $program,
            'message' => 'اطلاعات برنامه درسی بروزرسانی شد.'
        ]);
    }

    /**
     * Remove the specified program.
     */
    public function destroy(Request $request, $id)
    {
        $program = Program::findOrFail($id);
        $program->delete();

        return response()->json([
            'message' => 'برنامه درسی با موفقیت حذف گردید.'
        ]);
    }
}
