<?php

namespace App\Http\Controllers;

use App\Models\WorkflowItem;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkflowController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = WorkflowItem::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($user && !$user->isSuperAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('receiverUserId', $user->id)
                  ->orWhere('senderUserId', $user->id);
            });
        }

        return response()->json(['items' => $query->orderBy('created_at', 'desc')->get()]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'title' => 'required|string',
            'category' => 'required|string',
        ]);

        $item = WorkflowItem::create([
            'id' => (string) Str::uuid(),
            'studentId' => $request->studentId,
            'studentName' => $request->studentName,
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'status' => 'pending',
            'senderUserId' => $user?->id,
            'receiverUserId' => $request->receiverUserId,
            'senderName' => $user?->name,
            'receiverName' => $request->receiverName,
            'step' => 1,
            'history' => [
                [
                    'action' => 'ایجاد درخواست',
                    'by' => $user?->name,
                    'at' => now()->toIso8601String(),
                ]
            ],
            'attachmentUrl' => $request->attachmentUrl,
        ]);

        return response()->json(['item' => $item, 'message' => 'درخواست در کارتابل ثبت شد.'], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();
        $item = WorkflowItem::findOrFail($id);

        $request->validate([
            'status' => 'required|in:approved,rejected,needing_info',
            'comment' => 'nullable|string',
        ]);

        $item->status = $request->status;
        $history = $item->history ?? [];
        $history[] = [
            'action' => 'تغییر وضعیت به ' . $request->status,
            'by' => $user?->name,
            'comment' => $request->comment,
            'at' => now()->toIso8601String(),
        ];
        $item->history = $history;
        $item->save();

        return response()->json(['item' => $item, 'message' => 'وضعیت درخواست بروزرسانی شد.']);
    }
}
