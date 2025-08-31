<?php
// Test script to verify employee dashboard functionality
require_once __DIR__ . '/bootstrap.php';

echo "Testing employee dashboard functionality...\n";

// Test creating a reservation
$reservationModel = new Reservation($db);
$userModel = new User($db);
$bookModel = new Book($db);

echo "Creating a test reservation...\n";
$result = $reservationModel->createReservation(1, 1); // User ID 1, Book ID 1

if ($result) {
    echo "Reservation created successfully!\n";
} else {
    echo "Failed to create reservation.\n";
}

// Test getting active reservations
echo "Getting active reservations...\n";
$activeReservations = $reservationModel->getActiveReservations();
echo "Found " . count($activeReservations) . " active reservations.\n";

// Test getting overdue reservations
echo "Getting overdue reservations...\n";
$overdueReservations = $reservationModel->getOverdueReservations();
echo "Found " . count($overdueReservations) . " overdue reservations.\n";

// Test getting all users
echo "Getting all users...\n";
$users = $userModel->getAllUsers();
echo "Found " . count($users) . " users.\n";

// Test getting all books
echo "Getting all books...\n";
$books = $bookModel->getAllBooks();
echo "Found " . count($books) . " books.\n";

echo "All tests completed!\n";