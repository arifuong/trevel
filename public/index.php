<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Early emergency logger to capture fatal errors before Laravel logging initializes
$emergencyLogger = function (\Throwable $e): void {
    $sanitize = function (string $text): string {
        $secrets = [
            getenv('APP_KEY') ?: null,
            getenv('DB_PASSWORD') ?: null,
            getenv('AWS_SECRET_ACCESS_KEY') ?: null,
        ];
        foreach (array_filter($secrets) as $secret) {
            if (is_string($secret) && strlen($secret) >= 4) {
                $text = str_replace($secret, '[REDACTED]', $text);
            }
        }
        $text = preg_replace('/(password\s*[:=]\s*)[^\s,;&]+/i', '$1[REDACTED]', $text);
        return $text;
    };

    $msg = sprintf(
        "\n" . str_repeat('=', 70) . "\n" .
        "[EARLY BOOTSTRAP ERROR] %s\n" .
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

    file_put_contents('php://stderr', $msg);
};

set_exception_handler(function (\Throwable $e) use ($emergencyLogger): void {
    $emergencyLogger($e);
    throw $e;
});

register_shutdown_function(function () use ($emergencyLogger): void {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $msg = sprintf(
            "\n" . str_repeat('=', 70) . "\n" .
            "[EARLY FATAL SHUTDOWN] %s in %s:%d\n" .
            str_repeat('=', 70) . "\n",
            $error['message'],
            $error['file'],
            $error['line']
        );
        file_put_contents('php://stderr', $msg);
    }
});

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
try {
    /** @var Application $app */
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    $emergencyLogger($e);
    throw $e;
}
