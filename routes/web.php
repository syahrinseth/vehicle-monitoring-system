<?php

use App\Models\DigitalSticker;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public sticker display route (for students to show on phone)
Route::get('/sticker/{token}', function (string $token) {
    $sticker = DigitalSticker::where('qr_code_token', $token)
        ->with(['registration.vehicle.vehicleType', 'registration.student.user'])
        ->firstOrFail();

    return view('sticker.show', compact('sticker'));
})->name('student.sticker');
