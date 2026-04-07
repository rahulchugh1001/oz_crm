<?php

use App\Http\Middleware\CheckActiveUser;
use App\Http\Middleware\CheckAdminRole;
use App\Http\Middleware\CheckAdminOrStockRole;
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
        $middleware->alias([
            'check.active.user' => CheckActiveUser::class,
            'check.admin.role' => CheckAdminRole::class,
            'check.admin.or.stock.role' => CheckAdminOrStockRole::class,
        ]);
        $middleware->redirectUsersTo('/admin/dashboard');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
