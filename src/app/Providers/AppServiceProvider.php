<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;

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
        Vite::prefetch(concurrency: 3);

        Model::preventLazyLoading(!app()->isProduction());

        // Load domain migrations
        $domainsPath = app_path('Domains');

        foreach (File::directories($domainsPath) as $domainPath) {
            $migrationPath = $domainPath . '/Migrations';

            if (File::isDirectory($migrationPath)) {
                $this->loadMigrationsFrom($migrationPath);
            }
        }
    }
}
