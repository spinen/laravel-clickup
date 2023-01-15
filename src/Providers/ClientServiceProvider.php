<?php

namespace Spinen\ClickUp\Providers;

use GuzzleHttp\Client as Guzzle;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Spinen\ClickUp\Api\Client as ClickUp;
use Spinen\ClickUp\Support\Builder;

/**
 * Class ClientServiceProvider
 *
 * Since this is deferred, it only needed to deal with code that has to do with the client.
 */
class ClientServiceProvider extends LaravelServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerClient();

        $this->app->alias(ClickUp::class, 'ClickUp');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Builder::class,
            ClickUp::class,
        ];
    }

    /**
     * Register the client
     *
     * If the ClickUp id or roles are null, then assume sensible values via the API
     */
    protected function registerClient(): void
    {
        $this->app->bind(
            Builder::class,
            function (Application $app) {
                return new Builder($app->make(ClickUp::class));
            }
        );

        $this->app->bind(
            ClickUp::class,
            function (Application $app) {
                return new ClickUp(Config::get('clickup'), $app->make(Guzzle::class));
            }
        );
    }
}
