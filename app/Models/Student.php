<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'matric_number',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'emergency_contact',
        'ic_number',
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
