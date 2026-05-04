<?php

namespace LamCreator\Installer;

use Illuminate\Support\ServiceProvider;

class LaravelInstallerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            \LamCreator\Installer\InstallCommand::class,
        ]);
    }

    public function boot()
    {
        // Publish views
        $this->publishes([
            __DIR__.'/stubs/views' => resource_path('views/install'),
        ], 'laravel-installer-views');

        // Publish controller
        $this->publishes([
            __DIR__.'/stubs/InstallController.stub' => app_path('Http/Controllers/InstallController.php'),
        ], 'laravel-installer-controller');

        // Publish routes
        $this->publishes([
            __DIR__.'/stubs/install-route.stub' => base_path('routes/install.php'),
        ], 'laravel-installer-routes');
    }
}