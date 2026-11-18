<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\CheckForMaintenanceEnv::class);

        $middleware->alias([
            'admin' => \App\Http\Middleware\Admin::class,
            'phase2' => \App\Http\Middleware\EnsurePhase2Enabled::class,
            'preventBackHistory' => \App\Http\Middleware\PreventBackHistory::class,
        ]);

        // Where the "guest" middleware sends users who are already logged in.
        $middleware->redirectUsersTo(fn () => route('admin.dashboard.index', absolute: false));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
