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
        
        $data = [
            'title' => 'Admin Dashboard - Lib4All',
            'users' => $users,
            'books' => $books,
            'reservations' => $reservations
        ];
        
        $this->view('admin/dashboard', $data);
    }
    
    public function listUsers() {
        debug_log("AdminController::listUsers() called");
        
        // Check if user is admin using middleware
        AuthMiddleware::requireAdmin();
        
        $userModel = new User($this->db);
        $users = $userModel->getAllUsers();
        
        $data = [
            'title' => 'Manage Users - Lib4All',
            'users' => $users
        ];
        
        $this->view('admin/users', $data);
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
}