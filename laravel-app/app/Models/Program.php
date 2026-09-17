<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'title',
        'type',
        'day',
        'days',
        'time',
        'startTime',
        'endTime',
        'teacher',
        'classroom',
        'grade',
        'capacity',
        'notes',
        'mentorId',
        'parentProgramId',
        'representativeStudentIds',
        'representativeNames',
        'customRepresentative',
    ];

    protected $casts = [
        'days' => 'array',
        'representativeStudentIds' => 'array',
        'representativeNames' => 'array',
    ];

    /**
     * Get the students enrolled in this program.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollments', 'programId', 'studentId');
    }

    /**
     * Get the enrollments for the program.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'programId');
    }
}
