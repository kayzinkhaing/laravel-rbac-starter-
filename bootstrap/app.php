<?php

use App\Http\Middleware\HydrateUserAcl;
use App\Http\Middleware\loggingRequest;
use App\Http\Middleware\ResolveRoleDashboard;
use App\Http\Middleware\ShareActiveRole;
use App\Http\Middleware\XSSProtection;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'xss-protection' => XSSProtection::class,
            'log-request' => loggingRequest::class,
            'acl' => HydrateUserAcl::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'resolve.role.dashboard' => ResolveRoleDashboard::class,
            'share.active.role' => ShareActiveRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
