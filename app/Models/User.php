<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    use \Illuminate\Auth\MustVerifyEmail;

    protected $fillable = [
        'name',
        'email',
        'department_id',
        'designation',
        'password',
        'is_super_admin',
        'is_active',
        'password_must_change',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_super_admin' => 'boolean',
        'is_active' => 'boolean',
        'password_must_change' => 'boolean',
    ];

    public function departmentRelation()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function appPermissions()
    {
        return $this->hasMany(UserAppPermission::class);
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function mustChangePassword(): bool
    {
        return (bool) $this->password_must_change;
    }

    public function certificatesCreated()
    {
        return $this->hasMany(\App\Models\Certificate::class, 'created_by_id');
    }

    public function certificatesReviewed()
    {
        return $this->hasMany(\App\Models\Certificate::class, 'review_by_id');
    }

    public function certificatesApproved()
    {
        return $this->hasMany(\App\Models\Certificate::class, 'approval_by_id');
    }

    public function certificatesUploaded()
    {
        return $this->hasMany(\App\Models\Certificate::class, 'pdf_uploaded_by_id');
    }
}
