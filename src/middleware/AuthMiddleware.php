<?php

class AuthMiddleware {
    
    public static function isAuthenticated() {
        // Check if user is logged in
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    public static function isAdmin() {
        // Check if user is admin (only admin)
        return isset($_SESSION['user_role']) && 
               $_SESSION['user_role'] === 'admin';
    }
    
    public static function isEmployee() {
        // Check if user is employee (librarian)
        return isset($_SESSION['user_role']) && 
               $_SESSION['user_role'] === 'librarian';
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
    
    public static function requireEmployee() {
        // First check if user is authenticated
        if (!self::isAuthenticated()) {
            header('Location: /login');
            exit();
        }
        
        // Check if user is employee (librarian) OR admin (admins can access employee functions)
        if (!(self::isEmployee() || self::isAdmin())) {
            header('HTTP/1.0 403 Forbidden');
            echo 'Access denied. Employee privileges required.';
            exit();
        }
    }
}