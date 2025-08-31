<?php
// Test script to verify employee functionality
require_once __DIR__ . '/bootstrap.php';

echo "Testing employee functionality...\n";

// Test AuthMiddleware methods
echo "Testing AuthMiddleware::isEmployee()...\n";
// We can't test this directly without a session, but we can check if the method exists
if (method_exists('AuthMiddleware', 'isEmployee')) {
    echo "AuthMiddleware::isEmployee() method exists.\n";
} else {
    echo "AuthMiddleware::isEmployee() method does not exist.\n";
}

// Test EmployeeController class
echo "Testing EmployeeController class...\n";
if (class_exists('EmployeeController')) {
    echo "EmployeeController class exists.\n";
} else {
    echo "EmployeeController class does not exist.\n";
}

echo "All tests completed!\n";