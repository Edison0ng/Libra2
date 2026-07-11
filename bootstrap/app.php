<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware global
        $middleware->web(append: [
            // Tambahkan middleware global untuk web di sini
        ]);

        // Register middleware alias
        $middleware->alias([
            'username.spaces' => \App\Http\Middleware\HandleUsernameSpaces::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();