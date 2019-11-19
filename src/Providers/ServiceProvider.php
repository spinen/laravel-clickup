<?php

namespace Spinen\ClickUp\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

/**
 * Class ServiceProvider
 *
 * @package Spinen\ClickUp\Providers
 */
class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPublishes();

        // $this->registerRoutes();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/clickup.php', 'clickup');
    }

    /**
     * There are several resources that get published
     *
     * Only worry about telling the application about them if running in the console.
     */
    protected function registerPublishes()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

            $this->publishes(
                [
                    __DIR__ . '/../../config/clickup.php' => config_path('clickup.php'),
                ],
                'clickup-config'
            );

            $this->publishes(
                [
                    __DIR__ . '/../../database/migrations' => database_path('migrations'),
                ],
                'clickup-migrations'
            );
        }
    }
}
