<?php
// Simple autoloader for PhpSpreadsheet

function loadPhpSpreadsheetClass($class) {
    // Check if the class belongs to PhpSpreadsheet
    if (strpos($class, 'PhpOffice\\PhpSpreadsheet\\') === 0) {
        // Convert namespace to file path
        $relativePath = str_replace('PhpOffice\\PhpSpreadsheet\\', '', $class);
        $relativePath = str_replace('\\', '/', $relativePath);
        $filePath = __DIR__ . '/../lib/PhpSpreadsheet/src/PhpSpreadsheet/' . $relativePath . '.php';
        
        // Include the file if it exists
        if (file_exists($filePath)) {
            require_once $filePath;
        }
    }
}

// Register the autoloader
spl_autoload_register('loadPhpSpreadsheetClass');