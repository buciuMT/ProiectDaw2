<?php
// Test script to verify reservation cancellation
require_once __DIR__ . '/bootstrap.php';

echo "Testing reservation cancellation...\n";

$reservationModel = new Reservation($db);

// Test getting a reservation by ID
echo "Getting reservation by ID...\n";
$reservation = $reservationModel->getReservationById(6);
if ($reservation) {
    echo "Reservation found: ID " . $reservation['id'] . ", Status: " . $reservation['status'] . "\n";
} else {
    echo "Reservation not found.\n";
}

// Test cancelling a reservation
echo "Attempting to cancel reservation...\n";
$result = $reservationModel->cancelReservation(6);
if ($result) {
    echo "Reservation cancelled successfully.\n";
} else {
    echo "Failed to cancel reservation.\n";
}

// Check the reservation status again
echo "Checking reservation status after cancellation...\n";
$reservation = $reservationModel->getReservationById(6);
if ($reservation) {
    echo "Reservation status: " . $reservation['status'] . "\n";
} else {
    echo "Reservation not found.\n";
}