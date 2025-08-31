<?php
// Test script to verify the new reservation features
require_once __DIR__ . '/bootstrap.php';

echo "Testing new reservation features...\n";

// Create a reservation to test the new functionality
$reservationModel = new Reservation($db);

// Test creating a reservation with due date
echo "Creating a test reservation...\n";
$result = $reservationModel->createReservation(1, 1); // User ID 1, Book ID 1

if ($result) {
    echo "Reservation created successfully!\n";
    
    // Get user's borrowed books
    echo "Getting user's borrowed books...\n";
    $borrowedBooks = $reservationModel->getUserBorrowedBooks(1);
    echo "Found " . count($borrowedBooks) . " borrowed books.\n";
    
    // Get user's reservation history
    echo "Getting user's reservation history...\n";
    $history = $reservationModel->getUserReservationHistory(1);
    echo "Found " . count($history) . " history items.\n";
    
    // Get overdue reservations
    echo "Getting overdue reservations...\n";
    $overdue = $reservationModel->getOverdueReservations();
    echo "Found " . count($overdue) . " overdue reservations.\n";
} else {
    echo "Failed to create reservation.\n";
}