<?php

// Start session
session_start();

// Load environment variables
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos($line, '#') === 0) {
            continue;
        }
        
        // Parse key=value
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            $value = trim($value, '"\'');
            
            // Set environment variable
            $_ENV[$key] = $value;
        }
    }
}

// Load environment variables from .env file
loadEnv(__DIR__ . '/../.env');

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
require_once __DIR__ . '/helpers/visit_helper.php';
require_once __DIR__ . '/helpers/statistics_helper.php';

// Initialize database
$database = new Database();
$db = $database->connect();

// Log bootstrap completion
debug_log("Bootstrap completed");