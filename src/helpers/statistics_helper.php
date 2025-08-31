<?php

// Include TCPDF library
require_once __DIR__ . '/../lib/TCPDF/tcpdf.php';

function getUserStatistics($db) {
    // Total users
    $totalStmt = $db->query("SELECT COUNT(*) as total FROM users");
    $totalUsers = $totalStmt->fetch()['total'];
    
    // Users by role
    $roleStmt = $db->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    $usersByRole = $roleStmt->fetchAll();
    
    // Verified vs unverified users
    $verifiedStmt = $db->query("SELECT 
        SUM(CASE WHEN verified IS NOT NULL THEN 1 ELSE 0 END) as verified,
        SUM(CASE WHEN verified IS NULL THEN 1 ELSE 0 END) as unverified
        FROM users");
    $verificationStats = $verifiedStmt->fetch();
    
    return [
        'total_users' => $totalUsers,
        'users_by_role' => $usersByRole,
        'verified_users' => $verificationStats['verified'],
        'unverified_users' => $verificationStats['unverified']
    ];
}

function getBookStatistics($db) {
    // Total books
    $totalStmt = $db->query("SELECT COUNT(*) as total FROM books");
    $totalBooks = $totalStmt->fetch()['total'];
    
    // Total copies
    $copiesStmt = $db->query("SELECT SUM(copies_total) as total_copies, SUM(copies_available) as available_copies FROM books");
    $copyStats = $copiesStmt->fetch();
    
    // Books by genre
    $genreStmt = $db->query("SELECT genre, COUNT(*) as count FROM books WHERE genre IS NOT NULL GROUP BY genre ORDER BY count DESC LIMIT 10");
    $booksByGenre = $genreStmt->fetchAll();
    
    // Most popular books (by reservation count)
    $popularStmt = $db->query("SELECT b.title, b.author, COUNT(r.id) as reservation_count 
        FROM books b 
        LEFT JOIN reservations r ON b.id = r.book_id 
        GROUP BY b.id 
        ORDER BY reservation_count DESC 
        LIMIT 10");
    $popularBooks = $popularStmt->fetchAll();
    
    return [
        'total_books' => $totalBooks,
        'total_copies' => $copyStats['total_copies'],
        'available_copies' => $copyStats['available_copies'],
        'books_by_genre' => $booksByGenre,
        'popular_books' => $popularBooks
    ];
}

function getReservationStatistics($db) {
    // Total reservations
    $totalStmt = $db->query("SELECT COUNT(*) as total FROM reservations");
    $totalReservations = $totalStmt->fetch()['total'];
    
    // Reservations by status
    $statusStmt = $db->query("SELECT status, COUNT(*) as count FROM reservations GROUP BY status");
    $reservationsByStatus = $statusStmt->fetchAll();
    
    // Recent reservations (last 30 days)
    $recentStmt = $db->query("SELECT COUNT(*) as recent FROM reservations WHERE reserved_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $recentReservations = $recentStmt->fetch()['recent'];
    
    return [
        'total_reservations' => $totalReservations,
        'reservations_by_status' => $reservationsByStatus,
        'recent_reservations' => $recentReservations
    ];
}

function getLoanStatistics($db) {
    // Total loans
    $totalStmt = $db->query("SELECT COUNT(*) as total FROM loans");
    $totalLoans = $totalStmt->fetch()['total'];
    
    // Loans by status
    $statusStmt = $db->query("SELECT status, COUNT(*) as count FROM loans GROUP BY status");
    $loansByStatus = $statusStmt->fetchAll();
    
    // Overdue loans
    $overdueStmt = $db->query("SELECT COUNT(*) as overdue FROM loans WHERE status = 'overdue'");
    $overdueLoans = $overdueStmt->fetch()['overdue'];
    
    return [
        'total_loans' => $totalLoans,
        'loans_by_status' => $loansByStatus,
        'overdue_loans' => $overdueLoans
    ];
}

function getAllStatistics($db) {
    return [
        'users' => getUserStatistics($db),
        'books' => getBookStatistics($db),
        'reservations' => getReservationStatistics($db),
        'loans' => getLoanStatistics($db),
        'visits' => getVisitStatistics($db)
    ];
}

function exportStatisticsToPDF($db, $filename = 'library_statistics.pdf') {
    // Get all statistics
    $stats = getAllStatistics($db);
    
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Lib4All Library System');
    $pdf->SetTitle('Library Statistics Report');
    $pdf->SetSubject('Library Statistics');
    $pdf->SetKeywords('Library, Statistics, Report');
    
    // Set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Library Statistics Report', 'Generated on ' . date('Y-m-d H:i:s'));
    
    // Set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    
    // Set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    
    // Set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    
    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    
    // Set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    
    // Add a page
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', 'B', 20);
    
    // Title
    $pdf->Cell(0, 15, 'Library Statistics Report', 0, 1, 'C');
    $pdf->Ln(10);
    
    // User Statistics
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'User Statistics', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 8, 'Total Users: ' . $stats['users']['total_users'], 0, 1);
    $pdf->Cell(0, 8, 'Verified Users: ' . $stats['users']['verified_users'], 0, 1);
    $pdf->Cell(0, 8, 'Unverified Users: ' . $stats['users']['unverified_users'], 0, 1);
    $pdf->Ln(5);
    
    // Users by Role
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Users by Role', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);
    foreach ($stats['users']['users_by_role'] as $role) {
        $pdf->Cell(0, 8, ucfirst($role['role']) . ': ' . $role['count'], 0, 1);
    }
    $pdf->Ln(10);
    
    // Book Statistics
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Book Statistics', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 8, 'Total Books: ' . $stats['books']['total_books'], 0, 1);
    $pdf->Cell(0, 8, 'Total Copies: ' . $stats['books']['total_copies'], 0, 1);
    $pdf->Cell(0, 8, 'Available Copies: ' . $stats['books']['available_copies'], 0, 1);
    $pdf->Ln(5);
    
    // Books by Genre
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Books by Genre', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);
    foreach ($stats['books']['books_by_genre'] as $genre) {
        $pdf->Cell(0, 8, $genre['genre'] . ': ' . $genre['count'], 0, 1);
    }
    $pdf->Ln(10);
    
    // Reservation Statistics
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Reservation Statistics', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 8, 'Total Reservations: ' . $stats['reservations']['total_reservations'], 0, 1);
    $pdf->Cell(0, 8, 'Recent Reservations (30 days): ' . $stats['reservations']['recent_reservations'], 0, 1);
    $pdf->Ln(5);
    
    // Reservations by Status
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Reservations by Status', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);
    foreach ($stats['reservations']['reservations_by_status'] as $status) {
        $pdf->Cell(0, 8, ucfirst($status['status']) . ': ' . $status['count'], 0, 1);
    }
    $pdf->Ln(10);
    
    // Loan Statistics
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Loan Statistics', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 8, 'Total Loans: ' . $stats['loans']['total_loans'], 0, 1);
    $pdf->Cell(0, 8, 'Overdue Loans: ' . $stats['loans']['overdue_loans'], 0, 1);
    $pdf->Ln(5);
    
    // Loans by Status
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Loans by Status', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);
    foreach ($stats['loans']['loans_by_status'] as $status) {
        $pdf->Cell(0, 8, ucfirst($status['status']) . ': ' . $status['count'], 0, 1);
    }
    $pdf->Ln(10);
    
    // Visit Statistics
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Visit Statistics', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 8, 'Total Visits: ' . $stats['visits']['total_visits'], 0, 1);
    $pdf->Cell(0, 8, 'Logged-in User Visits: ' . $stats['visits']['logged_in_visits'], 0, 1);
    $pdf->Cell(0, 8, 'Guest Visitor Visits: ' . $stats['visits']['non_logged_in_visits'], 0, 1);
    $pdf->Cell(0, 8, 'Unique Visitors: ' . $stats['visits']['unique_visitors'], 0, 1);
    
    // Save the PDF to a temporary file
    $filepath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
    $pdf->Output($filepath, 'F');
    
    return $filepath;
}