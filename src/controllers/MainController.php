<?php

class MainController extends Controller {
    
    public function index() {
        debug_log("MainController::index() called");
        
        // Get top books (for now we'll just get all books)
        $bookModel = new Book($this->db);
        $topBooks = $bookModel->getAllBooks();
        
        debug_log("Retrieved " . count($topBooks) . " books for home page");
        
        $data = [
            'title' => 'Lib4All - Library Management System',
            'books' => $topBooks
        ];
        
        $this->view('home', $data);
    }
    
    public function books() {
        debug_log("MainController::books() called");
        
        $bookModel = new Book($this->db);
        $books = $bookModel->getAllBooks();
        
        debug_log("Retrieved " . count($books) . " books for books page");
        
        $data = [
            'title' => 'All Books - Lib4All',
            'books' => $books
        ];
        
        $this->view('books', $data);
    }
    
    public function users() {
        debug_log("MainController::users() called");
        
        $userModel = new User($this->db);
        $users = $userModel->getAllUsers();
        
        debug_log("Retrieved " . count($users) . " users");
        
        $data = [
            'title' => 'Users - Lib4All',
            'users' => $users
        ];
        
        $this->view('users', $data);
    }
    
    public function myReservations() {
        debug_log("MainController::myReservations() called");
        
        // Check if user is authenticated
        AuthMiddleware::requireAuth();
        
        $reservationModel = new Reservation($this->db);
        $reservations = $reservationModel->getUserActiveReservations($_SESSION['user_id']);
        
        debug_log("Retrieved " . count($reservations) . " active reservations for user " . $_SESSION['user_id']);
        
        $data = [
            'title' => 'My Reservations - Lib4All',
            'reservations' => $reservations
        ];
        
        $this->view('my_reservations', $data);
    }
    
    public function myBooks() {
        debug_log("MainController::myBooks() called");
        
        // Check if user is authenticated
        AuthMiddleware::requireAuth();
        
        $reservationModel = new Reservation($this->db);
        $borrowedBooks = $reservationModel->getUserBorrowedBooks($_SESSION['user_id']);
        
        debug_log("Retrieved " . count($borrowedBooks) . " borrowed books for user " . $_SESSION['user_id']);
        
        $data = [
            'title' => 'My Books - Lib4All',
            'borrowedBooks' => $borrowedBooks
        ];
        
        $this->view('my_books', $data);
    }
    
    public function reservationHistory() {
        debug_log("MainController::reservationHistory() called");
        
        // Check if user is authenticated
        AuthMiddleware::requireAuth();
        
        $reservationModel = new Reservation($this->db);
        $history = $reservationModel->getUserReservationHistory($_SESSION['user_id']);
        
        debug_log("Retrieved " . count($history) . " reservation history items for user " . $_SESSION['user_id']);
        
        $data = [
            'title' => 'Reservation History - Lib4All',
            'history' => $history
        ];
        
        $this->view('reservation_history', $data);
    }
    
    public function cancelReservation() {
        debug_log("MainController::cancelReservation() called");
        
        // Check if user is authenticated
        AuthMiddleware::requireAuth();
        
        $reservationId = $_POST['reservation_id'] ?? null;
        
        if (!$reservationId) {
            $_SESSION['flash_message'] = 'Invalid reservation ID.';
            $_SESSION['flash_type'] = 'danger';
            $this->redirect('/my-reservations');
            return;
        }
        
        $reservationModel = new Reservation($this->db);
        $result = $reservationModel->cancelReservation($reservationId, $_SESSION['user_id']);
        
        if ($result) {
            // We also need to update the book availability
            $query = "SELECT book_id FROM reservations WHERE id = :reservation_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':reservation_id', $reservationId);
            $stmt->execute();
            $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($reservation) {
                $bookModel = new Book($this->db);
                $book = $bookModel->getBookById($reservation['book_id']);
                if ($book) {
                    $bookModel->updateBook($reservation['book_id'], [
                        'copies_available' => $book['copies_available'] + 1
                    ]);
                }
            }
            
            $_SESSION['flash_message'] = 'Reservation cancelled successfully.';
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Failed to cancel reservation.';
            $_SESSION['flash_type'] = 'danger';
        }
        
        $this->redirect('/my-reservations');
    }
}