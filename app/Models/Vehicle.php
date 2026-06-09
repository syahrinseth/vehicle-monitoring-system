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
        'model',
        'payment_receipt_path',
        'review_status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
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

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isApproved(): bool
    {
        return $this->review_status === 'approved';
    }

    public function isPendingReview(): bool
    {
        return $this->review_status === 'pending';
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
