<?php

namespace Spinen\ClickUp\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Spinen\ClickUp\Http\Middleware\Filter;

/**
 * Class ServiceProvider
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
        $this->registerMiddleware();

        $this->registerPublishes();

        $this->registerRoutes();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/clickup.php', 'clickup');
    }

    /**
     * Register the middleware
     *
     * If a route needs to have the QuickBooks client, then make sure that the user has linked their account.
     */
    public function registerMiddleware()
    {
        $this->app->router->aliasMiddleware('clickup', Filter::class);
    }

    /**
     * There are several resources that get published
     *
     * Only worry about telling the application about them if running in the console.
     */
    protected function registerPublishes()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

            $this->publishes(
                [
                    __DIR__.'/../../config/clickup.php' => config_path('clickup.php'),
                ],
                'clickup-config'
            );

            $this->publishes(
                [
                    __DIR__.'/../../database/migrations' => database_path('migrations'),
                ],
                'clickup-migrations'
            );
        }
    }

    /**
     * Register the routes needed for the OAuth flow
     */
    protected function registerRoutes()
    {
        if (Config::get('clickup.route.enabled')) {
            Route::group(
                [
                    'namespace' => 'Spinen\ClickUp\Http\Controllers',
                    'middleware' => Config::get('clickup.route.middleware', ['web']),
                ],
                function () {
                    $this->loadRoutesFrom(realpath(__DIR__.'/../../routes/web.php'));
                }
            );
        }
    }
}
