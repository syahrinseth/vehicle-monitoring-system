<?php

namespace App\Filament\Authority\Resources;

use App\Filament\Authority\Resources\RegistrationResource\Pages;
use App\Models\Registration;
use App\Services\QRCodeService;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
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

    protected static ?string $navigationLabel = 'Registrations';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-document-check';
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
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('student.matric_number')->label('Matric No.')->searchable(),
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
                TextColumn::make('verifiedBy.name')->label('Verified By')->toggleable(),
                TextColumn::make('verified_at')->dateTime()->sortable()->toggleable(),
                TextColumn::make('submitted_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'verified' => 'Verified (Pending Approval)',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ]),
            ])
            ->actions([
                ViewAction::make(),

                Action::make('approve')
                    ->label('Approve & Issue Sticker')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Registration $record) => $record->status === 'verified')
                    ->form([
                        DatePicker::make('validity_start_date')
                            ->required()
                            ->default(now()->toDateString())
                            ->label('Sticker Valid From'),
                        DatePicker::make('validity_end_date')
                            ->required()
                            ->default(now()->addYear()->toDateString())
                            ->label('Sticker Valid Until'),
                    ])
                    ->action(function (Registration $record, array $data) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);

                        app(QRCodeService::class)->generateForRegistration(
                            $record,
                            $data['validity_start_date'],
                            $data['validity_end_date']
                        );

                        Notification::make()
                            ->title('Registration approved and sticker issued')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Registration $record) => in_array($record->status, ['pending', 'verified']))
                    ->form([
                        Textarea::make('rejection_reason')->required()->label('Reason'),
                    ])
                    ->action(function (Registration $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'rejected_at' => now(),
                        ]);
                        Notification::make()->title('Registration rejected')->warning()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrations::route('/'),
            'view' => Pages\ViewRegistration::route('/{record}'),
        ];
    }
}
