<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CertificateService;
use App\Observers\EnrollmentObserver;
use App\Models\Enrollment;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
         $this->app->singleton(CertificateService::class, function ($app) {
            return new CertificateService();
        });
    }

    public function boot(): void
    {
        Enrollment::observe(EnrollmentObserver::class);
    }
}
