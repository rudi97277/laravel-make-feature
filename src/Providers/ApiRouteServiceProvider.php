<?php

namespace Rudi97277\LaravelMakeFeature\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

class ApiRouteServiceProvider extends ServiceProvider
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
        $featuresPath = \app_path('Features');

        if (is_dir($featuresPath)) {
            Route::prefix('api')
                ->group(function () use ($featuresPath) {
                    foreach (File::directories($featuresPath) as $featureDir) {
                        $featureName = basename($featureDir);
                        $routeFile = "{$featureDir}/{$featureName}Route.php";

                        if (File::exists($routeFile)) {
                            require $routeFile;
                        }
                    }
                });
        }
    }
}
