<?php

namespace LamCreator\Installer;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'installer:install';

    protected $description = 'Install the web installer for Laravel';

    public function handle()
    {
        $this->info('Installing web installer...');

        // 1. Create InstallController
        $controllerStub = __DIR__.'/stubs/InstallController.stub';
        $controllerPath = app_path('Http/Controllers/InstallController.php');
        if (!File::exists($controllerPath)) {
            File::copy($controllerStub, $controllerPath);
            $this->info('InstallController created.');
        } else {
            $this->warn('InstallController already exists.');
        }

        // 2. Create views
        $viewsDir = resource_path('views/install');
        if (!File::exists($viewsDir)) {
            File::makeDirectory($viewsDir, 0755, true);
        }
        $viewFiles = ['layout.blade.php', 'step1.blade.php', 'step2.blade.php', 'step3.blade.php'];
        foreach ($viewFiles as $view) {
            $viewPath = $viewsDir . '/' . $view;
            if (!File::exists($viewPath)) {
                File::copy(__DIR__.'/stubs/views/' . $view, $viewPath);
                $this->info("View {$view} created.");
            } else {
                $this->warn("View {$view} already exists.");
            }
        }

        // 3. Add routes
        $routesStub = __DIR__.'/stubs/install-route.stub';
        $routesPath = base_path('routes/install.php');
        if (!File::exists($routesPath)) {
            File::copy($routesStub, $routesPath);
            $this->info('Routes file created at routes/install.php');
            $this->warn('Please include routes/install.php in your RouteServiceProvider or bootstrap/app.php');
        } else {
            $this->warn('Routes file already exists.');
        }

        // 4. Create installed flag check
        $installedPath = base_path('bootstrap/installed.php');
        if (!File::exists($installedPath)) {
            File::put($installedPath, "<?php\nreturn false;\n");
            $this->info('Installed flag created.');
        } else {
            $this->warn('Installed flag already exists.');
        }

        // 5. Add middleware to redirect to /install if not installed
        $this->injectMiddleware();

        $this->info('Installation complete!');
        $this->info('Visit /install to start the web installer.');
    }

    private function injectMiddleware(): void
    {
        $kernelPath = app_path('Http/Kernel.php');
        if (!File::exists($kernelPath)) {
            // Laravel 11 uses bootstrap/app.php for middleware
            $this->injectMiddlewareLaravel11();
            return;
        }

        $kernel = File::get($kernelPath);
        $check = "if (!file_exists(base_path('bootstrap/installed.php')) || !require base_path('bootstrap/installed.php')) {";
        if (str_contains($kernel, $check)) {
            $this->warn('Middleware already injected.');
            return;
        }

        $middleware = <<<PHP

        \$request = app('request');
        if (!\$request->is('install*') && (!file_exists(base_path('bootstrap/installed.php')) || !require base_path('bootstrap/installed.php'))) {
            return redirect('/install');
        }
PHP;
        $kernel = str_replace('return $next($request);', $middleware . "\n        return \$next(\$request);", $kernel);
        File::put($kernelPath, $kernel);
        $this->info('Middleware injected into Kernel.php');
    }

    private function injectMiddlewareLaravel11(): void
    {
        $appPath = base_path('bootstrap/app.php');
        if (!File::exists($appPath)) {
            $this->warn('Could not find bootstrap/app.php. Please manually add the middleware.');
            return;
        }

        $content = File::get($appPath);
        if (str_contains($content, 'installed.php')) {
            $this->warn('Middleware already injected.');
            return;
        }

        // This is a simple injection, might need adjustment
        $middleware = <<<PHP
->middleware(function (\$middleware) {
        \$middleware->use(function (\$request, \$next) {
            if (!\$request->is('install*') && (!file_exists(base_path('bootstrap/installed.php')) || !require base_path('bootstrap/installed.php'))) {
                return redirect('/install');
            }
            return \$next(\$request);
        });
    })
PHP;
        // Insert before ->create()
        $content = str_replace('->create()', $middleware . "\n->create()", $content);
        File::put($appPath, $content);
        $this->info('Middleware injected into bootstrap/app.php');
    }
}