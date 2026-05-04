# Lam-creator Laravel Installer

A Laravel package to add a WordPress-style web installer to your Laravel application.

## Requirements

- PHP 8.2+
- Laravel 10.0+ or 11.0+
- A database server (MySQL, PostgreSQL, SQLite, or SQL Server)

## How to Use

### Option 1: Install from Packagist (when published)

Require the package via Composer:

```bash
composer require lam-creator/laravel-installer
```

### Publish to Packagist

1. Push this repository to a public GitHub/GitLab repository.
2. Create a release tag such as `v1.0.0`:

```bash
git tag v1.0.0
git push origin v1.0.0
```

3. Go to https://packagist.org and submit the repository URL.
4. After Packagist imports the package, you can install it normally:

```bash
composer require lam-creator/laravel-installer
```

If Packagist still refuses the package, make sure the tag exists and the package is public.

### Option 2: Local Development

If you're developing locally, add the package as a local repository in your Laravel project's `composer.json`:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "/path/to/laravel-installer"
    }
  ],
  "require": {
    "lam-creator/laravel-installer": "*"
  }
}
```

Then run:

```bash
composer install
```

### Setup the Installer

Run the installer command to add the web installer to your Laravel application:

```bash
php artisan installer:install
```

This will:

- Create an `InstallController` in `app/Http/Controllers/`
- Add installation views in `resources/views/install/`
- Create a routes file at `routes/install.php` (you need to include it in your application)
- Create `bootstrap/installed.php` to track installation status
- Inject middleware to redirect to `/install` if not installed

## Include the Routes

After running the command, include the routes in your `bootstrap/app.php` (Laravel 11) or `app/Providers/RouteServiceProvider.php` (Laravel 10):

For Laravel 11 (`bootstrap/app.php`):

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/install.php'));
        },
    )
    // ...
```

For Laravel 10, in `app/Providers/RouteServiceProvider.php`:

```php
public function boot()
{
    $this->routes(function () {
        Route::middleware('web')
            ->group(base_path('routes/install.php'));
    });
}
```

## Run the app

Start the Laravel server:

```bash
php artisan serve
```

Then open:

```text
http://localhost:8000/install
```

Follow the web installer steps to complete the setup.

```

## Installer flow

1. Visit `/install`
2. Check server requirements and enter database settings
3. Create the application name and admin account
4. Finish installation and access the new site

## Notes

- If you are using SQLite, set `DB_DATABASE` to the SQLite file path.
- If the installer is already marked as installed, it will redirect normal requests away from the installer pages.

## Troubleshooting

- Make sure `composer` is installed and available on your PATH.
- Make sure `git` is installed and available on your PATH.
- If `php artisan serve` fails, verify the generated `.env` and database settings.
```
