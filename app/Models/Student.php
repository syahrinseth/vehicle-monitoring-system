<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    public const KOS_BENGKEL_OPTIONS = [
        'DIPLOMA KOMPUTER SISTEM' => 'DIPLOMA KOMPUTER SISTEM',
        'DIPLOMA TELEKOMUNIKASI' => 'DIPLOMA TELEKOMUNIKASI',
        'BENGKEL AUTOMOTIF' => 'BENGKEL AUTOMOTIF',
        'BENGKEL PEMBUATAN' => 'BENGKEL PEMBUATAN',
    ];

    protected $fillable = [
        'user_id',
        'matric_number',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'emergency_contact',
        'ic_number',
        'no_ndp',
        'kos_bengkel',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function activeRegistration()
    {
        return $this->hasOne(Registration::class)->where('status', 'approved')->latest();
    }
}
