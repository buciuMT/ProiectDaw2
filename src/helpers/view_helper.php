<?php

function renderView($view, $data = []) {
    // Extract data to variables
    extract($data);
    
    // Include the view file
    $viewPath = __DIR__ . "/views/{$view}.php";
    if (file_exists($viewPath)) {
        require_once $viewPath;
    } else {
        echo "View {$view} not found";
    }
}