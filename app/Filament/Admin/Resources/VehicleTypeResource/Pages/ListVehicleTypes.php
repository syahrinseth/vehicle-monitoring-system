<?php

namespace App\Filament\Admin\Resources\VehicleTypeResource\Pages;

use App\Filament\Admin\Resources\VehicleTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVehicleTypes extends ListRecords
{
    protected static string $resource = VehicleTypeResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
