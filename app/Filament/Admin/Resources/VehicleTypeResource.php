<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VehicleTypeResource\Pages;
use App\Models\VehicleType;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VehicleTypeResource extends Resource
{
    protected static ?string $model = VehicleType::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-tag';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Configuration';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')->required()->maxLength(100),
            TextInput::make('code')->required()->maxLength(20)->unique(ignoreRecord: true)->extraInputAttributes(['class' => 'uppercase'])->dehydrateStateUsing(fn ($state) => strtoupper($state)),
            Toggle::make('is_active')->label('Active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('code')->badge()->sortable(),
                IconColumn::make('is_active')->boolean()->label('Active'),
                TextColumn::make('vehicles_count')->counts('vehicles')->label('Vehicles'),
            ])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicleTypes::route('/'),
            'create' => Pages\CreateVehicleType::route('/create'),
            'edit' => Pages\EditVehicleType::route('/{record}/edit'),
        ];
    }
}
