<?php

namespace App\Filament\Admin\Resources\DigitalStickerResource\Pages;

use App\Filament\Admin\Resources\DigitalStickerResource;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;

class ViewDigitalSticker extends ViewRecord
{
    protected static string $resource = DigitalStickerResource::class;

    public function infolist(Schema $infolist): Schema
    {
        return $infolist->schema([
            TextEntry::make('registration.vehicle.registration_number')->label('Plate No.'),
            TextEntry::make('registration.student.user.name')->label('Student'),
            TextEntry::make('qr_code_token')->label('QR Token')->copyable(),
            TextEntry::make('validity_start_date')->date(),
            TextEntry::make('validity_end_date')->date(),
            TextEntry::make('status')->badge(),
            ImageEntry::make('qr_code_image_path')
                ->disk('public')
                ->label('QR Code')
                ->height(200),
            TextEntry::make('generated_at')->dateTime(),
            TextEntry::make('downloaded_at')->dateTime(),
        ]);
    }
}
