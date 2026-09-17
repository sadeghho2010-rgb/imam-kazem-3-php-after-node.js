<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'photoUrl',
        'nationalId',
        'isActive',
        'phoneNumber',
        'grade',
        'managementCenterCode',
        'instituteCode',
        'servicesCenterCode',
        'birthDate',
        'birthPlace',
        'fatherName',
        'fatherOccupation',
        'tammomStatus',
        'maritalStatus',
        'childrenCount',
        'livingStatus',
        'livingStatusOther',
        'classicEducation',
        'howzaEntryYear',
        'instituteEntryYear',
        'levelOneSchool',
        'deactivationReason',
        'deactivationDate',
        'deactivationNotes',
        'tuitionCode',
        'bankName1',
        'bankAccount1',
        'bankSheba1',
        'bankName2',
        'bankAccount2',
        'bankSheba2',
        'activeDepositAccount',
        'pastGrades',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'isActive' => 'boolean',
        'childrenCount' => 'integer',
        'pastGrades' => 'array',
    ];

    /**
     * Get the enrollments for the student.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'studentId');
    }

    /**
     * Get the programs/courses the student is enrolled in.
     */
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'enrollments', 'studentId', 'programId');
    }

    /**
     * Get the attendance details for the student.
     */
    public function attendanceDetails()
    {
        return $this->hasMany(StudentAttendanceDetail::class, 'studentId');
    }

    /**
     * Get the study stats daily for the student.
     */
    public function studyStats()
    {
        return $this->hasMany(StudyStat::class, 'studentId');
    }

    /**
     * Get the study logs for the student.
     */
    public function studyLogs()
    {
        return $this->hasMany(PeriodicStudyLog::class, 'studentId');
    }

    /**
     * Get the financial profile for the student.
     */
    public function financialProfile()
    {
        return $this->hasOne(StudentFinancialProfile::class, 'studentId');
    }

    /**
     * Get the loans for the student.
     */
    public function loans()
    {
        return $this->hasMany(FinanceLoan::class, 'studentId');
    }

    /**
     * Get the meal reservations for the student.
     */
    public function mealReservations()
    {
        return $this->hasMany(MealReservation::class, 'studentId');
    }
}
