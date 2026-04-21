<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

/*
 * Default php.ini memory_limit is often 128M. Large multipart requests (several admin photos
 * at once) can exceed that while Symfony builds the request — before any controller runs.
 * Raise the limit here so Request::capture() and image processing have headroom.
 */
@ini_set('memory_limit', '512M');

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
