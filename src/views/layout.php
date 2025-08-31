<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Lib4All'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-img-top {
            object-fit: cover;
        }
        .book-card {
            transition: transform 0.2s;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .user-info {
            background-color: #0d6efd;
            border-radius: 20px;
            padding: 5px 15px;
            margin-right: 10px;
        }
        .debug-info {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin: 20px 0;
            padding: 15px;
        }
        .debug-info h4 {
            color: #495057;
            margin-top: 0;
        }
        .debug-info pre {
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 3px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Lib4All</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/books">Books</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['user_role'] === 'librarian' || $_SESSION['user_role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/employee/dashboard">Employee Dashboard</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                My Library
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="/my-reservations">My Reservations</a></li>
                                <li><a class="dropdown-item" href="/my-books">My Borrowed Books</a></li>
                                <li><a class="dropdown-item" href="/reservation-history">Reservation History</a></li>
                            </ul>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <div class="user-info text-white">
                                <span class="me-2">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                    <span class="badge bg-warning text-dark">Admin</span>
                                <?php elseif ($_SESSION['user_role'] === 'librarian'): ?>
                                    <span class="badge bg-info">Employee</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Member</span>
                                <?php endif; ?>
                            </div>
                        </li>
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/dashboard">Admin</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-light ms-2" href="/logout">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-light" href="/login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary ms-2" href="/register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php 
        // Display flash message if exists
        if (isset($_SESSION['flash_message'])) {
            $type = isset($_SESSION['flash_type']) ? $_SESSION['flash_type'] : 'info';
            echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
            echo htmlspecialchars($_SESSION['flash_message']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            
            // Clear flash message
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
        }
        ?>
        
        <?php echo $content ?? ''; ?>
        
        <?php 
        // Display debug messages if debugging is enabled
        if (defined('DEBUGGING') && DEBUGGING) {
            display_debug_messages();
        }
        ?>
    </div>

    <footer class="bg-dark text-light text-center py-3 mt-5">
        <div class="container">
            <p>&copy; 2025 Lib4All Library Management System</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>