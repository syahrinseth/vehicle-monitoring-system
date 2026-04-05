<?php

namespace App\Filament\Guard\Resources;

use App\Filament\Guard\Resources\CheckInLogResource\Pages;
use App\Models\CheckInLog;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CheckInLogResource extends Resource
{
    protected static ?string $model = CheckInLog::class;
    protected static ?string $navigationLabel = 'Scan History';

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-clipboard-document-list'; }

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
                TextColumn::make('vehicle.student.user.name')->label('Student'),
                TextColumn::make('scan_method')->badge()->label('Method'),
                IconColumn::make('access_granted')->boolean()->label('Granted'),
                TextColumn::make('denial_reason')->label('Reason')->toggleable(),
                TextColumn::make('scanned_at')->dateTime()->sortable(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('guard_id', auth()->id()))
            ->defaultSort('scanned_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCheckInLogs::route('/'),
        ];
    }
}
