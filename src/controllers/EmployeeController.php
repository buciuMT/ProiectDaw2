<?php

class EmployeeController extends Controller {
    
    public function dashboard() {
        debug_log("EmployeeController::dashboard() called");
        
        // Check if user is employee (librarian) or admin
        AuthMiddleware::requireEmployee();
        
        $reservationModel = new Reservation($this->db);
        $userModel = new User($this->db);
        $bookModel = new Book($this->db);
        
        // Get search parameters
        $userId = $_GET['user_id'] ?? '';
        $bookId = $_GET['book_id'] ?? '';
        
        // Get active reservations based on search
        if (!empty($userId) || !empty($bookId)) {
            $query = "SELECT r.*, b.title as book_title, u.name as user_name
                      FROM reservations r
                      JOIN books b ON r.book_id = b.id
                      JOIN users u ON r.user_id = u.id
                      WHERE r.status = 'active'";
            
            $params = [];
            
            if (!empty($userId)) {
                $query .= " AND r.user_id = :user_id";
                $params[':user_id'] = $userId;
            }
            
            if (!empty($bookId)) {
                $query .= " AND r.book_id = :book_id";
                $params[':book_id'] = $bookId;
            }
            
            $query .= " ORDER BY r.reserved_at DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $activeReservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Get all active reservations
            $activeReservations = $reservationModel->getActiveReservations();
        }
        
        // Get overdue reservations
        $overdueReservations = $reservationModel->getOverdueReservations();
        
        // Get all users for the search dropdown
        $users = $userModel->getAllUsers();
        
        // Get all books for the search dropdown
        $books = $bookModel->getAllBooks();
        
        $data = [
            'title' => 'Employee Dashboard - Lib4All',
            'activeReservations' => $activeReservations,
            'overdueReservations' => $overdueReservations,
            'users' => $users,
            'books' => $books
        ];
        
        $this->view('employee/dashboard', $data);
    }
    
    public function manageReservation() {
        debug_log("EmployeeController::manageReservation() called");
        
        // Check if user is employee (librarian) or admin
        AuthMiddleware::requireEmployee();
        
        $action = $_POST['action'] ?? '';
        $reservationId = $_POST['reservation_id'] ?? '';
        
        if (empty($action) || empty($reservationId)) {
            $_SESSION['flash_message'] = 'Invalid request.';
            $_SESSION['flash_type'] = 'danger';
            $this->redirect('/employee/dashboard');
            return;
        }
        
        $reservationModel = new Reservation($this->db);
        $bookModel = new Book($this->db);
        
        // Get reservation details
        $query = "SELECT r.*, b.title as book_title, b.copies_total, b.copies_available 
                  FROM reservations r 
                  JOIN books b ON r.book_id = b.id 
                  WHERE r.id = :reservation_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':reservation_id', $reservationId);
        $stmt->execute();
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reservation) {
            $_SESSION['flash_message'] = 'Reservation not found.';
            $_SESSION['flash_type'] = 'danger';
            $this->redirect('/employee/dashboard');
            return;
        }
        
        $message = '';
        $success = false;
        
        switch ($action) {
            case 'approve':
                // For approval, we might want to set a due date if not already set
                $query = "UPDATE reservations SET due_date = DATE_ADD(CURDATE(), INTERVAL 14 DAY) 
                          WHERE id = :reservation_id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':reservation_id', $reservationId);
                $stmt->execute();
                
                $message = 'Reservation approved and due date set.';
                $success = true;
                break;
                
            case 'cancel':
                // First check if the reservation is already cancelled
                $checkQuery = "SELECT status FROM reservations WHERE id = :reservation_id";
                $checkStmt = $this->db->prepare($checkQuery);
                $checkStmt->bindParam(':reservation_id', $reservationId);
                $checkStmt->execute();
                $reservationStatus = $checkStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($reservationStatus && $reservationStatus['status'] === 'cancelled') {
                    $message = 'Reservation is already cancelled.';
                } else {
                    // Cancel the reservation
                    $result = $reservationModel->cancelReservation($reservationId);
                    
                    if ($result) {
                        // Increase book availability
                        $newAvailable = $reservation['copies_available'] + 1;
                        $bookModel->updateBook($reservation['book_id'], [
                            'copies_available' => $newAvailable
                        ]);
                        
                        $message = 'Reservation cancelled and book availability updated.';
                        $success = true;
                    } else {
                        $message = 'Failed to cancel reservation.';
                    }
                }
                break;
                
            case 'report_missing':
                // First check if the reservation is already cancelled
                $checkQuery = "SELECT status FROM reservations WHERE id = :reservation_id";
                $checkStmt = $this->db->prepare($checkQuery);
                $checkStmt->bindParam(':reservation_id', $reservationId);
                $checkStmt->execute();
                $reservationStatus = $checkStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($reservationStatus && $reservationStatus['status'] === 'cancelled') {
                    $message = 'Reservation is already cancelled.';
                    break;
                }
                
                // Mark book as missing - decrease total copies
                $newTotal = max(0, $reservation['copies_total'] - 1);
                $newAvailable = max(0, $reservation['copies_available'] - 1);
                
                $bookModel->updateBook($reservation['book_id'], [
                    'copies_total' => $newTotal,
                    'copies_available' => $newAvailable
                ]);
                
                // Also cancel the reservation
                $reservationModel->cancelReservation($reservationId);
                
                $message = 'Book marked as missing. Total copies decreased.';
                $success = true;
                break;
                
            case 'mark_returned':
                // First check if the reservation is already completed
                $checkQuery = "SELECT status FROM reservations WHERE id = :reservation_id";
                $checkStmt = $this->db->prepare($checkQuery);
                $checkStmt->bindParam(':reservation_id', $reservationId);
                $checkStmt->execute();
                $reservationStatus = $checkStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($reservationStatus && $reservationStatus['status'] === 'completed') {
                    $message = 'Book is already marked as returned.';
                } else {
                    // Mark book as returned
                    $query = "UPDATE reservations 
                              SET status = 'completed', returned_at = CURRENT_TIMESTAMP 
                              WHERE id = :reservation_id";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':reservation_id', $reservationId);
                    $stmt->execute();
                    
                    // Increase book availability
                    $newAvailable = $reservation['copies_available'] + 1;
                    $bookModel->updateBook($reservation['book_id'], [
                        'copies_available' => $newAvailable
                    ]);
                    
                    $message = 'Book marked as returned and availability updated.';
                    $success = true;
                }
                break;
                
            default:
                $message = 'Invalid action.';
                break;
        }
        
        if ($success) {
            $_SESSION['flash_message'] = $message;
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = $message;
            $_SESSION['flash_type'] = 'danger';
        }
        
        $this->redirect('/employee/dashboard');
    }
}