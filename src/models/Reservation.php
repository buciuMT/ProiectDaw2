<?php

class Reservation {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createReservation($userId, $bookId) {
        // Check if the user already has an active reservation for this book
        $checkQuery = "SELECT id FROM reservations WHERE user_id = :user_id AND book_id = :book_id AND status = 'active'";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':user_id', $userId);
        $checkStmt->bindParam(':book_id', $bookId);
        $checkStmt->execute();

        if ($checkStmt->fetch()) {
            return false; // Already reserved
        }

        // Create the reservation
        $query = "INSERT INTO reservations (user_id, book_id) VALUES (:user_id, :book_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':book_id', $bookId);
        
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

    public function cancelReservation($reservationId, $userId) {
        $query = "UPDATE reservations 
                  SET status = 'cancelled' 
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
}