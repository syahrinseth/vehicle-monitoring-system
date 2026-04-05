<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'vehicle_id',
        'status',
        'submitted_at',
        'verified_by',
        'verified_at',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'rejected_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'verified_at'  => 'datetime',
        'approved_at'  => 'datetime',
        'rejected_at'  => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function digitalSticker()
    {
        return $this->hasOne(DigitalSticker::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
