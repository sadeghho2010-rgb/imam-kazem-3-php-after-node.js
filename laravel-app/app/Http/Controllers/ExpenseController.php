<?php

namespace App\Http\Controllers;

use App\Models\FinanceExpense;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = FinanceExpense::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        $expenses = $query->orderBy('date', 'desc')->get();

        return response()->json([
            'expenses' => $expenses,
            'totalAmount' => $expenses->sum('amount')
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric|min:100',
            'category' => 'required|string',
            'date' => 'required|string',
        ]);

        $expense = FinanceExpense::create([
            'id' => (string) Str::uuid(),
            'title' => $request->title,
            'amount' => $request->amount,
            'category' => $request->category,
            'date' => $request->date,
            'payTo' => $request->payTo,
            'factorPhotoUrl' => $request->factorPhotoUrl,
            'status' => 'approved',
            'recordedBy' => $user?->name,
        ]);

        AuditLog::create([
            'userId' => $user?->id,
            'userName' => $user?->name,
            'roleTitle' => $user?->roleTitle,
            'action' => "ثبت سند هزینه تنخواه‌گردان: {$expense->title} ({$expense->amount} تومان)",
            'module' => 'تنخواه و هزینه‌ها',
            'details' => ['expenseId' => $expense->id, 'category' => $expense->category],
            'ip' => $request->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return response()->json([
            'expense' => $expense,
            'message' => 'سند هزینه با موفقیت ثبت شد.'
        ], 201);
    }
}
