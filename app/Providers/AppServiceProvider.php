<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CertificateService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CertificateService::class, function ($app) {
            return new CertificateService();
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
