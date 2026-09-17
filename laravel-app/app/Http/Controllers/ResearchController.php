<?php

namespace App\Http\Controllers;

use App\Models\ResearchRecord;
use App\Models\OralExam;
use App\Models\DiscussionGroup;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ResearchController extends Controller
{
    /**
     * Get research records.
     */
    public function index(Request $request)
    {
        $query = ResearchRecord::with('student');
        if ($request->filled('studentId')) {
            $query->where('studentId', $request->studentId);
        }
        return response()->json(['records' => $query->orderBy('created_at', 'desc')->get()]);
    }

    /**
     * Store research record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'studentId' => 'required|string|exists:students,id',
            'topic' => 'required|string',
            'stage' => 'required|string',
        ]);

        $data = $request->all();
        if (empty($data['id'])) {
            $data['id'] = (string) Str::uuid();
        }

        $record = ResearchRecord::create($data);

        return response()->json(['record' => $record, 'message' => 'پرونده پژوهشی ثبت شد.'], 201);
    }

    /**
     * Oral exams list.
     */
    public function oralExams(Request $request)
    {
        $query = OralExam::with('student');
        if ($request->filled('studentId')) {
            $query->where('studentId', $request->studentId);
        }
        return response()->json(['exams' => $query->orderBy('date', 'desc')->get()]);
    }

    /**
     * Store oral exam result.
     */
    public function storeOralExam(Request $request)
    {
        $request->validate([
            'studentId' => 'required|string|exists:students,id',
            'title' => 'required|string',
            'subjectType' => 'required|string',
            'score' => 'required|numeric',
            'examinerName' => 'required|string',
            'date' => 'required|string',
        ]);

        $data = $request->all();
        if (empty($data['id'])) {
            $data['id'] = (string) Str::uuid();
        }

        $exam = OralExam::create($data);

        return response()->json(['exam' => $exam, 'message' => 'نمره امتحان شفاهی با موفقیت ثبت شد.'], 201);
    }

    /**
     * Discussion groups list.
     */
    public function discussionGroups()
    {
        return response()->json(['groups' => DiscussionGroup::all()]);
    }

    /**
     * Store discussion group.
     */
    public function storeDiscussionGroup(Request $request)
    {
        $request->validate(['title' => 'required|string']);

        $data = $request->all();
        if (empty($data['id'])) {
            $data['id'] = (string) Str::uuid();
        }

        $group = DiscussionGroup::create($data);
        return response()->json(['group' => $group, 'message' => 'گروه مباحثه ثبت گردید.'], 201);
    }
}
