<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowItem extends Model
{
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'studentId',
        'studentName',
        'title',
        'description',
        'category',
        'status',
        'senderUserId',
        'receiverUserId',
        'senderName',
        'receiverName',
        'step',
        'history',
        'attachmentUrl',
    ];

    protected $casts = [
        'history' => 'array',
        'step' => 'integer',
    ];
}
