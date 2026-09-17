<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    /**
     * Display a listing of students with optional filtering.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Student::query();

        // Filter by active status
        if ($request->has('onlyActive') && filter_var($request->onlyActive, FILTER_VALIDATE_BOOLEAN)) {
            $query->where('isActive', true);
        }

        // Filter by grade
        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        // Scope filter for mentors/grade supervisors
        if ($user && !$user->isSuperAdmin()) {
            if ($user->gradeLabel) {
                $query->where('grade', $user->gradeLabel);
            } elseif (!empty($user->managedGrades)) {
                $query->whereIn('grade', $user->managedGrades);
            }
        }

        // Search query (name, nationalId, managementCenterCode)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nationalId', 'like', "%{$search}%")
                  ->orWhere('managementCenterCode', 'like', "%{$search}%")
                  ->orWhere('phoneNumber', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name', 'asc')->get();

        return response()->json([
            'students' => $students,
            'total' => $students->count(),
        ]);
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'grade' => 'nullable|string',
            'nationalId' => 'nullable|string|unique:students,nationalId',
            'phoneNumber' => 'nullable|string',
        ]);

        $data = $request->all();

        if (empty($data['id'])) {
            $data['id'] = (string) Str::uuid();
        }

        $student = Student::create($data);

        // Audit Log
        AuditLog::create([
            'userId' => $user?->id,
            'userName' => $user?->name,
            'roleTitle' => $user?->roleTitle,
            'action' => "ثبت پرونده طلبه جدید: {$student->name}",
            'module' => 'آموزش',
            'studentId' => $student->id,
            'studentName' => $student->name,
            'details' => ['grade' => $student->grade],
            'ip' => $request->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return response()->json([
            'student' => $student,
            'message' => 'پرونده طلبه با موفقیت ایجاد شد.'
        ], 201);
    }

    /**
     * Display the specified student profile with full relations.
     */
    public function show($id)
    {
        $student = Student::with([
            'enrollments.program',
            'financialProfile',
            'loans',
            'studyLogs',
        ])->findOrFail($id);

        return response()->json([
            'student' => $student
        ]);
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $student = Student::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'nationalId' => "sometimes|nullable|string|unique:students,nationalId,{$id},id",
        ]);

        $student->update($request->all());

        // Audit Log
        AuditLog::create([
            'userId' => $user?->id,
            'userName' => $user?->name,
            'roleTitle' => $user?->roleTitle,
            'action' => "ویرایش مشخصات طلبه: {$student->name}",
            'module' => 'آموزش',
            'studentId' => $student->id,
            'studentName' => $student->name,
            'details' => [],
            'ip' => $request->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return response()->json([
            'student' => $student,
            'message' => 'بروزرسانی مشخصات طلبه انجام شد.'
        ]);
    }

    /**
     * Deactivate or activate student profile.
     */
    public function toggleActiveStatus(Request $request, $id)
    {
        $user = $request->user();
        $student = Student::findOrFail($id);

        $request->validate([
            'isActive' => 'required|boolean',
            'deactivationReason' => 'nullable|string',
            'deactivationDate' => 'nullable|string',
            'deactivationNotes' => 'nullable|string',
        ]);

        $student->isActive = $request->isActive;
        if (!$request->isActive) {
            $student->deactivationReason = $request->deactivationReason;
            $student->deactivationDate = $request->deactivationDate ?? now()->format('Y/m/d');
            $student->deactivationNotes = $request->deactivationNotes;
        } else {
            $student->deactivationReason = null;
            $student->deactivationDate = null;
            $student->deactivationNotes = null;
        }

        $student->save();

        $actionText = $request->isActive ? "فعال‌سازی پرونده طلبه: {$student->name}" : "غیرفعال‌سازی پرونده طلبه: {$student->name}";

        AuditLog::create([
            'userId' => $user?->id,
            'userName' => $user?->name,
            'roleTitle' => $user?->roleTitle,
            'action' => $actionText,
            'module' => 'آموزش',
            'studentId' => $student->id,
            'studentName' => $student->name,
            'details' => ['reason' => $request->deactivationReason],
            'ip' => $request->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return response()->json([
            'student' => $student,
            'message' => 'وضعیت پرونده طلبه تغییر یافت.'
        ]);
    }

    /**
     * Remove the specified student profile.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $student = Student::findOrFail($id);
        $name = $student->name;

        $student->delete();

        AuditLog::create([
            'userId' => $user?->id,
            'userName' => $user?->name,
            'roleTitle' => $user?->roleTitle,
            'action' => "حذف پرونده طلبه: {$name}",
            'module' => 'آموزش',
            'studentId' => $id,
            'studentName' => $name,
            'details' => [],
            'ip' => $request->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return response()->json([
            'message' => 'پرونده طلبه با موفقیت حذف گردید.'
        ]);
    }
}
