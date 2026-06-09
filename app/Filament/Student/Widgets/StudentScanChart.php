<?php

namespace App\Filament\Student\Widgets;

use App\Models\CheckInLog;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class StudentScanChart extends ChartWidget
{
    protected ?string $heading = 'Scan History (Last 7 Days)';

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '320px';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $studentId = Auth::user()?->student?->id;
        $end = now()->endOfDay();
        $start = now()->subDays(6)->startOfDay();

        $labels = [];
        $counts = [];

        foreach (CarbonPeriod::create($start, '1 day', $end) as $date) {
            $day = $date->toDateString();
            $labels[] = $date->format('d M');

            if (! $studentId) {
                $counts[] = 0;

                continue;
            }

            $counts[] = CheckInLog::query()
                ->whereDate('scanned_at', $day)
                ->whereHas('vehicle', function ($query) use ($studentId) {
                    $query->where('student_id', $studentId);
                })
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Scans',
                    'data' => $counts,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.18)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getDescription(): ?string
    {
        return 'Daily scans for your registered vehicles.';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
