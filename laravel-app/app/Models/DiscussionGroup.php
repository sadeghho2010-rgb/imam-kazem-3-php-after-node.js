<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscussionGroup extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'title',
        'subject',
        'grade',
        'mentorId',
        'programId',
        'programTitle',
        'memberStudentIds',
        'externalMembers',
        'room',
        'description',
    ];

    protected $casts = [
        'memberStudentIds' => 'array',
        'externalMembers' => 'array',
    ];
}
