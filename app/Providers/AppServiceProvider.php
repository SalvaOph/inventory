<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Services\PdfExportService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(PdfExportService::class, function ($app) {
            return new PdfExportService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        Vite::macro('customAsset', function ($path) {
            $assetPath = Vite::asset($path);

            if (env('APP_ENV') === 'production') {
                return str_replace('/build/', '/', $assetPath);
            }
    
            return $assetPath;
        });
    }
}
