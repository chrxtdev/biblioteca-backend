<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class WelcomeWidget extends Widget
{
    protected static string $view = 'filament.widgets.welcome-widget';
    protected static ?int $sort = 0;
    protected int | string | array $columnSpan = 'full';

    public function getGreeting(): string
    {
        $hour = Carbon::now()->hour;
        
        if ($hour < 12) {
            return 'Bom dia';
        } elseif ($hour < 18) {
            return 'Boa tarde';
        } else {
            return 'Boa noite';
        }
    }

    public function getUserName(): string
    {
        return Auth::user()->name ?? 'Administrador';
    }

    public function getCurrentDate(): string
    {
        return Carbon::now()->translatedFormat('l, d \d\e F \d\e Y');
    }
}
