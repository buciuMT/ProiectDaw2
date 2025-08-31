<?php

class AdminController extends Controller {
    
    public function dashboard() {
        debug_log("AdminController::dashboard() called");
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        // Get user statistics
        $userModel = new User($this->db);
        $users = $userModel->getAllUsers();
        
        // Get book statistics
        $bookModel = new Book($this->db);
        $books = $bookModel->getAllBooks();
        
        // Get reservation statistics
        $reservationModel = new Reservation($this->db);
        $reservations = $reservationModel->getAllReservations();
        
        // Get visit statistics
        $visitStats = getVisitStatistics($this->db);
        
        // Get all statistics for dashboard
        $allStats = getAllStatistics($this->db);
        
        $data = [
            'title' => 'Admin Dashboard - Lib4All',
            'users' => $users,
            'books' => $books,
            'reservations' => $reservations,
            'visit_stats' => $visitStats,
            'all_stats' => $allStats
        ];
        
        $this->view('admin/dashboard', $data);
    }
    
    public function visitStatistics() {
        debug_log("AdminController::visitStatistics() called");
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        // Get detailed visit statistics
        $visitStats = getVisitStatistics($this->db);
        $visitTrend = getVisitTrend($this->db, 30);
        
        $data = [
            'title' => 'Visit Statistics - Lib4All',
            'visit_stats' => $visitStats,
            'visit_trend' => $visitTrend
        ];
        
        $this->view('admin/visit_statistics', $data);
    }
    
    public function exportStatistics() {
        debug_log("AdminController::exportStatistics() called");
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        // Generate and export statistics
        $filename = 'library_statistics_' . date('Y-m-d') . '.pdf';
        $filepath = exportStatisticsToPDF($this->db, $filename);
        
        // Set headers for download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Read and output the file
        readfile($filepath);
        
        // Delete the temporary file
        unlink($filepath);
        
        exit;
    }
    
    public function editUser($id) {
        debug_log("AdminController::editUser() called with id: " . $id);
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        $userModel = new User($this->db);
        $user = $userModel->getUserById($id);
        
        if (!$user) {
            // Show 404 page
            $data = [
                'title' => 'User Not Found - Lib4All'
            ];
            $this->view('404', $data);
            return;
        }
        
        $data = [
            'title' => 'Edit User - Lib4All',
            'user' => $user
        ];
        
        $this->view('admin/edit_user', $data);
    }
    
    public function updateUser($id) {
        debug_log("AdminController::updateUser() called with id: " . $id);
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        $userModel = new User($this->db);
        $user = $userModel->getUserById($id);
        
        if (!$user) {
            // Show 404 page
            $data = [
                'title' => 'User Not Found - Lib4All'
            ];
            $this->view('404', $data);
            return;
        }
        
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = $_POST['role'] ?? 'member';
        
        // Update user
        $userData = [
            'name' => $name,
            'email' => $email,
            'role' => $role
        ];
        
        $userModel->updateUser($id, $userData);
        
        // Set success message
        $_SESSION['flash_message'] = 'User updated successfully!';
        $_SESSION['flash_type'] = 'success';
        
        // Redirect to users list
        $this->redirect('/admin/users');
    }
    
    public function deleteUser($id) {
        debug_log("AdminController::deleteUser() called with id: " . $id);
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        // Prevent deleting the current user
        if ($id == $_SESSION['user_id']) {
            $_SESSION['flash_message'] = 'You cannot delete your own account!';
            $_SESSION['flash_type'] = 'danger';
            $this->redirect('/admin/users');
            return;
        }
        
        $userModel = new User($this->db);
        $userModel->deleteUser($id);
        
        // Set success message
        $_SESSION['flash_message'] = 'User deleted successfully!';
        $_SESSION['flash_type'] = 'success';
        
        // Redirect to users list
        $this->redirect('/admin/users');
    }
    
    public function listMigrations() {
        debug_log("AdminController::listMigrations() called");
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        // List all migration files
        $migrationDir = __DIR__ . '/../migrations/';
        $files = scandir($migrationDir);
        
        // Filter out . and ..
        $migrationFiles = [];
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $migrationDir . $file;
                $migrationFiles[] = [
                    'name' => $file,
                    'size' => filesize($filePath),
                    'modified' => date('Y-m-d H:i:s', filemtime($filePath)),
                    'content' => file_get_contents($filePath)
                ];
            }
        }
        
        debug_log("Found " . count($migrationFiles) . " migration files");
        
        $data = [
            'title' => 'Database Migrations - Lib4All',
            'migrations' => $migrationFiles
        ];
        
        $this->view('admin/migrations', $data);
    }
    
    public function viewMigration() {
        debug_log("AdminController::viewMigration() called");
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        $filename = $_GET['file'] ?? '';
        
        if (empty($filename)) {
            $_SESSION['flash_message'] = 'No migration file specified.';
            $_SESSION['flash_type'] = 'danger';
            $this->redirect('/admin/migrations');
            return;
        }
        
        $migrationPath = __DIR__ . '/../migrations/' . $filename;
        
        if (!file_exists($migrationPath)) {
            $_SESSION['flash_message'] = 'Migration file not found.';
            $_SESSION['flash_type'] = 'danger';
            $this->redirect('/admin/migrations');
            return;
        }
        
        $content = file_get_contents($migrationPath);
        
        $data = [
            'title' => 'View Migration - Lib4All',
            'filename' => $filename,
            'content' => $content
        ];
        
        $this->view('admin/view_migration', $data);
    }
    
    public function runMigration() {
        debug_log("AdminController::runMigration() called");
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        $filename = $_GET['file'] ?? '';
        
        if (empty($filename)) {
            $_SESSION['flash_message'] = 'No migration file specified.';
            $_SESSION['flash_type'] = 'danger';
            $this->redirect('/admin/migrations');
            return;
        }
        
        $migrationPath = __DIR__ . '/../migrations/' . $filename;
        
        if (!file_exists($migrationPath)) {
            $_SESSION['flash_message'] = 'Migration file not found.';
            $_SESSION['flash_type'] = 'danger';
            $this->redirect('/admin/migrations');
            return;
        }
        
        $migration = new Migration($this->db);
        $result = $migration->run($filename);
        
        if ($result) {
            debug_log("Migration completed successfully: " . $filename);
            $_SESSION['flash_message'] = "Migration {$filename} completed successfully!";
            $_SESSION['flash_type'] = 'success';
        } else {
            debug_log("Failed to run migration: " . $filename);
            $_SESSION['flash_message'] = "Failed to run migration {$filename}.";
            $_SESSION['flash_type'] = 'danger';
        }
        
        $this->redirect('/admin/migrations');
    }
    
    public function employeeDashboard() {
        debug_log("AdminController::employeeDashboard() called");
        
        // Check if user is admin or librarian using middleware
        AuthMiddleware::requireAdmin();
        
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
        
        $this->view('admin/employee_dashboard', $data);
    }
    
    public function manageReservation() {
        debug_log("AdminController::manageReservation() called");
        
        // Check if user is admin or librarian using middleware
        AuthMiddleware::requireAdmin();
        
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
    
    public function test() {
        debug_log("AdminController::test() called");
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        $data = [
            'title' => 'System Tests - Lib4All'
        ];
        
        $this->view('admin/test', $data);
    }
    
    public function apiTest() {
        debug_log("AdminController::apiTest() called");
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        // Get the test name from the request
        $test = $_GET['test'] ?? '';
        
        $result = [
            'status' => 'error',
            'message' => 'Unknown test',
            'data' => null
        ];
        
        switch ($test) {
            case 'mail':
                // Test the mail service
                try {
                    $mailService = new MailService();
                    $result['status'] = 'success';
                    $result['message'] = 'Mail service initialized successfully';
                    $result['data'] = [
                        'mail_host' => $mailService->getConfig()['mail_host'],
                        'mail_port' => $mailService->getConfig()['mail_port']
                    ];
                } catch (Exception $e) {
                    $result['message'] = 'Failed to initialize mail service: ' . $e->getMessage();
                }
                break;
                
            case 'send-mail':
                // Test sending an actual email
                try {
                    $mailService = new MailService();
                    // Get the email address from the request
                    $email = $_GET['email'] ?? '';
                    
                    if (empty($email)) {
                        $result['status'] = 'error';
                        $result['message'] = 'Email address is required';
                    } else {
                        // Validate email address
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $result['status'] = 'error';
                            $result['message'] = 'Invalid email address';
                        } else {
                            // Send test email
                            $sent = $mailService->sendTestEmail($email, 'Lib4All Test Recipient');
                            
                            if ($sent) {
                                $result['status'] = 'success';
                                $result['message'] = 'Test email sent successfully to ' . $email;
                                $result['data'] = [
                                    'recipient' => $email,
                                    'mail_host' => $mailService->getConfig()['mail_host'],
                                    'mail_port' => $mailService->getConfig()['mail_port']
                                ];
                            } else {
                                $result['status'] = 'error';
                                $result['message'] = 'Failed to send test email - check your SMTP configuration';
                                // Let's add more debug information
                                $result['data'] = [
                                    'recipient' => $email,
                                    'mail_host' => $mailService->getConfig()['mail_host'],
                                    'mail_port' => $mailService->getConfig()['mail_port'],
                                    'mail_username' => $mailService->getConfig()['mail_username']
                                ];
                            }
                        }
                    }
                } catch (Exception $e) {
                    $result['status'] = 'error';
                    $result['message'] = 'Failed to send test email: ' . $e->getMessage();
                    // Add debug information
                    $result['data'] = [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ];
                }
                break;
                
            default:
                $result['message'] = 'Unknown test: ' . $test;
                break;
        }
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }
}