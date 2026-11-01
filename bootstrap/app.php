<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Filesystem\Filesystem;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->redirectGuestsTo(function ($request) {
            return $request->is('api/*') ? null : route('login');
        });

        // Middleware global
        $middleware->web(append: [
            // Tambahkan middleware global untuk web di sini
        ]);

        // Register middleware alias
        $middleware->alias([
            'username.spaces' => \App\Http\Middleware\HandleUsernameSpaces::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
        
        $exceptions->report(function (\Throwable $e) {
            if (env('VERCEL') || isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
                echo "<h1>Original Exception Caught during reporting:</h1>";
                echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Line " . $e->getLine() . ")</p>";
                echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
                exit(1);
            }
        });
    })->create();

/*
|--------------------------------------------------------------------------
| Vercel Serverless Compatibility
|--------------------------------------------------------------------------
|
| Since Vercel has a read-only filesystem, we dynamically relocate Laravel's
| storage path to /tmp/storage and ensure the required directories exist.
| We also override the log channel to stderr to stream logs to Vercel.
|
*/
if (env('VERCEL') || isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    $storagePath = '/tmp/storage';
    $directories = [
        $storagePath,
        $storagePath . '/app',
        $storagePath . '/app/public',
        $storagePath . '/framework',
        $storagePath . '/framework/views',
        $storagePath . '/framework/sessions',
        $storagePath . '/framework/cache',
        $storagePath . '/framework/cache/data',
        $storagePath . '/logs',
        $storagePath . '/bootstrap',
        $storagePath . '/bootstrap/cache',
    ];
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    // Reroute Laravel storage path
    $app->useStoragePath($storagePath);

    // Override PackageManifest to write to writeable /tmp/storage/bootstrap/cache/packages.php
    $app->instance(PackageManifest::class, new PackageManifest(
        new Filesystem, 
        $app->basePath(), 
        $storagePath . '/bootstrap/cache/packages.php'
    ));

    // Redirect other bootstrap cache paths
    putenv('APP_SERVICES_CACHE=' . $storagePath . '/bootstrap/cache/services.php');
    putenv('APP_CONFIG_CACHE=' . $storagePath . '/bootstrap/cache/config.php');
    putenv('APP_ROUTES_CACHE=' . $storagePath . '/bootstrap/cache/routes.php');
    putenv('APP_EVENTS_CACHE=' . $storagePath . '/bootstrap/cache/events.php');

    putenv('LOG_CHANNEL=stderr');
    putenv('APP_DEBUG=true');
    putenv('APP_ENV=local');
}

return $app;
