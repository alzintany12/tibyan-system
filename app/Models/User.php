<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'position',
        'is_active',
        'avatar',
        'last_login_at',
        'created_by'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    // العلاقات
    public function cases()
    {
        return $this->hasMany(LegalCase::class, 'assigned_to');
    }

    /**
     * Alias for backward compatibility:
     * بعض الأماكن في الكود تستدعي legalCases بدلاً من cases.
     */
    public function legalCases()
    {
        return $this->cases();
    }

    public function createdCases()
    {
        return $this->hasMany(LegalCase::class, 'created_by');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'created_by');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'created_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // التحقق من الصلاحيات
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isLawyer()
    {
        return $this->hasRole('lawyer');
    }

    public function isSecretary()
    {
        return $this->hasRole('secretary');
    }

    // تسجيل آخر دخول
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    // الحصول على الصورة الرمزية
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0d6efd&color=fff&size=200';
    }
}
