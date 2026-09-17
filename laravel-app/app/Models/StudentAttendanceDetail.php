<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAttendanceDetail extends Model
{
    protected $fillable = [
        'sessionLogId',
        'studentId',
        'studentName',
        'nationalId',
        'status',
        'note',
        'lateMinutes',
        'isExcused',
        'excuseReason',
        'hasEducationalWarning',
        'warningRegisteredBy',
        'warningRegisteredAt',
    ];

    protected $casts = [
        'isExcused' => 'boolean',
        'hasEducationalWarning' => 'boolean',
        'lateMinutes' => 'integer',
    ];

    public function sessionLog()
    {
        return $this->belongsTo(AttendanceSessionLog::class, 'sessionLogId');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }
}
