<?php

require_once __DIR__ . '/bootstrap.php';

if ($argc < 2) {
    echo "Usage: php migrate.php <filename>\n";
    echo "Or: php migrate.php create <name>\n";
    exit(1);
}

$migration = new Migration($db);

if ($argv[1] === 'create' && isset($argv[2])) {
    $migration->create($argv[2]);
} else {
    $migration->run($argv[1]);
}