<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TuitionPeriod extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'title',
        'startDate',
        'endDate',
        'status',
        'totalStudentsCalculated',
        'totalPayoutAmount',
        'calculations',
    ];

    protected $casts = [
        'calculations' => 'array',
        'totalPayoutAmount' => 'decimal:2',
        'totalStudentsCalculated' => 'integer',
    ];
}
