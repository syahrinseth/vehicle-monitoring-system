<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CheckInLogResource\Pages;
use App\Models\CheckInLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CheckInLogResource extends Resource
{
    protected static ?string $model = CheckInLog::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-clipboard-document-list';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Reports';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle.registration_number')->label('Plate No.')->searchable()->sortable(),
                TextColumn::make('vehicle.student.user.name')->label('Student')->searchable(),
                TextColumn::make('guardUser.name')->label('Guard')->searchable(),
                TextColumn::make('scan_method')->badge()->label('Method'),
                IconColumn::make('access_granted')->boolean()->label('Access Granted'),
                TextColumn::make('denial_reason')->label('Denial Reason')->toggleable()->wrap(),
                TextColumn::make('scanned_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('access_granted')
                    ->label('Access')
                    ->options([1 => 'Granted', 0 => 'Denied']),
                SelectFilter::make('scan_method')
                    ->options(['qr' => 'QR Code', 'plate' => 'Plate Number']),
            ])
            ->defaultSort('scanned_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCheckInLogs::route('/'),
        ];
    }
}
