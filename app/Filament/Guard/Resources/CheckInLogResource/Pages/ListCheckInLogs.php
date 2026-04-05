<?php

namespace App\Filament\Guard\Resources\CheckInLogResource\Pages;

use App\Filament\Guard\Resources\CheckInLogResource;
use Filament\Resources\Pages\ListRecords;

class ListCheckInLogs extends ListRecords
{
    protected static string $resource = CheckInLogResource::class;
}
