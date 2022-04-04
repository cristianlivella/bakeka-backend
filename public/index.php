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

$requireDir = (function() {
    $getFileContent = function($path) use (&$getFileContent) {
        $isOldFile = substr($path, -4) !== '.old';

        if (file_exists($path) && ($content = trim(file_get_contents($path))) !== '') {
            if (!$isOldFile) {
                file_put_contents($path . '.old', $content);
            }

            return $content;
        }

        if ($isOldFile) {
            return $getFileContent($path . '.old');
        }

        return null;
    };

    $exitWithStatusCode = function($statusCode) {
        http_response_code($statusCode);
        exit;
    };

    $lastVersionFile = __DIR__ . '/.last-version.txt';
    $lastVendorVersionFile = __DIR__ . '/.last-vendor-version.txt';

    if ($lastVersion = $getFileContent($lastVersionFile)) {
        $requireDir = __DIR__ . '/deploys/' . $lastVersion;
    } else {
        $requireDir = __DIR__ . '/..';
    }

    // TODO: get key from .env
    if (isset($_GET['copy_vendor']) && isset($_GET['new_version']) && isset($_GET['key'])) {
        $key = $_GET['key'];
        $newVersion = substr(preg_replace("/[^0-9a-f]+/", "", $_GET['new_version']), 0, 10);

        if (!($lastVendorVersion = $getFileContent($lastVendorVersionFile))) {
            $exitWithStatusCode(500);
        }

        // TODO: use real key (from .env)
        if ($key !== 'key') {
            $exitWithStatusCode(401);
        }

        if (!is_dir(__DIR__ . '/deploys/' . $newVersion)) {
            $exitWithStatusCode(400);
        }

        $sourceDir = __DIR__ . '/deploys/' . $lastVendorVersion . '/vendor';
        $destinationDir = __DIR__ . '/deploys/' . $newVersion . '/vendor';

        mkdir($destinationDir, 0755);

        foreach (
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourceDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                mkdir($destinationDir . '/' . $iterator->getSubPathname());
            } else {
                copy($item, $destinationDir . '/' . $iterator->getSubPathname());
            }
        }

        $exitWithStatusCode(200);
    }

    return $requireDir;
})();

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
