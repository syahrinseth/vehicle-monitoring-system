<?php

namespace App\Filament\Student\Resources\RegistrationResource\Pages;

use App\Filament\Student\Resources\RegistrationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRegistration extends CreateRecord
{
    protected static string $resource = RegistrationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $student = auth()->user()->student;
        $data['student_id']   = $student->id;
        $data['status']       = 'pending';
        $data['submitted_at'] = now();
        return $data;
    }
}
