<?php

/**
 * Vercel Serverless Function Bridge for Laravel 13
 *
 * Handles routing for Vercel's read-only filesystem environment:
 * 1. Creates necessary writable directories inside /tmp
 * 2. Redirects storage, view compilation, and cache paths to /tmp
 * 3. Dispatches the incoming HTTP request to Laravel's public/index.php
 */

$storagePath = '/tmp/storage';
$dirs = [
    $storagePath,
    $storagePath . '/framework',
    $storagePath . '/framework/views',
    $storagePath . '/framework/cache',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/logs',
    $storagePath . '/bootstrap',
    $storagePath . '/bootstrap/cache',
    $storagePath . '/app',
    $storagePath . '/app/public',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Copy pre-compiled bootstrap cache if available
$originalBootstrap = __DIR__ . '/../bootstrap/cache';
if (is_dir($originalBootstrap)) {
    foreach (['packages.php', 'services.php'] as $cacheFile) {
        $source = $originalBootstrap . '/' . $cacheFile;
        $dest = $storagePath . '/bootstrap/cache/' . $cacheFile;
        if (file_exists($source) && !file_exists($dest)) {
            copy($source, $dest);
        }
    }
}

// Redirect Laravel storage path to writable /tmp
putenv('LARAVEL_STORAGE_PATH=' . $storagePath);
$_ENV['LARAVEL_STORAGE_PATH'] = $storagePath;
$_SERVER['LARAVEL_STORAGE_PATH'] = $storagePath;

// Redirect compiled Blade views to /tmp
putenv('VIEW_COMPILED_PATH=' . $storagePath . '/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = $storagePath . '/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = $storagePath . '/framework/views';

// Redirect bootstrap cache files to /tmp
putenv('APP_SERVICES_CACHE=' . $storagePath . '/bootstrap/cache/services.php');
$_ENV['APP_SERVICES_CACHE'] = $storagePath . '/bootstrap/cache/services.php';
$_SERVER['APP_SERVICES_CACHE'] = $storagePath . '/bootstrap/cache/services.php';

putenv('APP_PACKAGES_CACHE=' . $storagePath . '/bootstrap/cache/packages.php');
$_ENV['APP_PACKAGES_CACHE'] = $storagePath . '/bootstrap/cache/packages.php';
$_SERVER['APP_PACKAGES_CACHE'] = $storagePath . '/bootstrap/cache/packages.php';

putenv('APP_CONFIG_CACHE=' . $storagePath . '/bootstrap/cache/config.php');
$_ENV['APP_CONFIG_CACHE'] = $storagePath . '/bootstrap/cache/config.php';
$_SERVER['APP_CONFIG_CACHE'] = $storagePath . '/bootstrap/cache/config.php';

putenv('APP_ROUTES_CACHE=' . $storagePath . '/bootstrap/cache/routes.php');
$_ENV['APP_ROUTES_CACHE'] = $storagePath . '/bootstrap/cache/routes.php';
$_SERVER['APP_ROUTES_CACHE'] = $storagePath . '/bootstrap/cache/routes.php';

putenv('APP_EVENTS_CACHE=' . $storagePath . '/bootstrap/cache/events.php');
$_ENV['APP_EVENTS_CACHE'] = $storagePath . '/bootstrap/cache/events.php';
$_SERVER['APP_EVENTS_CACHE'] = $storagePath . '/bootstrap/cache/events.php';

// Dispatch to standard Laravel entry point
require __DIR__ . '/../public/index.php';
