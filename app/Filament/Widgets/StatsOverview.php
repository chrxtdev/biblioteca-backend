<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Dados para os gráficos sparkline dos últimos 7 dias
        $last7Days = collect(range(6, 0))->map(function ($daysAgo) {
            return Book::whereDate('created_at', Carbon::today()->subDays($daysAgo))->count();
        })->toArray();

        $approvedLast7Days = collect(range(6, 0))->map(function ($daysAgo) {
            return Book::where('is_verified', true)
                ->whereDate('updated_at', Carbon::today()->subDays($daysAgo))
                ->count();
        })->toArray();

        $pendingCount = Book::where('is_verified', false)->whereNull('rejection_reason')->count();
        $approvedCount = Book::where('is_verified', true)->count();
        $rejectedCount = Book::where('is_verified', false)->whereNotNull('rejection_reason')->count();
        $totalCount = Book::count();

        // Calcular taxa de aprovação
        $approvalRate = $totalCount > 0 ? round(($approvedCount / $totalCount) * 100) : 0;

        // URLs para os filtros
        $booksUrl = route('filament.admin.resources.books.index');
        $pendingUrl = $booksUrl . '?tableFilters[status][value]=pending';
        $approvedUrl = $booksUrl . '?tableFilters[status][value]=approved';
        $rejectedUrl = $booksUrl . '?tableFilters[status][value]=rejected';

        return [
            Stat::make('📚 Total de Livros', $totalCount)
                ->description('Todos os livros no sistema')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info')
                ->chart($last7Days)
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:scale-[1.02] transition-transform ring-2 ring-sky-500/20',
                    'wire:click' => "redirectToUrl('{$booksUrl}')",
                    'onclick' => "window.location.href='{$booksUrl}'",
                ]),

            Stat::make('⏳ Pendentes', $pendingCount)
                ->description($pendingCount > 0 ? 'Aguardando sua revisão!' : 'Tudo em dia! ✨')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingCount > 0 ? 'warning' : 'success')
                ->chart([7, 3, 4, 5, 6, 3, $pendingCount])
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:scale-[1.02] transition-transform ' . ($pendingCount > 0 ? 'ring-2 ring-amber-500/30 animate-pulse' : ''),
                    'onclick' => "window.location.href='{$pendingUrl}'",
                ]),

            Stat::make('✅ Aprovados', $approvedCount)
                ->description("Taxa: {$approvalRate}% aprovados")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart($approvedLast7Days)
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:scale-[1.02] transition-transform ring-2 ring-emerald-500/20',
                    'onclick' => "window.location.href='{$approvedUrl}'",
                ]),
                
            Stat::make('❌ Rejeitados', $rejectedCount)
                ->description('Precisam de correção')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:scale-[1.02] transition-transform ring-2 ring-rose-500/20',
                    'onclick' => "window.location.href='{$rejectedUrl}'",
                ]),
        ];
    }
}