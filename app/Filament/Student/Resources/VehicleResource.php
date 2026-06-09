<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\VehicleResource\Pages;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationLabel = "My Vehicles";

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return "heroicon-o-truck";
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make("vehicle_type_id")
                ->label("Vehicle Type")
                ->options(VehicleType::active()->pluck("name", "id"))
                ->required(),

            TextInput::make("registration_number")
                ->label("Plate Number")
                ->required()
                ->unique(ignoreRecord: true)
                ->extraInputAttributes(["class" => "uppercase"])
                ->dehydrateStateUsing(fn($state) => strtoupper($state))
                ->placeholder("e.g. ABC 1234"),

            TextInput::make("color")->required(),
            TextInput::make("model")->required(),

            FileUpload::make("payment_receipt_path")
                ->label("Payment Receipt")
                ->disk("public")
                ->directory("receipts")
                ->acceptedFileTypes([
                    "image/jpeg",
                    "image/png",
                    "application/pdf",
                ])
                ->maxSize(5120)
                ->required(
                    fn(string $operation): bool => $operation === "create",
                ),

            Placeholder::make("payment_note")
                ->label("Payment Note")
                ->content(
                    new HtmlString(
                        sprintf(
                            '<div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900/40 dark:bg-amber-950/30 dark:text-amber-200">' .
                                '<p class="font-semibold">Sticker per car registration is RM%s.</p>' .
                                '<p class="mt-2">Bank: %s</p>' .
                                "<p>Account Name: %s</p>" .
                                "<p>Account Number: %s</p>" .
                                "</div>",
                            config("vms.sticker_fee", 5),
                            e(config("vms.bank_name", "N/A")),
                            e(config("vms.bank_account_name", "N/A")),
                            e(config("vms.bank_account_number", "N/A")),
                        ),
                    ),
                )
                ->visibleOn("create")
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("registration_number")
                    ->label("Plate No.")
                    ->searchable()
                    ->sortable(),
                TextColumn::make("vehicleType.name")->label("Type"),
                TextColumn::make("color"),
                TextColumn::make("model"),
                TextColumn::make("review_status")
                    ->badge()
                    ->label("Review")
                    ->color(
                        fn(?string $state): string => match ($state) {
                            "approved" => "success",
                            "rejected" => "danger",
                            default => "warning",
                        },
                    ),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $student = Auth::user()?->student;

                return $student
                    ? $query->where("student_id", $student->id)
                    : $query->whereNull("id");
            })
            ->actions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListVehicles::route("/"),
            "create" => Pages\CreateVehicle::route("/create"),
            "edit" => Pages\EditVehicle::route("/{record}/edit"),
        ];
    }
}
