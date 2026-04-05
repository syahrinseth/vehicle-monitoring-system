<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'vehicle_type_id',
        'registration_number',
        'color',
        'manufacturer',
        'model',
        'year',
        'engine_number',
        'chassis_number',
        'registration_document_path',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function activeRegistration()
    {
        return $this->hasOne(Registration::class)->where('status', 'approved')->latest();
    }

    public function checkInLogs()
    {
        return $this->hasMany(CheckInLog::class);
    }

    public function latestSticker()
    {
        return $this->hasOneThrough(
            DigitalSticker::class,
            Registration::class,
            'vehicle_id',
            'registration_id'
        )->where('digital_stickers.status', 'valid')->latest('digital_stickers.created_at');
    }
}
