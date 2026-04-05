<?php

namespace App\Filament\Student\Resources\VehicleResource\Pages;

use App\Filament\Student\Resources\VehicleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVehicle extends EditRecord
{
    protected static string $resource = VehicleResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
