<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchRecord extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'studentId',
        'topic',
        'type',
        'teamMemberIds',
        'stage',
        'description',
        'professorNotes',
        'supervisorNotes',
        'criticNotes',
        'score',
        'usages',
        'needsFollowUp',
        'followUpTodoId',
    ];

    protected $casts = [
        'teamMemberIds' => 'array',
        'usages' => 'array',
        'needsFollowUp' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }
}
