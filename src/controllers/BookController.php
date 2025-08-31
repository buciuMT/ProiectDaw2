<?php

class BookController extends Controller {
    
    public function index() {
        debug_log("BookController::index() called");
        
        $bookModel = new Book($this->db);
        $books = $bookModel->getAllBooks();
        
        debug_log("Retrieved " . count($books) . " books");
        
        $data = [
            'title' => 'All Books - Lib4All',
            'books' => $books
        ];
        
        $this->view('books', $data);
    }
    
    public function show($id) {
        debug_log("BookController::show() called with id: " . $id);
        
        $bookModel = new Book($this->db);
        $book = $bookModel->getBookById($id);
        
        debug_log("Book lookup result", $book);
        
        if (!$book) {
            debug_log("Book not found, showing 404 page");
            
            // Show 404 page
            $data = [
                'title' => 'Book Not Found - Lib4All'
            ];
            $this->view('404', $data);
            return;
        }
        
        // Check if user is logged in and if they have reserved this book
        $isReserved = false;
        if (isset($_SESSION['user_id'])) {
            debug_log("Checking reservation status for user: " . $_SESSION['user_id']);
            
            $reservationModel = new Reservation($this->db);
            $isReserved = $reservationModel->isBookReservedByUser($id, $_SESSION['user_id']);
            
            debug_log("Reservation status: " . ($isReserved ? 'reserved' : 'not reserved'));
        }
        
        $data = [
            'title' => $book['title'] . ' - Lib4All',
            'book' => $book,
            'isReserved' => $isReserved
        ];
        
        $this->view('book_detail', $data);
    }
    
    public function create() {
        debug_log("BookController::create() called");
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        $data = [
            'title' => 'Add New Book - Lib4All'
        ];
        
        $this->view('book_form', $data);
    }
    
    public function scrape() {
        debug_log("BookController::scrape() called");
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        $data = [
            'title' => 'Scrape Book - Lib4All'
        ];
        
        $this->view('book_scrape', $data);
    }
    
    public function scrapeStore() {
        debug_log("BookController::scrapeStore() called");
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        $url = $_POST['url'] ?? '';
        
        if (empty($url)) {
            $_SESSION['flash_message'] = 'Please provide a URL to scrape.';
            $_SESSION['flash_type'] = 'danger';
            $this->redirect('/books/scrape');
            return;
        }
        
        // Include the BookScraperService
        require_once __DIR__ . '/../services/BookScraperService.php';
        
        // Create scraper instance
        $scraper = new BookScraperService();
        
        // Scrape book data
        $bookData = $scraper->scrapeBookFromUrl($url);
        
        if ($bookData === false) {
            $_SESSION['flash_message'] = 'Failed to scrape book information from the provided URL.';
            $_SESSION['flash_type'] = 'danger';
            $this->redirect('/books/scrape');
            return;
        }
        
        // Add book to library
        $bookId = $scraper->addBookToLibrary($bookData, $this->db);
        
        if ($bookId === false) {
            $_SESSION['flash_message'] = 'Failed to add scraped book to the library.';
            $_SESSION['flash_type'] = 'danger';
            $this->redirect('/books/scrape');
            return;
        }
        
        // Set success message
        $_SESSION['flash_message'] = 'Book successfully scraped and added to the library!';
        $_SESSION['flash_type'] = 'success';
        
        debug_log("Book scraped and added successfully, redirecting to books list");
        
        // Redirect to books list
        $this->redirect('/books');
    }
    
    public function store() {
        debug_log("BookController::store() called");
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        $title = $_POST['title'] ?? '';
        $author = $_POST['author'] ?? '';
        $isbn = $_POST['isbn'] ?? '';
        $publishedYear = $_POST['published_year'] ?? '';
        $genre = $_POST['genre'] ?? '';
        $copiesTotal = $_POST['copies_total'] ?? 1;
        $copiesAvailable = $_POST['copies_available'] ?? 1;
        
        debug_log("Creating book with data", [
            'title' => $title,
            'author' => $author,
            'isbn' => $isbn,
            'published_year' => $publishedYear,
            'genre' => $genre,
            'copies_total' => $copiesTotal,
            'copies_available' => $copiesAvailable
        ]);
        
        // Handle file upload
        $coverImage = null;
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            debug_log("Processing cover image upload");
            $coverImage = $this->uploadCoverImage($_FILES['cover_image']);
            debug_log("Cover image uploaded: " . ($coverImage ?: 'failed'));
        }
        
        // Create book
        $bookModel = new Book($this->db);
        $bookData = [
            'title' => $title,
            'author' => $author,
            'isbn' => $isbn,
            'published_year' => $publishedYear,
            'genre' => $genre,
            'copies_total' => $copiesTotal,
            'copies_available' => $copiesAvailable,
            'cover_image' => $coverImage
        ];
        
        $bookModel->createBook($bookData);
        
        // Set success message
        $_SESSION['flash_message'] = 'Book added successfully!';
        $_SESSION['flash_type'] = 'success';
        
        debug_log("Book created successfully, redirecting to books list");
        
        // Redirect to books list
        $this->redirect('/books');
    }
    
    public function edit($id) {
        debug_log("BookController::edit() called with id: " . $id);
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        $bookModel = new Book($this->db);
        $book = $bookModel->getBookById($id);
        
        debug_log("Book lookup result", $book);
        
        if (!$book) {
            debug_log("Book not found, showing 404 page");
            
            // Show 404 page
            $data = [
                'title' => 'Book Not Found - Lib4All'
            ];
            $this->view('404', $data);
            return;
        }
        
        $data = [
            'title' => 'Edit Book - Lib4All',
            'book' => $book
        ];
        
        $this->view('book_form', $data);
    }
    
    public function update($id) {
        debug_log("BookController::update() called with id: " . $id);
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        $title = $_POST['title'] ?? '';
        $author = $_POST['author'] ?? '';
        $isbn = $_POST['isbn'] ?? '';
        $publishedYear = $_POST['published_year'] ?? '';
        $genre = $_POST['genre'] ?? '';
        $copiesTotal = $_POST['copies_total'] ?? 1;
        $copiesAvailable = $_POST['copies_available'] ?? 1;
        
        debug_log("Updating book with data", [
            'title' => $title,
            'author' => $author,
            'isbn' => $isbn,
            'published_year' => $publishedYear,
            'genre' => $genre,
            'copies_total' => $copiesTotal,
            'copies_available' => $copiesAvailable
        ]);
        
        // Handle file upload
        $coverImage = null;
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            debug_log("Processing cover image upload");
            $coverImage = $this->uploadCoverImage($_FILES['cover_image']);
            debug_log("Cover image uploaded: " . ($coverImage ?: 'failed'));
        }
        
        // Update book
        $bookModel = new Book($this->db);
        $bookData = [
            'title' => $title,
            'author' => $author,
            'isbn' => $isbn,
            'published_year' => $publishedYear,
            'genre' => $genre,
            'copies_total' => $copiesTotal,
            'copies_available' => $copiesAvailable
        ];
        
        // Only update cover image if a new one was uploaded
        if ($coverImage) {
            $bookData['cover_image'] = $coverImage;
        }
        
        $bookModel->updateBook($id, $bookData);
        
        // Set success message
        $_SESSION['flash_message'] = 'Book updated successfully!';
        $_SESSION['flash_type'] = 'success';
        
        debug_log("Book updated successfully, redirecting to books list");
        
        // Redirect to books list
        $this->redirect('/books');
    }
    
    public function delete($id) {
        debug_log("BookController::delete() called with id: " . $id);
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        $bookModel = new Book($this->db);
        $bookModel->deleteBook($id);
        
        // Set success message
        $_SESSION['flash_message'] = 'Book deleted successfully!';
        $_SESSION['flash_type'] = 'success';
        
        debug_log("Book deleted successfully, redirecting to books list");
        
        // Redirect to books list
        $this->redirect('/books');
    }
    
    public function reserve($id) {
        debug_log("BookController::reserve() called with id: " . $id);
        
        // Check if user is logged in
        if (!AuthMiddleware::isAuthenticated()) {
            debug_log("User not authenticated, redirecting to login");
            $this->redirect('/login');
            return;
        }
        
        debug_log("User authenticated with id: " . $_SESSION['user_id']);
        
        $bookModel = new Book($this->db);
        $book = $bookModel->getBookById($id);
        
        debug_log("Book lookup result", $book);
        
        if (!$book) {
            debug_log("Book not found, showing 404 page");
            
            // Show 404 page
            $data = [
                'title' => 'Book Not Found - Lib4All'
            ];
            $this->view('404', $data);
            return;
        }
        
        // Check if there are available copies
        if ($book['copies_available'] <= 0) {
            debug_log("No copies available for reservation");
            
            // Show error message
            $data = [
                'title' => $book['title'] . ' - Lib4All',
                'book' => $book,
                'error' => 'No copies available for reservation'
            ];
            $this->view('book_detail', $data);
            return;
        }
        
        // Create reservation without automatically setting due date
        $reservationModel = new Reservation($this->db);
        $result = $reservationModel->createReservation($_SESSION['user_id'], $id, false);
        
        debug_log("Reservation creation result: " . ($result ? 'success' : 'failed'));
        
        if ($result) {
            // Update book availability
            $bookModel->updateBook($id, [
                'copies_available' => $book['copies_available'] - 1
            ]);
            
            debug_log("Book availability updated, copies available: " . ($book['copies_available'] - 1));
            
            // Send reservation confirmation email
            $userModel = new User($this->db);
            $user = $userModel->getUserById($_SESSION['user_id']);
            
            debug_log("Sending reservation confirmation email to: " . $user['email']);
            
            $mailService = new MailService();
            $mailService->sendReservationConfirmation($user['email'], $user['name'], $book['title']);
            
            // Set success message
            $_SESSION['flash_message'] = 'Book reserved successfully! A confirmation email has been sent to your email address.';
            $_SESSION['flash_type'] = 'success';
            
            debug_log("Reservation successful, redirecting to book detail page");
            
            // Redirect to book detail page
            $this->redirect('/books/detail?id=' . $id);
        } else {
            debug_log("Reservation failed - possibly duplicate reservation");
            
            // Show error message
            $data = [
                'title' => $book['title'] . ' - Lib4All',
                'book' => $book,
                'error' => 'Failed to reserve book. You may have already reserved this book.'
            ];
            $this->view('book_detail', $data);
        }
    }
    
    public function cancelReservation($id) {
        debug_log("BookController::cancelReservation() called with id: " . $id);
        
        // Check if user is logged in
        if (!AuthMiddleware::isAuthenticated()) {
            debug_log("User not authenticated, redirecting to login");
            $this->redirect('/login');
            return;
        }
        
        debug_log("User authenticated with id: " . $_SESSION['user_id']);
        
        $bookModel = new Book($this->db);
        $book = $bookModel->getBookById($id);
        
        debug_log("Book lookup result", $book);
        
        if (!$book) {
            debug_log("Book not found, showing 404 page");
            
            // Show 404 page
            $data = [
                'title' => 'Book Not Found - Lib4All'
            ];
            $this->view('404', $data);
            return;
        }
        
        // Cancel reservation
        $reservationModel = new Reservation($this->db);
        $result = $reservationModel->cancelReservation($_GET['reservation_id'], $_SESSION['user_id']);
        
        debug_log("Reservation cancellation result: " . ($result ? 'success' : 'failed'));
        
        if ($result) {
            // Update book availability
            $bookModel->updateBook($id, [
                'copies_available' => $book['copies_available'] + 1
            ]);
            
            debug_log("Book availability updated, copies available: " . ($book['copies_available'] + 1));
            
            // Set success message
            $_SESSION['flash_message'] = 'Reservation cancelled successfully!';
            $_SESSION['flash_type'] = 'success';
            
            debug_log("Reservation cancelled successfully, redirecting to book detail page");
            
            // Redirect to book detail page
            $this->redirect('/books/detail?id=' . $id);
        } else {
            debug_log("Reservation cancellation failed");
            
            // Show error message
            $data = [
                'title' => $book['title'] . ' - Lib4All',
                'book' => $book,
                'error' => 'Failed to cancel reservation'
            ];
            $this->view('book_detail', $data);
        }
    }
    
    private function uploadCoverImage($file) {
        debug_log("BookController::uploadCoverImage() called");
        debug_log("File data", $file);
        
        // Create uploads directory if it doesn't exist
        $uploadDir = __DIR__ . '/../uploads/book_covers/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
            debug_log("Created uploads directory: " . $uploadDir);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $uploadPath = $uploadDir . $filename;
        
        debug_log("Generated filename: " . $filename);
        debug_log("Upload path: " . $uploadPath);
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            debug_log("File uploaded successfully");
            
            // Return relative path for database storage
            return 'uploads/book_covers/' . $filename;
        }
        
        debug_log("File upload failed");
        return null;
    }
}