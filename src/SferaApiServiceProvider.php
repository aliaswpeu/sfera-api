<?php

namespace Aliaswpeu\SferaApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SferaApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::prefix('api')
            ->as('api.')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
            });


        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/sfera-api.php' => config_path('sfera-api.php'),
            ], 'sfera-api-config');
        }

    }
}
