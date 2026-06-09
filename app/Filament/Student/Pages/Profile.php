<?php

namespace App\Filament\Student\Pages;

use App\Models\User;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class Profile extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Profile';

    protected static ?string $title = 'My Profile';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.student.pages.profile';

    public array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        $student = $user?->student;

        $this->data = [
            'name' => $user?->name,
            'ic_number' => $student?->ic_number,
            'no_ndp' => $student?->no_ndp,
            'address' => $student?->address,
            'kos_bengkel' => $student?->kos_bengkel,
        ];

        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label('Nama')
                ->required(),

            TextInput::make('ic_number')
                ->label('IC no')
                ->required(),

            TextInput::make('no_ndp')
                ->label('no NDP')
                ->required(),

            TextInput::make('kos_bengkel')
                ->label('Kos bengkel')
                ->numeric()
                ->prefix('RM')
                ->required(),

            Textarea::make('address')
                ->label('Alamat pelajar')
                ->rows(4)
                ->required(),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        /** @var User|null $user */
        $user = Auth::user();
        $student = $user?->student;

        if (! $user || ! $student) {
            Notification::make()->title('Student profile not found')->danger()->send();

            return;
        }

        $user->update([
            'name' => $data['name'],
        ]);

        $student->update([
            'ic_number' => $data['ic_number'],
            'no_ndp' => $data['no_ndp'],
            'address' => $data['address'],
            'kos_bengkel' => $data['kos_bengkel'],
        ]);

        Notification::make()->title('Profile updated')->success()->send();
    }
}
