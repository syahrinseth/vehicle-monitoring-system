<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'role', 'is_active', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            'admin'               => $this->role === 'admin',
            'institute-authority' => $this->role === 'institute_authority',
            'guard'               => $this->role === 'guard',
            'student'             => $this->role === 'student',
            default               => false,
        };
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGuard(): bool
    {
        return $this->role === 'guard';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isInstituteAuthority(): bool
    {
        return $this->role === 'institute_authority';
    }

    // Relationships
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function checkInLogs()
    {
        return $this->hasMany(CheckInLog::class, 'guard_id');
    }

    public function verifiedRegistrations()
    {
        return $this->hasMany(Registration::class, 'verified_by');
    }

    public function approvedRegistrations()
    {
        return $this->hasMany(Registration::class, 'approved_by');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }
}
