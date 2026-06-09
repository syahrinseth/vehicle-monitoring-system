<?php

namespace App\Filament\Student\Pages;

use App\Models\Student;
use App\Models\User;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Register extends BaseRegister
{
    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('Nama')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email')
            ->email()
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel());
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Password')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
            ->same('passwordConfirmation');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label('Confirm Password')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->dehydrated(false);
    }

    protected function getIcNumberFormComponent(): Component
    {
        return TextInput::make('ic_number')
            ->label('IC no')
            ->required()
            ->maxLength(20);
    }

    protected function getNoNdpFormComponent(): Component
    {
        return TextInput::make('no_ndp')
            ->label('no NDP')
            ->required()
            ->maxLength(50);
    }

    protected function getKosBengkelFormComponent(): Component
    {
        return TextInput::make('kos_bengkel')
            ->label('Kos bengkel')
            ->numeric()
            ->prefix('RM')
            ->required();
    }

    protected function getAddressFormComponent(): Component
    {
        return Textarea::make('address')
            ->label('Alamat pelajar')
            ->rows(4)
            ->required();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
            $this->getIcNumberFormComponent(),
            $this->getNoNdpFormComponent(),
            $this->getKosBengkelFormComponent(),
            $this->getAddressFormComponent(),
        ]);
    }

    protected function handleRegistration(array $data): Model
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'role' => 'student',
            'is_active' => true,
        ]);

        Student::create([
            'user_id' => $user->id,
            'matric_number' => 'STU'.str_pad((string) $user->id, 6, '0', STR_PAD_LEFT),
            'ic_number' => $data['ic_number'],
            'no_ndp' => $data['no_ndp'],
            'kos_bengkel' => $data['kos_bengkel'],
            'address' => $data['address'],
        ]);

        return $user;
    }
}
