<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StudentResource\Pages;
use App\Models\Student;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-academic-cap';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'User Management';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('user_id')
                ->label('User Account')
                ->options(
                    User::where('role', 'student')
                        ->orderBy('name')
                        ->pluck('name', 'id')
                )
                ->searchable()
                ->required(),

            TextInput::make('matric_number')
                ->label('Matric Number')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(20)
                ->extraInputAttributes(['class' => 'uppercase'])
                ->dehydrateStateUsing(fn ($state) => strtoupper($state)),

            TextInput::make('ic_number')
                ->label('IC Number')
                ->maxLength(20),

            TextInput::make('phone')
                ->tel()
                ->maxLength(20),

            Select::make('gender')
                ->options(['male' => 'Male', 'female' => 'Female'])
                ->required(),

            DatePicker::make('date_of_birth')
                ->label('Date of Birth'),

            Textarea::make('address')
                ->rows(3)
                ->columnSpanFull(),

            TextInput::make('emergency_contact')
                ->label('Emergency Contact')
                ->maxLength(20),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('matric_number')
                    ->label('Matric No.')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('ic_number')
                    ->label('IC No.')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')->searchable()->toggleable(),
                TextColumn::make('gender')->badge(),
                TextColumn::make('vehicles_count')
                    ->counts('vehicles')
                    ->label('Vehicles'),
                TextColumn::make('registrations_count')
                    ->counts('registrations')
                    ->label('Registrations'),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('gender')
                    ->options(['male' => 'Male', 'female' => 'Female']),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('matric_number');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
