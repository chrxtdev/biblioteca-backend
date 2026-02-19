<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        App::setLocale('pt_BR');
        Carbon::setLocale('pt_BR');

        \Illuminate\Support\Facades\View::composer('dashboard', \App\View\Composers\DashboardComposer::class);
        // \Illuminate\Support\Facades\View::composer('dashboard.*', \App\View\Composers\DashboardComposer::class);

        // Rate Limiters (Security)
        \Illuminate\Support\Facades\RateLimiter::for('api', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('uploads', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        // Observers
        \App\Models\Book::observe(\App\Observers\BookObserver::class);
    }
}
