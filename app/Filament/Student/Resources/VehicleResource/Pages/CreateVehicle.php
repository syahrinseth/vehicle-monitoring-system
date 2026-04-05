<?php

namespace App\Filament\Student\Resources\VehicleResource\Pages;

use App\Filament\Student\Resources\VehicleResource;
use App\Models\Student;
use Filament\Resources\Pages\CreateRecord;

class CreateVehicle extends CreateRecord
{
    protected static string $resource = VehicleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $student = auth()->user()->student;

        if (! $student) {
            $student = Student::create([
                'user_id'       => auth()->id(),
                'matric_number' => 'STU' . str_pad(auth()->id(), 6, '0', STR_PAD_LEFT),
            ]);
        }

        $data['student_id'] = $student->id;
        return $data;
    }
}
