<?php

namespace App\Filament\Admin\Widgets;

use App\Models\CheckInLog;
use App\Models\DigitalSticker;
use App\Models\Registration;
use App\Models\Student;
use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Students', Student::count())
                ->description('Registered students')
                ->icon('heroicon-o-academic-cap')
                ->color('info'),

            Stat::make('Total Vehicles', Vehicle::count())
                ->description('Vehicles in system')
                ->icon('heroicon-o-truck')
                ->color('success'),

            Stat::make('Pending Registrations', Registration::where('status', 'pending')->count())
                ->description('Awaiting verification')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Active Stickers', DigitalSticker::where('status', 'valid')->count())
                ->description('Valid digital stickers')
                ->icon('heroicon-o-qr-code')
                ->color('success'),

            Stat::make('Today\'s Check-ins', CheckInLog::whereDate('scanned_at', today())->count())
                ->description('Scans today')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('info'),

            Stat::make('Access Denied Today', CheckInLog::whereDate('scanned_at', today())->where('access_granted', false)->count())
                ->description('Denied entries today')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
