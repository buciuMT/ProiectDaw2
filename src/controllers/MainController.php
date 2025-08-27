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
}