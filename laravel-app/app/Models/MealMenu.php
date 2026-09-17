<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealMenu extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'dayOfWeek',
        'date',
        'mealName',
        'price',
        'subsidy',
        'description',
        'isActive',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subsidy' => 'decimal:2',
        'isActive' => 'boolean',
    ];
}
