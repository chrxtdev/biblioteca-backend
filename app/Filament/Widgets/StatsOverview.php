<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        return [
            Stat::make('Total de Livros', Book::count())
                ->description('Todos os livros enviados')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),

            
            Stat::make('Pendentes de Análise', Book::where('is_verified', false)->whereNull('rejection_reason')->count())
                ->description('Aguardando sua revisão')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('Livros Aprovados', Book::where('is_verified', true)->count())
                ->description('Disponíveis no site')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
                
            Stat::make('Livros Rejeitados', Book::where('is_verified', false)->whereNotNull('rejection_reason')->count())
                ->description('Rejeitados pelos revisores')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
        ];
    }
}