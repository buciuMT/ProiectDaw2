<?php

class Reservation {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createReservation($userId, $bookId, $setDueDate = true) {
        // Check if the user already has an active reservation for this book
        $checkQuery = "SELECT id FROM reservations WHERE user_id = :user_id AND book_id = :book_id AND status = 'active'";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':user_id', $userId);
        $checkStmt->bindParam(':book_id', $bookId);
        $checkStmt->execute();

        if ($checkStmt->fetch()) {
            return false; // Already reserved
        }

        if ($setDueDate) {
            // Get the loan period from the book
            $bookQuery = "SELECT loan_period FROM books WHERE id = :book_id";
            $bookStmt = $this->db->prepare($bookQuery);
            $bookStmt->bindParam(':book_id', $bookId);
            $bookStmt->execute();
            $book = $bookStmt->fetch(PDO::FETCH_ASSOC);
            
            // Calculate due date (loan period from today)
            $loanPeriod = $book['loan_period'] ?? 14; // Default to 14 days
            $dueDate = date('Y-m-d', strtotime("+$loanPeriod days"));

            // Create the reservation with due date
            $query = "INSERT INTO reservations (user_id, book_id, due_date) VALUES (:user_id, :book_id, :due_date)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':book_id', $bookId);
            $stmt->bindParam(':due_date', $dueDate);
        } else {
            // Create the reservation without due date
            $query = "INSERT INTO reservations (user_id, book_id) VALUES (:user_id, :book_id)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':book_id', $bookId);
        }
        
        return $stmt->execute();
    }

    public function getUserReservations($userId) {
        $query = "SELECT r.*, b.title, b.author, b.cover_image 
                  FROM reservations r 
                  JOIN books b ON r.book_id = b.id 
                  WHERE r.user_id = :user_id 
                  ORDER BY r.reserved_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserActiveReservations($userId) {
        $query = "SELECT r.*, b.title, b.author, b.cover_image 
                  FROM reservations r 
                  JOIN books b ON r.book_id = b.id 
                  WHERE r.user_id = :user_id AND r.status = 'active'
                  ORDER BY r.reserved_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserBorrowedBooks($userId) {
        $query = "SELECT r.*, b.title, b.author, b.cover_image,
                  CASE 
                    WHEN r.due_date < CURDATE() THEN 'overdue'
                    ELSE 'on-time'
                  END as status_indicator
                  FROM reservations r 
                  JOIN books b ON r.book_id = b.id 
                  WHERE r.user_id = :user_id AND r.status = 'active' AND r.due_date IS NOT NULL
                  ORDER BY r.due_date ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserReservationHistory($userId) {
        $query = "SELECT r.*, b.title, b.author, b.cover_image,
                  CASE 
                    WHEN r.status = 'completed' THEN 'returned'
                    WHEN r.status = 'cancelled' THEN 'cancelled'
                    ELSE 'active'
                  END as status_text
                  FROM reservations r 
                  JOIN books b ON r.book_id = b.id 
                  WHERE r.user_id = :user_id
                  ORDER BY r.reserved_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cancelReservation($reservationId, $userId = null) {
        // First check if the reservation is already cancelled
        $checkQuery = "SELECT status FROM reservations WHERE id = :reservation_id";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':reservation_id', $reservationId);
        $checkStmt->execute();
        $reservation = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reservation) {
            return false; // Reservation not found
        }
        
        if ($reservation['status'] === 'cancelled') {
            return true; // Already cancelled, nothing to do
        }
        
        // Proceed with cancellation
        if ($userId !== null) {
            $query = "UPDATE reservations 
                      SET status = 'cancelled' 
                      WHERE id = :reservation_id AND user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':reservation_id', $reservationId);
            $stmt->bindParam(':user_id', $userId);
        } else {
            $query = "UPDATE reservations 
                      SET status = 'cancelled' 
                      WHERE id = :reservation_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':reservation_id', $reservationId);
        }
        
        return $stmt->execute();
    }

    public function returnBook($reservationId, $userId) {
        $query = "UPDATE reservations 
                  SET status = 'completed', returned_at = CURRENT_TIMESTAMP
                  WHERE id = :reservation_id AND user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':reservation_id', $reservationId);
        $stmt->bindParam(':user_id', $userId);
        
        return $stmt->execute();
    }

    public function getBookReservations($bookId) {
        $query = "SELECT r.*, u.name as user_name 
                  FROM reservations r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.book_id = :book_id AND r.status = 'active'
                  ORDER BY r.reserved_at ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':book_id', $bookId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isBookReservedByUser($bookId, $userId) {
        $query = "SELECT id FROM reservations 
                  WHERE book_id = :book_id AND user_id = :user_id AND status = 'active'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':book_id', $bookId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetch() !== false;
    }
    
    public function getAllReservations() {
        $query = "SELECT r.*, b.title as book_title, u.name as user_name
                  FROM reservations r
                  JOIN books b ON r.book_id = b.id
                  JOIN users u ON r.user_id = u.id
                  ORDER BY r.reserved_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getActiveReservations() {
        $query = "SELECT r.*, b.title as book_title, u.name as user_name
                  FROM reservations r
                  JOIN books b ON r.book_id = b.id
                  JOIN users u ON r.user_id = u.id
                  WHERE r.status = 'active'
                  ORDER BY r.reserved_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReservationById($reservationId) {
        $query = "SELECT * FROM reservations WHERE id = :reservation_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':reservation_id', $reservationId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getOverdueReservations() {
        $query = "SELECT r.*, b.title as book_title, u.name as user_name, b.loan_period
                  FROM reservations r
                  JOIN books b ON r.book_id = b.id
                  JOIN users u ON r.user_id = u.id
                  WHERE r.status = 'active' AND r.due_date < CURDATE()
                  ORDER BY r.due_date ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}