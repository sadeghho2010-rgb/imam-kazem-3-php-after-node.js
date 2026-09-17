<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OralExam extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'studentId',
        'title',
        'subjectType',
        'score',
        'examinerName',
        'date',
        'isRetake',
        'notes',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'isRetake' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }
}
