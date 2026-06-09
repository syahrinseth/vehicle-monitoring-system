<x-filament-panels::page>
        <form wire:submit="save" class="space-y-6">
            {{ $this->form }}

        <div class="h-6"></div>

        <div>
            <x-filament::button type="submit">
                Save Profile
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
