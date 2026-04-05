# Guard Scan Page — Implementation Plan

## Overview of Issues & Decisions

### Issue 1: Plate number search always shows "Please enter a plate number"
**Root cause:** Filament v5 + Livewire v3 requires all public properties to be
explicitly declared. `$plateData` and `$data` (the statePath arrays for both
forms) are never declared, so Livewire cannot bind user input to them.
`getState()` therefore returns empty arrays.

**Secondary bug:** `clearResult()` resets `$this->plate_number` and `$this->qr_token`
(standalone, unused properties) instead of `$this->plateData` and `$this->data`.

### Issue 2: QR camera scanning
Currently the QR Code section is a plain text input only.

**Plan:** Add a camera-based scanner (html5-qrcode via CDN) that auto-submits
on detection, alongside the existing text input (for hardware barcode scanners).

### Decision: QR code payload → URL instead of JSON
Change the QR image to encode the app sticker URL:
```
https://{APP_URL}/sticker/{token}
```

**Rationale:**
- Any generic phone camera opens the sticker page directly
- The web app camera scanner extracts the token from the URL path
- Backward compat NOT needed (only URL format, no JSON fallback)
- Rear camera facing mode for scanning printed QRs at gates

---

## Files to Modify

### 1. app/Services/QRCodeService.php

**Change:** In `generateForRegistration()`, replace the `$qrData` JSON payload
with the sticker URL.

**Before:**
```php
$qrData = json_encode([
    'token'    => $token,
    'plate'    => $registration->vehicle->registration_number,
    'student'  => $registration->student->matric_number,
    'exp'      => $validityEnd,
]);
```

**After:**
```php
$qrData = url(route('student.sticker', ['token' => $token], absolute: true));
// Encodes the full URL: https://app-domain.com/sticker/{uuid}
```

**Location:** Line 18–23, replace `$qrData` assignment.

---

### 2. app/Filament/Guard/Pages/ScanVehicle.php

**Change A — Declare missing Livewire-bound state properties (CRITICAL FIX):**

Add after line 39 (after `$accessStatus` property):
```php
public array $data = [];
public array $plateData = [];
```

**Why:** Filament v5 + Livewire v3 requires explicit declaration of all properties
that forms will bind to via `statePath()`.

---

**Change B — Add private token extraction helper:**

Add this private method after `clearResult()`:
```php
private function extractToken(string $raw): ?string
{
    $raw = trim($raw);
    
    if (empty($raw)) {
        return null;
    }
    
    // URL format: https://domain.com/sticker/{uuid}
    // Extract the UUID from the path
    if (str_contains($raw, '/sticker/')) {
        $parts = explode('/sticker/', $raw);
        if (isset($parts[1])) {
            return trim(explode('?', $parts[1])[0]); // Remove query params if any
        }
    }
    
    // Raw UUID (from hardware scanner or manual entry)
    return $raw;
}
```

---

**Change C — Fix `scanByQR()` to read from `$this->data` directly:**

Replace lines 71–94 with:
```php
public function scanByQR(): void
{
    $raw = trim($this->data['qr_token'] ?? '');
    $token = $this->extractToken($raw);

    if (! $token) {
        Notification::make()->title('Please enter a QR token')->warning()->send();
        return;
    }

    $sticker = app(QRCodeService::class)->verifyToken($token);

    if (! $sticker) {
        $this->vehicleResult = null;
        $this->accessStatus = 'denied';
        $this->logCheckIn(null, null, 'qr', false, 'QR token not found');
        Notification::make()->title('QR Code not found')->danger()->send();
        return;
    }

    $this->processSticker($sticker, 'qr');
}
```

---

**Change D — Fix `searchByPlate()` to read from `$this->plateData` directly:**

Replace lines 96–131 with:
```php
public function searchByPlate(): void
{
    $plate = strtoupper(trim($this->plateData['plate_number'] ?? ''));

    if (empty($plate)) {
        Notification::make()->title('Please enter a plate number')->warning()->send();
        return;
    }

    $vehicle = Vehicle::where('registration_number', $plate)
        ->with(['vehicleType', 'student.user', 'latestSticker'])
        ->first();

    if (! $vehicle) {
        $this->vehicleResult = null;
        $this->accessStatus = 'denied';
        $this->logCheckIn(null, null, 'plate', false, 'Vehicle not found in system');
        Notification::make()->title('Vehicle not found')->danger()->send();
        return;
    }

    $sticker = $vehicle->latestSticker;

    if (! $sticker) {
        $this->vehicleResult = $this->buildVehicleResult($vehicle, null);
        $this->accessStatus = 'denied';
        $this->logCheckIn($vehicle->id, null, 'plate', false, 'No valid sticker');
        return;
    }

    $this->processSticker($sticker, 'plate');
}
```

---

**Change E — Fix `clearResult()` to reset the actual form state arrays:**

Replace lines 180–186 with:
```php
public function clearResult(): void
{
    $this->vehicleResult = null;
    $this->accessStatus = null;
    $this->data = ['qr_token' => ''];
    $this->plateData = ['plate_number' => ''];
    
    // Sync Filament's internal form state
    $this->qrForm->fill($this->data);
    $this->plateForm->fill($this->plateData);
}
```

---

**Change F — Remove now-unused standalone properties:**

Delete lines 33–35:
```php
public ?string $qr_token = null;
public ?string $plate_number = null;
```

These are no longer needed because the actual state is in `$this->data['qr_token']`
and `$this->plateData['plate_number']`.

---

### 3. resources/views/filament/guard/pages/scan-vehicle.blade.php

**Change A — Update the QR Code section to include camera UI:**

Replace the QR section (lines 5–13) with:
```blade
{{-- QR Code Scanner --}}
<x-filament::section heading="Scan QR Code">
    <div x-data="qrScanner()" class="space-y-4">
        
        {{-- Camera reader container (hidden by default) --}}
        <div id="qr-reader" x-show="cameraActive" class="w-full rounded-lg overflow-hidden border-2 border-gray-300 mb-4"></div>

        {{-- Text input for manual entry or camera result --}}
        <form wire:submit="scanByQR" class="space-y-4">
            {{ $this->qrForm }}
            
            {{-- Camera toggle buttons --}}
            <div class="flex gap-3">
                <x-filament::button 
                    type="button" 
                    @click="startCamera()" 
                    x-show="!cameraActive"
                    icon="heroicon-o-camera" 
                    class="flex-1">
                    Start Camera Scan
                </x-filament::button>
                
                <x-filament::button 
                    type="button" 
                    @click="stopCamera()" 
                    x-show="cameraActive"
                    color="red"
                    icon="heroicon-o-stop" 
                    class="flex-1">
                    Stop Camera
                </x-filament::button>
                
                <x-filament::button 
                    type="submit" 
                    icon="heroicon-o-qr-code" 
                    class="flex-1">
                    Verify QR Code
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament::section>
```

---

**Change B — Add html5-qrcode library and Alpine.js QR scanner logic:**

Add at the end of the file, before the closing `</x-filament-panels::page>` tag:

```blade
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    function qrScanner() {
        return {
            cameraActive: false,
            scanner: null,

            async startCamera() {
                this.cameraActive = true;
                await this.$nextTick();
                
                this.scanner = new Html5Qrcode("qr-reader");
                
                try {
                    await this.scanner.start(
                        { facingMode: "environment" }, // rear camera
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        (decodedText) => this.onQRDetected(decodedText),
                        (error) => { /* ignore errors */ }
                    );
                } catch (err) {
                    console.error('Camera error:', err);
                    Livewire.dispatch('notify', {
                        title: 'Camera Error',
                        message: 'Unable to access camera. Check permissions.',
                        type: 'danger'
                    });
                    this.cameraActive = false;
                }
            },

            async stopCamera() {
                if (this.scanner) {
                    await this.scanner.stop();
                    this.scanner = null;
                }
                this.cameraActive = false;
            },

            onQRDetected(decodedText) {
                const token = this.extractToken(decodedText);
                
                // Stop camera and set the token
                this.stopCamera();
                
                // Update Livewire component state and submit
                @this.set('data.qr_token', token).then(() => {
                    @this.call('scanByQR');
                });
            },

            extractToken(raw) {
                raw = raw.trim();

                // URL format: https://domain.com/sticker/{uuid}
                if (raw.includes('/sticker/')) {
                    const parts = raw.split('/sticker/');
                    if (parts[1]) {
                        return parts[1].split('?')[0].trim();
                    }
                }

                // Raw UUID (from hardware scanner, no extraction needed)
                return raw;
            }
        };
    }
</script>
```

---

## Implementation Order

1. **app/Services/QRCodeService.php** — Update QR payload to URL
2. **app/Filament/Guard/Pages/ScanVehicle.php** — Fix Livewire properties and methods
3. **resources/views/filament/guard/pages/scan-vehicle.blade.php** — Add camera UI and integration

---

## Verification Checklist

After implementation:

- [ ] Plate number search works (no "Please enter a plate number" error with valid input)
- [ ] Clear button resets both fields correctly
- [ ] Camera scanner starts and asks for permission
- [ ] QR code detected from sticker URL format (https://app/sticker/{uuid})
- [ ] Token auto-submits and calls scanByQR()
- [ ] Stop Camera button works
- [ ] Text input still accepts manual entry and hardware scanner input
- [ ] Old stickers (if any) with raw UUID token still work
