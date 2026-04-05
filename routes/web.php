<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/student/login');
});

// Public sticker display route (for students to show on phone)
Route::get('/sticker/{token}', function (string $token) {
    $sticker = \App\Models\DigitalSticker::where('qr_code_token', $token)
        ->with(['registration.vehicle.vehicleType', 'registration.student.user'])
        ->firstOrFail();
    return view('sticker.show', compact('sticker'));
})->name('student.sticker');

