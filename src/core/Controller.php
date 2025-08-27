<?php

class Controller {
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Render a view with data
    public function view($view, $data = []) {
        // Extract data to variables
        extract($data);
        
        // Include the view file
        $viewPath = __DIR__ . "/../views/{$view}.php";
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            echo "View {$view} not found";
        }
    }

    // Redirect to a URL
    protected function redirect($url) {
        // Debug: Log redirect
        error_log("Redirecting to: " . $url);
        header("Location: {$url}");
        exit();
    }

    // Return JSON response
    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}