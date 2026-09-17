<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'username',
        'password',
        'name',
        'fullName',
        'level',
        'role',
        'roleTitle',
        'scope',
        'gradeLabel',
        'managedGrades',
        'mentorId',
        'studentId',
        'studentName',
        'linkedStudentId',
        'isReadOnly',
        'canEdit',
        'canManageUsers',
        'canBackup',
        'isActive',
        'allowedTabs',
        'avatarBg',
        'phone',
        'nationalId',
        'personnelCode',
        'bankName',
        'bankAccount',
        'bankSheba',
        'lastLogin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'managedGrades' => 'array',
        'allowedTabs' => 'array',
        'isReadOnly' => 'boolean',
        'canEdit' => 'boolean',
        'canManageUsers' => 'boolean',
        'canBackup' => 'boolean',
        'isActive' => 'boolean',
        'lastLogin' => 'datetime',
    ];

    /**
     * Handle the UUID generation when creating a new User.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->id)) {
                $user->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Check if the user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin' || $this->canManageUsers;
    }

    /**
     * Check if a specific tab/module is allowed for this user.
     */
    public function isTabAllowed(string $tab): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (is_array($this->allowedTabs)) {
            return in_array($tab, $this->allowedTabs);
        }

        return false;
    }

    /**
     * Get the student profile associated with this user (if any).
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }
}
