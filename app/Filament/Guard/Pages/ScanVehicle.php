<?php

namespace App\Filament\Guard\Pages;

use App\Models\CheckInLog;
use App\Models\DigitalSticker;
use App\Models\Vehicle;
use App\Services\QRCodeService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class ScanVehicle extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-qr-code';
    }

    protected string $view = 'filament.guard.pages.scan-vehicle';

    protected static ?string $title = 'Scan Vehicle';

    protected static ?string $navigationLabel = 'Scan / Lookup';

    protected static ?int $navigationSort = 1;

    public ?array $vehicleResult = null;

    public ?string $accessStatus = null; // 'granted' | 'denied'

    public array $data = [];

    public array $plateData = [];

    public function qrForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('qr_token')
                    ->label('QR Code Token')
                    ->placeholder('Scan or paste QR code token here')
                    ->autofocus(),
            ])
            ->statePath('data');
    }

    public function plateForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('plate_number')
                    ->label('Vehicle Plate Number')
                    ->placeholder('e.g. ABC 1234')
                    ->extraInputAttributes(['class' => 'uppercase'])
                    ->dehydrateStateUsing(fn ($state) => strtoupper($state)),
            ])
            ->statePath('plateData');
    }

    protected function getForms(): array
    {
        return ['qrForm', 'plateForm'];
    }

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

    private function processSticker(DigitalSticker $sticker, string $method): void
    {
        $vehicle = $sticker->registration->vehicle;
        $granted = $sticker->isValid();
        $reason = ! $granted ? ($sticker->status === 'revoked' ? 'Sticker revoked' : 'Sticker expired') : null;

        $this->vehicleResult = $this->buildVehicleResult($vehicle, $sticker);
        $this->accessStatus = $granted ? 'granted' : 'denied';

        $this->logCheckIn($vehicle->id, $sticker->id, $method, $granted, $reason);

        $granted
            ? Notification::make()->title('Access Granted')->success()->send()
            : Notification::make()->title('Access Denied: '.$reason)->danger()->send();
    }

    private function buildVehicleResult(Vehicle $vehicle, ?DigitalSticker $sticker): array
    {
        return [
            'plate' => $vehicle->registration_number,
            'type' => $vehicle->vehicleType->name ?? 'N/A',
            'color' => $vehicle->color,
            'manufacturer' => $vehicle->manufacturer,
            'model' => $vehicle->model,
            'student_name' => $vehicle->student->user->name ?? 'N/A',
            'matric' => $vehicle->student->matric_number ?? 'N/A',
            'sticker_status' => $sticker ? ucfirst($sticker->status) : 'No Sticker',
            'valid_until' => $sticker ? $sticker->validity_end_date->format('d M Y') : 'N/A',
        ];
    }

    private function logCheckIn(?int $vehicleId, ?int $stickerId, string $method, bool $granted, ?string $reason): void
    {
        if ($vehicleId) {
            CheckInLog::create([
                'vehicle_id' => $vehicleId,
                'digital_sticker_id' => $stickerId,
                'guard_id' => auth()->id(),
                'scan_method' => $method,
                'access_granted' => $granted,
                'denial_reason' => $reason,
                'scanner_ip' => request()->ip(),
                'scanned_at' => now(),
            ]);
        }
    }

    public function notifyCameraError(): void
    {
        Notification::make()
            ->title('Camera Error')
            ->body('Unable to access camera. Please check your browser permissions.')
            ->danger()
            ->send();
    }

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
}
