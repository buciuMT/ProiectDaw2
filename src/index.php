<?php

require_once __DIR__ . '/bootstrap.php';

// Track visit
trackVisit($db);

// Simple router
$path = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

debug_log("Routing request", [
    'path' => $path,
    'method' => $method
]);

// Parse the path to separate the path from query parameters
$parsedUrl = parse_url($path);
$route = $parsedUrl['path'];

debug_log("Parsed route", [
    'route' => $route,
    'query_params' => isset($parsedUrl['query']) ? $parsedUrl['query'] : null
]);

// Initialize controllers
$mainController = new MainController($db);
$adminController = new AdminController($db);
$authController = new AuthController($db);
$bookController = new BookController($db);

// Route handling
switch ($route) {
    case '/':
    case '':
        debug_log("Routing to MainController::index()");
        $mainController->index();
        break;
    case '/books':
        debug_log("Routing to BookController::index()");
        $bookController->index();
        break;
    case '/books/create':
        debug_log("Routing to BookController::create()");
        $bookController->create();
        break;
    case '/books/scrape':
        debug_log("Routing to BookController::scrape()");
        $bookController->scrape();
        break;
    case '/books/scrape-store':
        debug_log("Routing to BookController::scrapeStore()");
        if ($method === 'POST') {
            $bookController->scrapeStore();
        }
        break;
    case '/books/store':
        debug_log("Routing to BookController::store()");
        if ($method === 'POST') {
            $bookController->store();
        }
        break;
    case '/books/edit':
        debug_log("Routing to BookController::edit() with id: " . ($_GET['id'] ?? 'null'));
        if (isset($_GET['id'])) {
            $bookController->edit($_GET['id']);
        }
        break;
    case '/books/update':
        debug_log("Routing to BookController::update() with id: " . ($_GET['id'] ?? 'null'));
        if ($method === 'POST' && isset($_GET['id'])) {
            $bookController->update($_GET['id']);
        }
        break;
    case '/books/delete':
        debug_log("Routing to BookController::delete() with id: " . ($_GET['id'] ?? 'null'));
        if ($method === 'POST' && isset($_GET['id'])) {
            $bookController->delete($_GET['id']);
        }
        break;
    case '/books/detail':
        debug_log("Routing to BookController::show() with id: " . ($_GET['id'] ?? 'null'));
        if (isset($_GET['id'])) {
            $bookController->show($_GET['id']);
        }
        break;
    case '/books/reserve':
        debug_log("Routing to BookController::reserve() with id: " . ($_GET['id'] ?? 'null'));
        if ($method === 'POST' && isset($_GET['id'])) {
            $bookController->reserve($_GET['id']);
        }
        break;
    case '/reservation-history':
        debug_log("Routing to MainController::reservationHistory()");
        $mainController->reservationHistory();
        break;
    case '/books/cancel-reservation':
        debug_log("Routing to BookController::cancelReservation() with id: " . ($_GET['id'] ?? 'null'));
        if ($method === 'POST' && isset($_GET['id']) && isset($_GET['reservation_id'])) {
            $bookController->cancelReservation($_GET['id']);
        }
        break;
    case '/my-reservations':
        debug_log("Routing to MainController::myReservations()");
        $mainController->myReservations();
        break;
    case '/my-books':
        debug_log("Routing to MainController::myBooks()");
        $mainController->myBooks();
        break;
    case '/reservation-history':
        debug_log("Routing to MainController::reservationHistory()");
        $mainController->reservationHistory();
        break;
    case '/cancel-reservation':
        debug_log("Routing to MainController::cancelReservation()");
        if ($method === 'POST') {
            $mainController->cancelReservation();
        }
        break;
    case '/users':
        debug_log("Routing to MainController::users()");
        $mainController->users();
        break;
    case '/login':
        debug_log("Routing to AuthController::login()");
        if ($method === 'GET') {
            $authController->showLogin();
        } elseif ($method === 'POST') {
            $authController->login();
        }
        break;
    case '/register':
        debug_log("Routing to AuthController::register()");
        if ($method === 'GET') {
            $authController->showRegister();
        } elseif ($method === 'POST') {
            $authController->register();
        }
        break;
    case '/logout':
        debug_log("Routing to AuthController::logout()");
        $authController->logout();
        break;
    case '/admin':
    case '/admin/dashboard':
        debug_log("Routing to AdminController::dashboard()");
        $adminController->dashboard();
        break;
    case '/admin/users':
        debug_log("Routing to AdminController::listUsers()");
        $adminController->listUsers();
        break;
    case '/admin/users/edit':
        debug_log("Routing to AdminController::editUser() with id: " . ($_GET['id'] ?? 'null'));
        if (isset($_GET['id'])) {
            $adminController->editUser($_GET['id']);
        }
        break;
    case '/admin/users/update':
        debug_log("Routing to AdminController::updateUser() with id: " . ($_GET['id'] ?? 'null'));
        if ($method === 'POST' && isset($_GET['id'])) {
            $adminController->updateUser($_GET['id']);
        }
        break;
    case '/admin/users/delete':
        debug_log("Routing to AdminController::deleteUser() with id: " . ($_GET['id'] ?? 'null'));
        if ($method === 'POST' && isset($_GET['id'])) {
            $adminController->deleteUser($_GET['id']);
        }
        break;
    case '/admin/migrations':
        debug_log("Routing to AdminController::listMigrations()");
        $adminController->listMigrations();
        break;
    case '/admin/migrations/view':
        debug_log("Routing to AdminController::viewMigration()");
        $adminController->viewMigration();
        break;
    case '/admin/migrations/run':
        debug_log("Routing to AdminController::runMigration()");
        $adminController->runMigration();
        break;
    case '/employee/dashboard':
        debug_log("Routing to EmployeeController::dashboard()");
        $employeeController = new EmployeeController($db);
        $employeeController->dashboard();
        break;
    case '/employee/manage-reservation':
        debug_log("Routing to EmployeeController::manageReservation()");
        $employeeController = new EmployeeController($db);
        if ($method === 'POST') {
            $employeeController->manageReservation();
        }
        break;
    case '/admin/test':
        debug_log("Routing to AdminController::test()");
        $adminController->test();
        break;
    case '/admin/visit-statistics':
        debug_log("Routing to AdminController::visitStatistics()");
        $adminController->visitStatistics();
        break;
    case '/admin/export-statistics':
        debug_log("Routing to AdminController::exportStatistics()");
        $adminController->exportStatistics();
        break;
    case '/api/test':
        debug_log("Routing to AdminController::apiTest()");
        $adminController->apiTest();
        break;
    case '/verify-email':
        debug_log("Routing to AuthController::verifyEmail()");
        $authController->verifyEmail();
        break;
    case '/please-verify':
        debug_log("Routing to AuthController::pleaseVerify()");
        $authController->pleaseVerify();
        break;
    case '/contact':
        debug_log("Routing to MainController::contact()");
        if ($method === 'GET') {
            $mainController->showContactForm();
        } elseif ($method === 'POST') {
            $mainController->sendContactMessage();
        }
        break;
    default:
        debug_log("Routing to 404 page");
        // Show 404 page
        $data = [
            'title' => 'Page Not Found - Lib4All'
        ];
        $mainController->view('404', $data);
        break;
}