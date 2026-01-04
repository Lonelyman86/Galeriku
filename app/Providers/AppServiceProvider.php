<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\Schema::defaultStringLength(191);

        // Pagination
        Paginator::useBootstrapFive();

        //

        // =====================================================
        //  NOTIFIKASI GLOBAL UNTUK SEMUA VIEW
        // =====================================================
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                $view->with(
                    'unreadNotificationsCount',
                    $user->notifications()->whereNull('read_at')->count()
                );

                $view->with(
                    'recentNotifications',
                    $user->notifications()->latest()->take(10)->get()
                );
            }
        });
    }
}
