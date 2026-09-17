<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealReservation extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'studentId',
        'nationalId',
        'date',
        'status',
        'pricePaid',
    ];

    protected $casts = [
        'pricePaid' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }
}
