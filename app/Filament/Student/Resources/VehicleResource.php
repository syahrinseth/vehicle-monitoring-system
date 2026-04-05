<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\VehicleResource\Pages;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationLabel = 'My Vehicles';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-truck';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('vehicle_type_id')
                ->label('Vehicle Type')
                ->options(VehicleType::active()->pluck('name', 'id'))
                ->required(),

            TextInput::make('registration_number')
                ->label('Plate Number')
                ->required()
                ->unique(ignoreRecord: true)
                ->extraInputAttributes(['class' => 'uppercase'])
                ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                ->placeholder('e.g. ABC 1234'),

            TextInput::make('color')->required(),
            TextInput::make('manufacturer')->required()->label('Brand'),
            TextInput::make('model')->required(),
            TextInput::make('year')->numeric()->minValue(1990)->maxValue(date('Y') + 1),
            TextInput::make('engine_number')->nullable(),
            TextInput::make('chassis_number')->nullable(),

            FileUpload::make('registration_document_path')
                ->label('Vehicle Registration Document (Grant/Geran)')
                ->disk('public')
                ->directory('documents')
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                ->maxSize(5120)
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registration_number')->label('Plate No.')->searchable()->sortable(),
                TextColumn::make('vehicleType.name')->label('Type'),
                TextColumn::make('color'),
                TextColumn::make('manufacturer')->label('Brand'),
                TextColumn::make('model'),
                TextColumn::make('year'),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $student = auth()->user()->student;

                return $student
                    ? $query->where('student_id', $student->id)
                    : $query->whereNull('id');
            })
            ->actions([EditAction::make(), DeleteAction::make()]);
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
