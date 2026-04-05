<?php

namespace App\Services;

use App\Models\DigitalSticker;
use App\Models\Registration;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QRCodeService
{
    public function generateForRegistration(Registration $registration, string $validityStart, string $validityEnd): DigitalSticker
    {
        $token = Str::uuid()->toString();

        // Encode the sticker URL in the QR code
        // This allows any QR scanner to open the sticker page directly
        $qrData = url(route('student.sticker', ['token' => $token], absolute: true));

        $imagePath = $this->generateQRImage($token, $qrData);

        return DigitalSticker::create([
            'registration_id' => $registration->id,
            'qr_code_token' => $token,
            'qr_code_image_path' => $imagePath,
            'validity_start_date' => $validityStart,
            'validity_end_date' => $validityEnd,
            'status' => 'valid',
        ]);
    }

    public function generateQRImage(string $token, string $data): string
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_L,
            'scale' => 10,
            'imageBase64' => false,
        ]);

        $qrcode = new QRCode($options);
        $imageData = $qrcode->render($data);

        $filename = 'qrcodes/'.$token.'.png';
        Storage::disk('public')->put($filename, $imageData);

        return $filename;
    }

    public function verifyToken(string $token): ?DigitalSticker
    {
        $sticker = DigitalSticker::where('qr_code_token', $token)
            ->with(['registration.vehicle.student.user', 'registration.vehicle.vehicleType'])
            ->first();

        if (! $sticker) {
            return null;
        }

        // Auto-expire stickers past their validity date
        if ($sticker->status === 'valid' && now()->isAfter($sticker->validity_end_date)) {
            $sticker->update(['status' => 'expired']);
        }

        return $sticker;
    }

    public function revokeSticker(DigitalSticker $sticker): void
    {
        $sticker->update(['status' => 'revoked']);
    }
}
