<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| First we need to get an application instance. This creates an instance
| of the application / container and bootstraps the application so it
| is ready to receive HTTP / Console requests from the environment.
|
*/

$lastVersionFile = __DIR__ . '/.last-version.txt';
$lastVendorVersionFile = __DIR__ . '/.last-vendor-version.txt';

if (file_exists($lastVersionFile)) {
    $requireDir = __DIR__ . '/../../' . trim(file_get_contents($lastVersionFile));
} else {
    $requireDir = __DIR__ . '/..';
}

if (file_exists($lastVendorVersionFile)) {
    define('LAST_VENDOR_VERSION', trim(file_get_contents($lastVersionFile)));
}

$app = require $requireDir . '/bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$app->run();
