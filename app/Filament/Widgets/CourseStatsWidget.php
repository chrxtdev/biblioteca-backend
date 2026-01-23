<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use Filament\Widgets\ChartWidget;

class CourseStatsWidget extends ChartWidget
{
    protected static ?string $heading = '🎓 Livros por Curso';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $courses = Book::where('is_verified', true)
            ->selectRaw('course, COUNT(*) as count')
            ->groupBy('course')
            ->orderByDesc('count')
            ->limit(8)
            ->get();

        $colors = [
            'rgba(139, 92, 246, 0.8)',   // violet
            'rgba(59, 130, 246, 0.8)',    // blue
            'rgba(34, 197, 94, 0.8)',     // green
            'rgba(251, 191, 36, 0.8)',    // amber
            'rgba(239, 68, 68, 0.8)',     // red
            'rgba(236, 72, 153, 0.8)',    // pink
            'rgba(20, 184, 166, 0.8)',    // teal
            'rgba(249, 115, 22, 0.8)',    // orange
        ];

        return [
            'datasets' => [
                [
                    'data' => $courses->pluck('count')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $courses->count()),
                    'borderWidth' => 0,
                    'hoverOffset' => 10,
                ],
            ],
            'labels' => $courses->pluck('course')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'cutout' => '60%',
        ];
    }
}
