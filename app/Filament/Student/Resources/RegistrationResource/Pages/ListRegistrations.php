<?php

namespace App\Filament\Student\Resources\RegistrationResource\Pages;

use App\Filament\Student\Resources\RegistrationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRegistrations extends ListRecords
{
    protected static string $resource = RegistrationResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
