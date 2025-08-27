<?php

class AuthMiddleware {
    
    public static function isAuthenticated() {
        // Check if user is logged in
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    public static function isAdmin() {
        // Check if user is admin
        return isset($_SESSION['user_role']) && 
               ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'librarian');
    }
    
    public static function requireAuth() {
        if (!self::isAuthenticated()) {
            header('Location: /login');
            exit();
        }
    }
    
    public static function requireAdmin() {
        // First check if user is authenticated
        if (!self::isAuthenticated()) {
            header('Location: /login');
            exit();
        }
        
        // Then check if user is admin
        if (!self::isAdmin()) {
            header('HTTP/1.0 403 Forbidden');
            echo 'Access denied. Admin privileges required.';
            exit();
        }
    }
}