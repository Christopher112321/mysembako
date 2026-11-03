<?php

/**
 * Vercel Serverless Function Bridge for Laravel 12
 */

// Setup writable temporary storage directories on Vercel's read-only filesystem
$directories = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Set environment variables for serverless execution
putenv('APP_STORAGE=/tmp/storage');
$_ENV['APP_STORAGE'] = '/tmp/storage';
$_SERVER['APP_STORAGE'] = '/tmp/storage';

putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
$_ENV['APP_CONFIG_CACHE'] = '/tmp/bootstrap/cache/config.php';
$_SERVER['APP_CONFIG_CACHE'] = '/tmp/bootstrap/cache/config.php';

putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
$_ENV['APP_SERVICES_CACHE'] = '/tmp/bootstrap/cache/services.php';
$_SERVER['APP_SERVICES_CACHE'] = '/tmp/bootstrap/cache/services.php';

putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/bootstrap/cache/packages.php';
$_SERVER['APP_PACKAGES_CACHE'] = '/tmp/bootstrap/cache/packages.php';

putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
$_ENV['APP_ROUTES_CACHE'] = '/tmp/bootstrap/cache/routes.php';
$_SERVER['APP_ROUTES_CACHE'] = '/tmp/bootstrap/cache/routes.php';

putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
$_ENV['APP_EVENTS_CACHE'] = '/tmp/bootstrap/cache/events.php';
$_SERVER['APP_EVENTS_CACHE'] = '/tmp/bootstrap/cache/events.php';

putenv('SESSION_DRIVER=cookie');
$_ENV['SESSION_DRIVER'] = 'cookie';
$_SERVER['SESSION_DRIVER'] = 'cookie';

putenv('CACHE_STORE=array');
$_ENV['CACHE_STORE'] = 'array';
$_SERVER['CACHE_STORE'] = 'array';

// Enable debug and fallback APP_KEY
putenv('APP_DEBUG=true');
$_ENV['APP_DEBUG'] = 'true';
$_SERVER['APP_DEBUG'] = 'true';

if (!getenv('APP_KEY')) {
    putenv('APP_KEY=base64:zlnStk5Imh6tDkFvS08A8HLY6VJxXkp3XkOrYviD5hA=');
    $_ENV['APP_KEY'] = 'base64:zlnStk5Imh6tDkFvS08A8HLY6VJxXkp3XkOrYviD5hA=';
    $_SERVER['APP_KEY'] = 'base64:zlnStk5Imh6tDkFvS08A8HLY6VJxXkp3XkOrYviD5hA=';
}

// Fallback DB credentials if not set in Vercel environment
if (!getenv('DB_HOST') || getenv('DB_HOST') === '127.0.0.1') {
    putenv('DB_CONNECTION=mysql');
    $_ENV['DB_CONNECTION'] = 'mysql';
    $_SERVER['DB_CONNECTION'] = 'mysql';

    putenv('DB_HOST=gateway01.ap-northeast-1.prod.aws.tidbcloud.com');
    $_ENV['DB_HOST'] = 'gateway01.ap-northeast-1.prod.aws.tidbcloud.com';
    $_SERVER['DB_HOST'] = 'gateway01.ap-northeast-1.prod.aws.tidbcloud.com';

    putenv('DB_PORT=4000');
    $_ENV['DB_PORT'] = '4000';
    $_SERVER['DB_PORT'] = '4000';

    putenv('DB_DATABASE=mysembako');
    $_ENV['DB_DATABASE'] = 'mysembako';
    $_SERVER['DB_DATABASE'] = 'mysembako';

    putenv('DB_USERNAME=3Jdt6tKbLQzJ6zp.root');
    $_ENV['DB_USERNAME'] = '3Jdt6tKbLQzJ6zp.root';
    $_SERVER['DB_USERNAME'] = '3Jdt6tKbLQzJ6zp.root';

    putenv('DB_PASSWORD=RRpGsiI2M4FDVXMO');
    $_ENV['DB_PASSWORD'] = 'RRpGsiI2M4FDVXMO';
    $_SERVER['DB_PASSWORD'] = 'RRpGsiI2M4FDVXMO';

    putenv('DB_SSL=true');
    $_ENV['DB_SSL'] = 'true';
    $_SERVER['DB_SSL'] = 'true';
}

// Force HTTPS detection for Vercel
$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_PORT'] = 443;
$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';

// Normalize script name so Laravel does not treat /api as baseUrl
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

try {
    // Forward request to standard Laravel public/index.php
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Vercel Serverless Exception Details:</h1>\n";
    echo "<p><b>Error:</b> " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "<p><b>File:</b> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>\n";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>\n";
}
