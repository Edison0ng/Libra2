<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
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
    ];
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    $app->useStoragePath($storagePath);
    putenv('LOG_CHANNEL=stderr');
}

return $app;
