<?php

namespace App\Filament\Student\Resources\VehicleResource\Pages;

use App\Filament\Student\Resources\VehicleResource;
use App\Models\Student;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVehicles extends ListRecords
{
    protected static string $resource = VehicleResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
