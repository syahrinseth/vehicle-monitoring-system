<?php

namespace App\Filament\Admin\Resources\VehicleTypeResource\Pages;

use App\Filament\Admin\Resources\VehicleTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVehicleType extends EditRecord
{
    protected static string $resource = VehicleTypeResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
