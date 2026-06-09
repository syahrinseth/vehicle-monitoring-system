<?php

namespace App\Filament\Student\Resources\RegistrationResource\Pages;

use App\Filament\Student\Resources\RegistrationResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateRegistration extends CreateRecord
{
    protected static string $resource = RegistrationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var User|null $user */
        $user = Auth::user();
        $student = $user?->student;

        if ($user && $student) {
            $user->update([
                'name' => $data['student_name'],
            ]);

            $student->update([
                'ic_number' => $data['ic_number'],
                'no_ndp' => $data['no_ndp'],
                'address' => $data['address'],
                'kos_bengkel' => $data['kos_bengkel'],
            ]);

            $data['student_id'] = $student->id;
        }

        unset($data['student_name'], $data['ic_number'], $data['no_ndp'], $data['address'], $data['kos_bengkel']);

        $data['status'] = 'pending';
        $data['submitted_at'] = now();

        return $data;
    }
}
