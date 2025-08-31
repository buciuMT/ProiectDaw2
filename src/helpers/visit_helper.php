<?php

function trackVisit($db, $pageUrl = null) {
    // Get the current page URL if not provided
    if ($pageUrl === null) {
        $pageUrl = $_SERVER['REQUEST_URI'];
    }
    
    // Determine if user is logged in
    $isLoggedIn = isset($_SESSION['user_id']) ? 1 : 0;
    $userId = $isLoggedIn ? $_SESSION['user_id'] : null;
    
    // Get IP address
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
    
    // Get user agent
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    // Insert visit record
    $stmt = $db->prepare("INSERT INTO visits (user_id, ip_address, user_agent, page_url, is_logged_in) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $ipAddress, $userAgent, $pageUrl, $isLoggedIn]);
}

function getVisitStatistics($db) {
    // Get total visits
    $totalStmt = $db->query("SELECT COUNT(*) as total FROM visits");
    $totalVisits = $totalStmt->fetch()['total'];
    
    // Get logged-in user visits
    $loggedInStmt = $db->query("SELECT COUNT(*) as logged_in FROM visits WHERE is_logged_in = 1");
    $loggedInVisits = $loggedInStmt->fetch()['logged_in'];
    
    // Get non-logged-in user visits
    $nonLoggedInVisits = $totalVisits - $loggedInVisits;
    
    // Get unique visitors (by IP)
    $uniqueStmt = $db->query("SELECT COUNT(DISTINCT ip_address) as unique_visitors FROM visits");
    $uniqueVisitors = $uniqueStmt->fetch()['unique_visitors'];
    
    // Get top pages
    $topPagesStmt = $db->query("SELECT page_url, COUNT(*) as visits FROM visits GROUP BY page_url ORDER BY visits DESC LIMIT 10");
    $topPages = $topPagesStmt->fetchAll();
    
    return [
        'total_visits' => $totalVisits,
        'logged_in_visits' => $loggedInVisits,
        'non_logged_in_visits' => $nonLoggedInVisits,
        'unique_visitors' => $uniqueVisitors,
        'top_pages' => $topPages
    ];
}

function getVisitTrend($db, $days = 30) {
    $stmt = $db->prepare("
        SELECT 
            DATE(created_at) as date,
            COUNT(*) as total_visits,
            SUM(CASE WHEN is_logged_in = 1 THEN 1 ELSE 0 END) as logged_in_visits,
            SUM(CASE WHEN is_logged_in = 0 THEN 1 ELSE 0 END) as non_logged_in_visits
        FROM visits 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ");
    $stmt->execute([$days]);
    return $stmt->fetchAll();
}