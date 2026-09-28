<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

if (env('VERCEL')) {
    $storagePath = sys_get_temp_dir().'/si-pita';

    if (! is_dir($storagePath)) {
        mkdir($storagePath, 0775, true);
    }

    $viewPath = $storagePath.'/framework/views';
    if (! is_dir($viewPath)) {
        mkdir($viewPath, 0775, true);
    }

    $app->useStoragePath($storagePath);
}

return $app;
