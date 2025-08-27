<?php

// Initialize the database with initial schema
require_once __DIR__ . '/bootstrap.php';

echo "Initializing Lib4All database...\n";

// Read and execute the initial schema migration
$migrationFile = __DIR__ . '/migrations/2025-08-27_10-00-00_create_initial_schema.sql';

if (file_exists($migrationFile)) {
    $sql = file_get_contents($migrationFile);
    
    try {
        $db->exec($sql);
        echo "Database initialized successfully!\n";
    } catch (PDOException $e) {
        echo "Error initializing database: " . $e->getMessage() . "\n";
    }
} else {
    echo "Migration file not found: {$migrationFile}\n";
}