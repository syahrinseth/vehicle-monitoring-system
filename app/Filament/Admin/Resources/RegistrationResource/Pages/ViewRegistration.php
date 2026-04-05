<?php

namespace App\Filament\Admin\Resources\RegistrationResource\Pages;

use App\Filament\Admin\Resources\RegistrationResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;

class ViewRegistration extends ViewRecord
{
    protected static string $resource = RegistrationResource::class;

    public function infolist(Schema $infolist): Schema
    {
        return $infolist->schema([
            TextEntry::make('student.matric_number')->label('Matric No.'),
            TextEntry::make('student.user.name')->label('Student Name'),
            TextEntry::make('vehicle.registration_number')->label('Plate No.'),
            TextEntry::make('vehicle.vehicleType.name')->label('Vehicle Type'),
            TextEntry::make('status')->badge(),
            TextEntry::make('submitted_at')->dateTime(),
            TextEntry::make('verifiedBy.name')->label('Verified By'),
            TextEntry::make('verified_at')->dateTime(),
            TextEntry::make('approvedBy.name')->label('Approved By'),
            TextEntry::make('approved_at')->dateTime(),
            TextEntry::make('rejection_reason')->label('Rejection Reason'),
        ]);
    }
}
