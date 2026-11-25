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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\TrackUniqueVisitors::class);
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'api.token' => \App\Http\Middleware\ApiTokenAuthMiddleware::class,
            'api.security' => \App\Http\Middleware\ApiSecurityHeadersMiddleware::class,
            'api.botblock' => \App\Http\Middleware\BlockMaliciousBotsMiddleware::class,
            'api.sanitize' => \App\Http\Middleware\SanitizeApiInputMiddleware::class,
            'verify.webhook' => \App\Http\Middleware\VerifyWebhookSignatureMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
