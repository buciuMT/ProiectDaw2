<?php

// Debugging configuration
define('DEBUGGING', true); // Set to false to disable debugging

// Store debug messages
$debug_messages = [];

// Debug logging function
function debug_log($message, $data = null) {
    global $debug_messages;
    
    if (DEBUGGING) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] DEBUG: {$message}";
        
        if ($data !== null) {
            $logMessage .= ' ' . print_r($data, true);
        }
        
        // Store in global array
        $debug_messages[] = $logMessage;
        
        // Also log to error log
        error_log($logMessage);
    }
}

// Function to display debug messages in HTML
function display_debug_messages() {
    global $debug_messages;
    
    if (DEBUGGING && !empty($debug_messages)) {
        echo '<div style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; margin: 20px 0; padding: 15px;">';
        echo '<h4 style="color: #495057; margin-top: 0;">Debug Information</h4>';
        echo '<pre style="background-color: #e9ecef; padding: 10px; border-radius: 3px; overflow-x: auto;">';
        foreach ($debug_messages as $message) {
            echo htmlspecialchars($message) . "\n";
        }
        echo '</pre>';
        echo '</div>';
    }
}