<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'studentId',
        'programId',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'programId');
    }
}
