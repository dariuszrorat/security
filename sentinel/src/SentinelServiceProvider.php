<?php

namespace Security\Sentinel;

use Illuminate\Support\ServiceProvider;

class SentinelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'sentinel');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/routes.php';
        $this->app->make('Security\Sentinel\SentinelController');

        $this->mergeConfigFrom(
            __DIR__.'/config/sentinel.php', 'sentinel'
        );
    }
}
