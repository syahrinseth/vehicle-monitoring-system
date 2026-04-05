<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VehicleResource\Pages;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-truck';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Vehicle Management';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('student_id')
                ->relationship('student', 'matric_number')
                ->searchable()
                ->preload()
                ->required(),

            Select::make('vehicle_type_id')
                ->label('Vehicle Type')
                ->options(VehicleType::active()->pluck('name', 'id'))
                ->required(),

            TextInput::make('registration_number')
                ->label('Plate Number')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(20)
                ->extraInputAttributes(['class' => 'uppercase'])
                ->dehydrateStateUsing(fn ($state) => strtoupper($state)),

            TextInput::make('manufacturer')->required()->maxLength(100),
            TextInput::make('model')->required()->maxLength(100),
            TextInput::make('color')->required()->maxLength(50),

            TextInput::make('year')
                ->numeric()
                ->required()
                ->minValue(1990)
                ->maxValue(now()->year + 1),

            TextInput::make('engine_number')->maxLength(100),
            TextInput::make('chassis_number')->maxLength(100),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registration_number')
                    ->label('Plate No.')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('vehicleType.name')
                    ->label('Type')
                    ->badge(),
                TextColumn::make('manufacturer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('model')->searchable(),
                TextColumn::make('color'),
                TextColumn::make('year')->sortable(),
                TextColumn::make('student.matric_number')
                    ->label('Matric No.')
                    ->searchable(),
                TextColumn::make('student.user.name')
                    ->label('Student Name')
                    ->searchable(),
                TextColumn::make('registrations_count')
                    ->counts('registrations')
                    ->label('Registrations'),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('vehicle_type_id')
                    ->label('Vehicle Type')
                    ->relationship('vehicleType', 'name'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
