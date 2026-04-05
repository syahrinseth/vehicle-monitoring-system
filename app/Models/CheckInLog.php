<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckInLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'digital_sticker_id',
        'guard_id',
        'scan_method',
        'access_granted',
        'denial_reason',
        'scanner_ip',
        'notes',
        'scanned_at',
    ];

    protected $casts = [
        'access_granted' => 'boolean',
        'scanned_at' => 'datetime',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function digitalSticker()
    {
        return $this->belongsTo(DigitalSticker::class);
    }

    public function guardUser()
    {
        return $this->belongsTo(User::class, 'guard_id');
    }
}
