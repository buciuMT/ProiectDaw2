<?php

class AuthController extends Controller {
    
    public function showLogin() {
        debug_log("AuthController::showLogin() called");
        debug_log("Current session data", $_SESSION);
        
        $data = [
            'title' => 'Login - Lib4All'
        ];
        $this->view('login', $data);
    }
    
    public function login() {
        debug_log("AuthController::login() called");
        debug_log("Request method: " . $_SERVER['REQUEST_METHOD']);
        debug_log("POST data received", $_POST);
        debug_log("Session data before login", $_SESSION);
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        debug_log("Login attempt for email: " . $email);
        
        // Get user by email
        $userModel = new User($this->db);
        $user = $userModel->getUserByEmail($email);
        
        debug_log("User lookup result", $user);
        
        if ($user && password_verify($password, $user['password'])) {
            debug_log("Authentication successful for user: " . $user['name']);
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            
            debug_log("Session data after login", $_SESSION);
            
            // Set a success message in session
            $_SESSION['flash_message'] = 'Login successful. Welcome back, ' . $user['name'] . '!';
            $_SESSION['flash_type'] = 'success';
            
            // Redirect to home page
            debug_log("Redirecting to home page");
            $this->redirect('/');
        } else {
            debug_log("Authentication failed for email: " . $email);
            if ($user) {
                debug_log("User found but password verification failed");
            } else {
                debug_log("User not found");
            }
            
            // Show login page with error
            $data = [
                'title' => 'Login - Lib4All',
                'error' => 'Invalid email or password'
            ];
            $this->view('login', $data);
        }
    }
    
    public function showRegister() {
        debug_log("AuthController::showRegister() called");
        
        $data = [
            'title' => 'Register - Lib4All'
        ];
        $this->view('register', $data);
    }
    
    public function register() {
        debug_log("AuthController::register() called");
        debug_log("Request method: " . $_SERVER['REQUEST_METHOD']);
        debug_log("POST data received", $_POST);
        
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        debug_log("Registration attempt for email: " . $email);
        
        // Basic validation
        if ($password !== $confirmPassword) {
            debug_log("Password validation failed - passwords do not match");
            
            $data = [
                'title' => 'Register - Lib4All',
                'error' => 'Passwords do not match'
            ];
            $this->view('register', $data);
            return;
        }
        
        // Check if user already exists
        $userModel = new User($this->db);
        $existingUser = $userModel->getUserByEmail($email);
        
        debug_log("Existing user check result", $existingUser);
        
        if ($existingUser) {
            debug_log("Registration failed - email already registered");
            
            $data = [
                'title' => 'Register - Lib4All',
                'error' => 'Email already registered'
            ];
            $this->view('register', $data);
            return;
        }
        
        // Create new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userData = [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => 'member' // Default role
        ];
        
        debug_log("Creating new user with data", $userData);
        
        $userModel->createUser($userData);
        
        // Send registration email
        debug_log("Sending registration email to: " . $email);
        $mailService = new MailService();
        $mailService->sendRegistrationEmail($email, $name);
        
        // Set a success message in session
        $_SESSION['flash_message'] = 'Registration successful. Please check your email for confirmation.';
        $_SESSION['flash_type'] = 'success';
        
        debug_log("Registration successful, redirecting to login page");
        
        // Redirect to login page
        $this->redirect('/login');
    }
    
    public function logout() {
        debug_log("AuthController::logout() called");
        debug_log("Session data before logout", $_SESSION);
        
        // Set a success message in session
        $_SESSION['flash_message'] = 'You have been logged out successfully.';
        $_SESSION['flash_type'] = 'success';
        
        // Destroy session
        session_destroy();
        
        debug_log("Session destroyed");
        
        // Redirect to home page
        $this->redirect('/');
    }
}