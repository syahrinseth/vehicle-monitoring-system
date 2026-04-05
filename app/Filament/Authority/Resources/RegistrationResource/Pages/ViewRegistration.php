<?php

namespace App\Filament\Authority\Resources\RegistrationResource\Pages;

use App\Filament\Authority\Resources\RegistrationResource;
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
            TextEntry::make('student.user.email')->label('Email'),
            TextEntry::make('vehicle.registration_number')->label('Plate No.'),
            TextEntry::make('vehicle.vehicleType.name')->label('Vehicle Type'),
            TextEntry::make('vehicle.color')->label('Color'),
            TextEntry::make('vehicle.manufacturer')->label('Brand'),
            TextEntry::make('vehicle.model')->label('Model'),
            TextEntry::make('status')->badge(),
            TextEntry::make('verifiedBy.name')->label('Verified By Admin'),
            TextEntry::make('verified_at')->dateTime(),
            TextEntry::make('rejection_reason')->label('Rejection Reason'),
        ]);
    }
}
