<?php

namespace App\Filament\Student\Resources\VehicleResource\Pages;

use App\Filament\Student\Resources\VehicleResource;
use App\Models\Student;
use App\Models\User;
use App\Models\Vehicle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;

class CreateVehicle extends CreateRecord
{
    protected static string $resource = VehicleResource::class;

    protected function beforeCreate(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        $student = $user?->student;

        if (! $student) {
            return;
        }

        if (Vehicle::where('student_id', $student->id)->count() >= 3) {
            Notification::make()
                ->title('Vehicle limit reached')
                ->body('Each student account can only register up to 3 vehicles.')
                ->danger()
                ->send();

            throw new Halt;
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var User|null $user */
        $user = Auth::user();
        $student = $user?->student;

        if (! $student) {
            $student = Student::create([
                'user_id' => Auth::id(),
                'matric_number' => 'STU'.str_pad((string) Auth::id(), 6, '0', STR_PAD_LEFT),
            ]);
        }

        $data['student_id'] = $student->id;

        return $data;
    }
}
