<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SunatService;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SunatService::class, function ($app) {
            return new SunatService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
