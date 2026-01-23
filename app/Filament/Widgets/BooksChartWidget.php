<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class BooksChartWidget extends ChartWidget
{
    protected static ?string $heading = '📈 Livros Enviados por Mês';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $months = collect();
        $approved = collect();
        $pending = collect();
        $rejected = collect();

        // Últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->translatedFormat('M/y'));

            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $approved->push(
                Book::where('is_verified', true)
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count()
            );

            $pending->push(
                Book::where('is_verified', false)
                    ->whereNull('rejection_reason')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count()
            );

            $rejected->push(
                Book::where('is_verified', false)
                    ->whereNotNull('rejection_reason')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count()
            );
        }

        return [
            'datasets' => [
                [
                    'label' => 'Aprovados',
                    'data' => $approved->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'borderRadius' => 8,
                ],
                [
                    'label' => 'Pendentes',
                    'data' => $pending->toArray(),
                    'backgroundColor' => 'rgba(251, 191, 36, 0.8)',
                    'borderColor' => 'rgb(251, 191, 36)',
                    'borderWidth' => 2,
                    'borderRadius' => 8,
                ],
                [
                    'label' => 'Rejeitados',
                    'data' => $rejected->toArray(),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                    'borderRadius' => 8,
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
