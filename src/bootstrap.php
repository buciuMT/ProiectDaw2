<?php

// Start session
session_start();

// Include debugging configuration
require_once __DIR__ . '/config/debug.php';

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/config/',
        __DIR__ . '/core/',
        __DIR__ . '/controllers/',
        __DIR__ . '/models/',
        __DIR__ . '/middleware/',
        __DIR__ . '/helpers/',
        __DIR__ . '/services/',
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Include helpers
require_once __DIR__ . '/helpers/view_helper.php';

// Initialize database
$database = new Database();
$db = $database->connect();

// Log bootstrap completion
debug_log("Bootstrap completed");