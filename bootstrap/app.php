<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityAndPerformanceHeaders::class);
        $middleware->redirectTo(
            guests: fn (Request $request) => $request->is('admin*') ? route('admin.login') : route('login'),
            users: fn (Request $request) => $request->user()?->isAdmin() ? route('admin.dashboard') : route('jamaah.dashboard'),
        );
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'jamaah' => \App\Http\Middleware\JamaahMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (\Throwable $e): void {
            $sanitize = function (string $text): string {
                $secrets = [
                    getenv('APP_KEY') ?: null,
                    getenv('DB_PASSWORD') ?: null,
                    getenv('AWS_SECRET_ACCESS_KEY') ?: null,
                    app()->bound('config') ? config('database.connections.mysql.password') : null,
                ];
                foreach (array_filter($secrets) as $secret) {
                    if (is_string($secret) && strlen($secret) >= 4) {
                        $text = str_replace($secret, '[REDACTED]', $text);
                    }
                }
                $text = preg_replace('/(password\s*[:=]\s*)[^\s,;&]+/i', '$1[REDACTED]', $text);
                return $text;
            };

            $diagnostic = sprintf(
                "\n" . str_repeat('=', 70) . "\n" .
                "[VERCEL DIAGNOSTIC ERROR] %s\n" .
                "Message : %s\n" .
                "Location: %s:%d\n" .
                "Trace   :\n%s\n" .
                str_repeat('=', 70) . "\n",
                get_class($e),
                $sanitize($e->getMessage()),
                $e->getFile(),
                $e->getLine(),
                $sanitize($e->getTraceAsString())
            );

            file_put_contents('php://stderr', $diagnostic);
        });

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
