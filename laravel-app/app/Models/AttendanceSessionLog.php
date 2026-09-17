<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSessionLog extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'programId',
        'programTitle',
        'grade',
        'date',
        'dayOfWeek',
        'isCancelled',
        'cancellationReason',
        'hasSubstituteTeacher',
        'substituteTeacherId',
        'substituteTeacherName',
        'substituteTeacherNotes',
        'notes',
        'recordedByUserId',
        'recordedByName',
        'recordedAt',
    ];

    protected $casts = [
        'isCancelled' => 'boolean',
        'hasSubstituteTeacher' => 'boolean',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'programId');
    }

    public function details()
    {
        return $this->hasMany(StudentAttendanceDetail::class, 'sessionLogId');
    }
}
