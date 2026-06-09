<?php

namespace App\Filament\Student\Widgets;

use App\Models\DigitalSticker;
use App\Models\Registration;
use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StudentStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $studentId = Auth::user()?->student?->id;

        if (! $studentId) {
            return [
                Stat::make('My Vehicles', 0)
                    ->description('No student profile linked')
                    ->icon('heroicon-o-truck')
                    ->color('gray'),

                Stat::make('My Registrations', 0)
                    ->description('No student profile linked')
                    ->icon('heroicon-o-document-text')
                    ->color('gray'),

                Stat::make('Pending Registrations', 0)
                    ->description('Waiting for review')
                    ->icon('heroicon-o-clock')
                    ->color('warning'),

                Stat::make('Active Stickers', 0)
                    ->description('Valid QR stickers')
                    ->icon('heroicon-o-qr-code')
                    ->color('success'),
            ];
        }

        return [
            Stat::make('My Vehicles', Vehicle::where('student_id', $studentId)->count())
                ->description('Vehicles registered under my account')
                ->icon('heroicon-o-truck')
                ->color('info'),

            Stat::make('My Registrations', Registration::where('student_id', $studentId)->count())
                ->description('All submitted applications')
                ->icon('heroicon-o-document-text')
                ->color('primary'),

            Stat::make('Pending Registrations', Registration::where('student_id', $studentId)->where('status', 'pending')->count())
                ->description('Still waiting for approval')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Active Stickers', DigitalSticker::whereHas('registration', function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            })->where('status', 'valid')->count())
                ->description('Valid QR stickers')
                ->icon('heroicon-o-qr-code')
                ->color('success'),
        ];
    }
}
