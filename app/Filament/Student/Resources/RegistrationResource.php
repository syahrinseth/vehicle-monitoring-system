<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\RegistrationResource\Pages;
use App\Models\Registration;
use App\Models\Vehicle;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?string $navigationLabel = 'My Registration';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-document-text';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('vehicle_id')
                ->label('Select Vehicle')
                ->options(function () {
                    $student = auth()->user()->student;

                    return $student
                        ? Vehicle::where('student_id', $student->id)->pluck('registration_number', 'id')
                        : [];
                })
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle.registration_number')->label('Plate No.'),
                TextColumn::make('vehicle.vehicleType.name')->label('Vehicle Type'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'verified' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('digitalSticker.validity_end_date')
                    ->label('Sticker Expires')
                    ->date(),
                TextColumn::make('digitalSticker.status')
                    ->label('Sticker Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'valid' => 'success',
                        'expired' => 'warning',
                        'revoked' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('submitted_at')->dateTime()->sortable(),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $student = auth()->user()->student;

                return $student
                    ? $query->where('student_id', $student->id)
                    : $query->whereNull('id');
            })
            ->actions([
                Action::make('view_sticker')
                    ->label('View Sticker')
                    ->icon('heroicon-o-qr-code')
                    ->color('success')
                    ->visible(fn (Registration $record) => $record->digitalSticker?->status === 'valid')
                    ->url(fn (Registration $record) => route('student.sticker', $record->digitalSticker->qr_code_token))
                    ->openUrlInNewTab(),

                Action::make('download_qr')
                    ->label('Download QR')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->visible(fn (Registration $record) => (bool) $record->digitalSticker?->qr_code_image_path)
                    ->action(function (Registration $record) {
                        $record->digitalSticker->update(['downloaded_at' => now()]);
                    })
                    ->url(fn (Registration $record) => $record->digitalSticker?->qr_code_image_path
                        ? Storage::disk('public')->url($record->digitalSticker->qr_code_image_path)
                        : null)
                    ->openUrlInNewTab(),

                Action::make('renew')
                    ->label('Request Renewal')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (Registration $record) => $record->digitalSticker?->status === 'expired')
                    ->action(function (Registration $record) {
                        Registration::create([
                            'student_id' => $record->student_id,
                            'vehicle_id' => $record->vehicle_id,
                            'status' => 'pending',
                            'submitted_at' => now(),
                        ]);
                        Notification::make()->title('Renewal request submitted')->success()->send();
                    })
                    ->requiresConfirmation(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrations::route('/'),
            'create' => Pages\CreateRegistration::route('/create'),
        ];
    }
}
