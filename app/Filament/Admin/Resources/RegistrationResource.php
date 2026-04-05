<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RegistrationResource\Pages;
use App\Models\Registration;
use App\Services\QRCodeService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-document-check';
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

            Select::make('vehicle_id')
                ->relationship('vehicle', 'registration_number')
                ->searchable()
                ->preload()
                ->required(),

            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'verified' => 'Verified',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->required(),

            Textarea::make('rejection_reason')
                ->label('Rejection Reason')
                ->nullable()
                ->visible(fn ($get) => $get('status') === 'rejected'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('student.matric_number')->label('Matric No.')->searchable()->sortable(),
                TextColumn::make('student.user.name')->label('Student Name')->searchable(),
                TextColumn::make('vehicle.registration_number')->label('Plate No.')->searchable(),
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
                TextColumn::make('submitted_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'verified' => 'Verified',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ]),
            ])
            ->actions([
                ViewAction::make(),

                Action::make('verify')
                    ->label('Verify')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->visible(fn (Registration $record) => $record->status === 'pending')
                    ->action(function (Registration $record) {
                        $record->update([
                            'status' => 'verified',
                            'verified_by' => auth()->id(),
                            'verified_at' => now(),
                        ]);
                        Notification::make()->title('Registration verified')->success()->send();
                    })
                    ->requiresConfirmation(),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Registration $record) => in_array($record->status, ['pending', 'verified']))
                    ->form([
                        Textarea::make('rejection_reason')->required()->label('Reason for rejection'),
                    ])
                    ->action(function (Registration $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'rejected_at' => now(),
                        ]);
                        Notification::make()->title('Registration rejected')->warning()->send();
                    }),

                Action::make('generate_sticker')
                    ->label('Generate Sticker')
                    ->icon('heroicon-o-qr-code')
                    ->color('success')
                    ->visible(fn (Registration $record) => $record->status === 'approved' && ! $record->digitalSticker)
                    ->form([
                        DatePicker::make('validity_start_date')->required()->default(now()->toDateString()),
                        DatePicker::make('validity_end_date')->required()->default(now()->addYear()->toDateString()),
                    ])
                    ->action(function (Registration $record, array $data) {
                        app(QRCodeService::class)->generateForRegistration(
                            $record,
                            $data['validity_start_date'],
                            $data['validity_end_date']
                        );
                        Notification::make()->title('Digital sticker generated')->success()->send();
                    }),

                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrations::route('/'),
            'create' => Pages\CreateRegistration::route('/create'),
            'edit' => Pages\EditRegistration::route('/{record}/edit'),
            'view' => Pages\ViewRegistration::route('/{record}'),
        ];
    }
}
