<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'title',
        'code',
        'capacity',
        'floor',
        'facilities',
        'notes',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'facilities' => 'array',
    ];
}
