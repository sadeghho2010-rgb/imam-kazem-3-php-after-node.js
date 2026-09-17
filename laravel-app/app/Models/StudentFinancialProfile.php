<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFinancialProfile extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'studentId',
        'debtAmount',
        'creditAmount',
        'bankDetails',
        'isExemptFromTuition',
        'customAllowanceAmount',
        'customDeductionAmount',
        'customReason',
    ];

    protected $casts = [
        'isExemptFromTuition' => 'boolean',
        'debtAmount' => 'decimal:2',
        'creditAmount' => 'decimal:2',
        'customAllowanceAmount' => 'decimal:2',
        'customDeductionAmount' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }
}
