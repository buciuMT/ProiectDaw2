<?php
// Script to run the loan dates migration
require_once __DIR__ . '/bootstrap.php';

echo "Running migration to add loan dates to reservations...\n";

$migration = new Migration($db);
$result = $migration->run('05_add_loan_dates_to_reservations.sql');

if ($result) {
    echo "Migration completed successfully!\n";
} else {
    echo "Failed to run migration.\n";
}