<?php

namespace Rudi97277\LaravelMakeFeature;

use Illuminate\Support\ServiceProvider;
use Rudi97277\LaravelMakeFeature\Console\Commands\MakeFeature;
use Rudi97277\LaravelMakeFeature\Providers\RouteServiceProvider;

class LaravelMakeFeatureServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // You can bind services to the container here if needed
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        // Register the "make:feature" command
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeFeature::class,
            ]);
        }
    }
}
