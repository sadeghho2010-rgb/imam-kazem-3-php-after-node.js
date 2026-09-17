<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'userId',
        'userName',
        'roleTitle',
        'action',
        'module',
        'details',
        'ip',
        'studentId',
        'studentName',
        'timestamp',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }
}
