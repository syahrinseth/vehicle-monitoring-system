<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DigitalSticker extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'qr_code_token',
        'qr_code_image_path',
        'validity_start_date',
        'validity_end_date',
        'status',
        'generated_at',
        'downloaded_at',
    ];

    protected $casts = [
        'validity_start_date' => 'date',
        'validity_end_date'   => 'date',
        'generated_at'        => 'datetime',
        'downloaded_at'       => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($sticker) {
            if (empty($sticker->qr_code_token)) {
                $sticker->qr_code_token = Str::uuid()->toString();
            }
            $sticker->generated_at = now();
        });
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function checkInLogs()
    {
        return $this->hasMany(CheckInLog::class);
    }

    public function isValid(): bool
    {
        return $this->status === 'valid'
            && now()->between($this->validity_start_date, $this->validity_end_date);
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired'
            || now()->isAfter($this->validity_end_date);
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->isValid()) {
            return 'Valid';
        }
        if ($this->status === 'revoked') {
            return 'Revoked';
        }
        return 'Expired';
    }
}
