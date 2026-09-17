<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceLoan extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'studentId',
        'loanAmount',
        'installmentAmount',
        'totalInstallments',
        'remainingInstallments',
        'paidInstallmentsCount',
        'status',
        'loanDate',
        'description',
    ];

    protected $casts = [
        'loanAmount' => 'decimal:2',
        'installmentAmount' => 'decimal:2',
        'totalInstallments' => 'integer',
        'remainingInstallments' => 'integer',
        'paidInstallmentsCount' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }
}
