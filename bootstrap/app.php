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
    ->withMiddleware(function (Middleware $middleware) {
        // Enregistrement manuel des alias pour le package Spatie Permission
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

    $middleware->web(append: [ // Ou $middleware->append() pour global
        \App\Http\Middleware\ShareSiteSettings::class,
        ]);

        // Vous pouvez ajouter d'autres configurations de middleware ici si nécessaire
        // Par exemple, pour les groupes de middleware globaux ou web :
        // $middleware->web(append: [
        //     \App\Http\Middleware\AnotherWebMiddleware::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();