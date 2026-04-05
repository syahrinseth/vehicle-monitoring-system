<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Digital Vehicle Sticker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">

        {{-- Status Banner --}}
        @php
            $isValid = $sticker->isValid();
        @endphp

        <div class="rounded-xl p-4 mb-6 text-center {{ $isValid ? 'bg-green-50 border-2 border-green-500' : 'bg-red-50 border-2 border-red-500' }}">
            <p class="text-3xl font-bold {{ $isValid ? 'text-green-700' : 'text-red-700' }}">
                {{ $isValid ? 'LULUS' : 'TIDAK SAH' }}
            </p>
            <p class="text-sm mt-1 {{ $isValid ? 'text-green-600' : 'text-red-600' }}">
                {{ $isValid ? 'Valid Entry Sticker' : 'Sticker ' . ucfirst($sticker->status) }}
            </p>
        </div>

        {{-- QR Code --}}
        @if ($sticker->qr_code_image_path)
            <div class="flex justify-center mb-4">
                <img src="{{ Storage::disk('public')->url($sticker->qr_code_image_path) }}"
                     alt="QR Code"
                     class="w-48 h-48 rounded-lg border border-gray-200" />
            </div>
        @endif

        {{-- Vehicle Info --}}
        <div class="space-y-3">
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500 text-sm">Plate Number</span>
                <span class="font-bold text-lg">{{ $sticker->registration->vehicle->registration_number }}</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500 text-sm">Vehicle Type</span>
                <span class="font-semibold">{{ $sticker->registration->vehicle->vehicleType->name }}</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500 text-sm">Student</span>
                <span class="font-semibold">{{ $sticker->registration->student->user->name }}</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500 text-sm">Matric No.</span>
                <span class="font-semibold">{{ $sticker->registration->student->matric_number }}</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500 text-sm">Valid From</span>
                <span class="font-semibold">{{ $sticker->validity_start_date->format('d M Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500 text-sm">Valid Until</span>
                <span class="font-semibold {{ $isValid ? 'text-green-600' : 'text-red-600' }}">
                    {{ $sticker->validity_end_date->format('d M Y') }}
                </span>
            </div>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            Vehicle Monitoring System &bull; Institut
        </p>
    </div>
</body>
</html>
