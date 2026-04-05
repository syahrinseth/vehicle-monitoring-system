<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DigitalStickerResource\Pages;
use App\Models\DigitalSticker;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DigitalStickerResource extends Resource
{
    protected static ?string $model = DigitalSticker::class;

    protected static ?int $navigationSort = 3;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-qr-code';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Vehicle Management';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('registration_id')
                ->relationship('registration', 'id')
                ->required(),
            DatePicker::make('validity_start_date')->required(),
            DatePicker::make('validity_end_date')->required(),
            Select::make('status')->options([
                'valid' => 'Valid',
                'expired' => 'Expired',
                'revoked' => 'Revoked',
            ])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registration.vehicle.registration_number')->label('Plate No.')->searchable(),
                TextColumn::make('registration.student.matric_number')->label('Matric No.')->searchable(),
                TextColumn::make('registration.student.user.name')->label('Student')->searchable(),
                TextColumn::make('validity_start_date')->date()->sortable(),
                TextColumn::make('validity_end_date')->date()->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'valid' => 'success',
                        'expired' => 'warning',
                        'revoked' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('generated_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'valid' => 'Valid',
                    'expired' => 'Expired',
                    'revoked' => 'Revoked',
                ]),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('revoke')
                    ->label('Revoke')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (DigitalSticker $record) => $record->status === 'valid')
                    ->action(fn (DigitalSticker $record) => $record->update(['status' => 'revoked']))
                    ->requiresConfirmation(),
                Action::make('download_qr')
                    ->label('Download QR')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (DigitalSticker $record) => $record->qr_code_image_path
                        ? Storage::disk('public')->url($record->qr_code_image_path)
                        : null)
                    ->openUrlInNewTab()
                    ->visible(fn (DigitalSticker $record) => (bool) $record->qr_code_image_path),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDigitalStickers::route('/'),
            'view' => Pages\ViewDigitalSticker::route('/{record}'),
        ];
    }
}
