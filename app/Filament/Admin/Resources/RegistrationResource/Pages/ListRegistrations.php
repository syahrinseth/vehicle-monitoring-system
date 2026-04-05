<?php

namespace App\Filament\Admin\Resources\RegistrationResource\Pages;

use App\Filament\Admin\Resources\RegistrationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRegistrations extends ListRecords
{
    protected static string $resource = RegistrationResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
