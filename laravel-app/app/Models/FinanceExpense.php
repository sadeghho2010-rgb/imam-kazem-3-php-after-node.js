<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceExpense extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'title',
        'amount',
        'category',
        'date',
        'payTo',
        'factorPhotoUrl',
        'status',
        'recordedBy',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
