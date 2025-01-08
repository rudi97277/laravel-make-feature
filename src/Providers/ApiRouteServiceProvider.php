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
        $featuresPath = app_path('Features');

        if (is_dir($featuresPath)) {
            Route::prefix('api')
                ->group(function () use ($featuresPath) {
                    $this->loadFeatureRoutes($featuresPath);
                });
        }
    }

    /**
     * Recursively load feature routes.
     *
     * @param string $directory
     * @return void
     */
    protected function loadFeatureRoutes(string $directory): void
    {
        foreach (File::directories($directory) as $subDir) {
            $routeFile = "{$subDir}/" . basename($subDir) . "Route.php";

            if (File::exists($routeFile)) {
                require $routeFile;
            }

            $this->loadFeatureRoutes($subDir);
        }
    }
}
