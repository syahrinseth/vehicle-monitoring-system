<?php

namespace App\Filament\Authority\Widgets;

use App\Models\DigitalSticker;
use App\Models\Registration;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AuthorityStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Awaiting Approval', Registration::where('status', 'verified')->count())
                ->description('Verified, pending your approval')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Approved This Month', Registration::where('status', 'approved')
                    ->whereMonth('updated_at', now()->month)
                    ->whereYear('updated_at', now()->year)
                    ->count())
                ->description('Approvals this month')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Rejected This Month', Registration::where('status', 'rejected')
                    ->whereMonth('updated_at', now()->month)
                    ->whereYear('updated_at', now()->year)
                    ->count())
                ->description('Rejections this month')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Active Stickers', DigitalSticker::where('status', 'valid')->count())
                ->description('Currently valid')
                ->icon('heroicon-o-qr-code')
                ->color('info'),
        ];
    }
}
