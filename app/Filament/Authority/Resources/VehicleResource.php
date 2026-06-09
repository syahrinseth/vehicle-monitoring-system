<?php

namespace App\Filament\Authority\Resources;

use App\Filament\Authority\Resources\VehicleResource\Pages;
use App\Models\Vehicle;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationLabel = 'Vehicle Reviews';

    protected static ?int $navigationSort = 2;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-truck';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Vehicle Management';
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
                TextColumn::make('registration_number')
                    ->label('Plate No.')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('student.matric_number')
                    ->label('Matric No.')
                    ->searchable(),
                TextColumn::make('student.user.name')
                    ->label('Student Name')
                    ->searchable(),
                TextColumn::make('review_status')
                    ->badge()
                    ->label('Review')
                    ->color(fn (?string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('payment_receipt_path')
                    ->label('Receipt')
                    ->formatStateUsing(fn (?string $state): string => filled($state) ? 'Open' : '-')
                    ->url(fn (Vehicle $record): ?string => $record->payment_receipt_path
                        ? asset('storage/'.$record->payment_receipt_path)
                        : null)
                    ->openUrlInNewTab(),
                TextColumn::make('reviewedBy.name')
                    ->label('Reviewed By')
                    ->toggleable(),
                TextColumn::make('reviewed_at')
                    ->dateTime()
                    ->toggleable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('review_status')
                    ->label('Review Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Vehicle $record) => $record->isPendingReview())
                    ->action(function (Vehicle $record) {
                        $record->update([
                            'review_status' => 'approved',
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                            'rejection_reason' => null,
                        ]);

                        Notification::make()->title('Vehicle approved')->success()->send();
                    })
                    ->requiresConfirmation(),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Vehicle $record) => $record->isPendingReview())
                    ->form([
                        Textarea::make('rejection_reason')->required()->label('Reason'),
                    ])
                    ->action(function (Vehicle $record, array $data) {
                        $record->update([
                            'review_status' => 'rejected',
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        Notification::make()->title('Vehicle rejected')->warning()->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
        ];
    }
}
