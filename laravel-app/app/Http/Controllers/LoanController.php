<?php

namespace App\Http\Controllers;

use App\Models\FinanceLoan;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LoanController extends Controller
{
    /**
     * List all loans with optional status filter.
     */
    public function index(Request $request)
    {
        $query = FinanceLoan::with('student');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('studentId')) {
            $query->where('studentId', $request->studentId);
        }

        $loans = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'loans' => $loans
        ]);
    }

    /**
     * Store a new loan for a student.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'studentId' => 'required|string|exists:students,id',
            'loanAmount' => 'required|numeric|min:1000',
            'installmentAmount' => 'required|numeric|min:100',
            'totalInstallments' => 'required|integer|min:1',
            'loanDate' => 'nullable|string',
        ]);

        $loan = FinanceLoan::create([
            'id' => (string) Str::uuid(),
            'studentId' => $request->studentId,
            'loanAmount' => $request->loanAmount,
            'installmentAmount' => $request->installmentAmount,
            'totalInstallments' => $request->totalInstallments,
            'remainingInstallments' => $request->totalInstallments,
            'paidInstallmentsCount' => 0,
            'status' => 'active',
            'loanDate' => $request->loanDate ?? now()->format('Y/m/d'),
            'description' => $request->description,
        ]);

        AuditLog::create([
            'userId' => $user?->id,
            'userName' => $user?->name,
            'roleTitle' => $user?->roleTitle,
            'action' => "پرداخت و تخصیص وام جدید قرض‌الحسنه به مبلغ {$loan->loanAmount} تومان",
            'module' => 'صندوق وام',
            'studentId' => $loan->studentId,
            'details' => ['loanId' => $loan->id, 'installments' => $loan->totalInstallments],
            'ip' => $request->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return response()->json([
            'loan' => $loan,
            'message' => 'وام قرض‌الحسنه با موفقیت برای طلبه ثبت و فعال گردید.'
        ], 201);
    }

    /**
     * Pay an installment manually or settle loan.
     */
    public function payInstallment(Request $request, $id)
    {
        $user = $request->user();
        $loan = FinanceLoan::findOrFail($id);

        if ($loan->remainingInstallments <= 0 || $loan->status === 'settled') {
            return response()->json(['error' => 'این وام قبلاً به طور کامل تسویه شده است.'], 400);
        }

        $loan->paidInstallmentsCount += 1;
        $loan->remainingInstallments -= 1;

        if ($loan->remainingInstallments <= 0) {
            $loan->status = 'settled';
        }

        $loan->save();

        AuditLog::create([
            'userId' => $user?->id,
            'userName' => $user?->name,
            'roleTitle' => $user?->roleTitle,
            'action' => "وصول قسط وام ({$loan->paidInstallmentsCount} از {$loan->totalInstallments})",
            'module' => 'صندوق وام',
            'studentId' => $loan->studentId,
            'details' => ['loanId' => $loan->id, 'remaining' => $loan->remainingInstallments],
            'ip' => $request->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return response()->json([
            'loan' => $loan,
            'message' => $loan->status === 'settled' ? 'آخرین قسط وصول شد و پرونده وام تسویه گردید.' : 'قسط وام با موفقیت کسر و ثبت شد.'
        ]);
    }
}
