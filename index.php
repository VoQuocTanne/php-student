<?php
// Start session
session_start();

// Load required files
require_once 'app/controllers/SinhVienController.php';
require_once 'app/controllers/HocPhanController.php';
require_once 'app/controllers/DangKyController.php';
require_once 'app/controllers/AuthController.php';

// Create auth controller for authentication checks
$authController = new AuthController();

// Get controller and action from URL parameters
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'sinhvien';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Create controller object based on the request
switch ($controller) {
    case 'sinhvien':
        // Check if user is logged in
        $authController->requireLogin();
        
        $controller = new SinhVienController();
        break;
    
    case 'hocphan':
        // Check if user is logged in
        $authController->requireLogin();
        
        $controller = new HocPhanController();
        break;
    
    case 'dangky':
        // Check if user is logged in
        $authController->requireLogin();
        
        $controller = new DangKyController();
        break;
    
    case 'auth':
        $controller = $authController;
        break;
    
    default:
        // Redirect to login page if controller not found
        header('Location: index.php?controller=auth&action=login');
        exit;
}

// Call the appropriate action method
switch ($action) {
    // SinhVienController actions
    case 'index':
    case 'create':
    case 'store':
    case 'show':
    case 'edit':
    case 'update':
    case 'delete':
        // Call the action method
        $controller->$action();
        break;
    
    // AuthController specific actions
    case 'login':
    case 'authenticate':
    case 'logout':
        // Only allow these actions for AuthController
        if ($controller instanceof AuthController) {
            $controller->$action();
        } else {
            header('Location: index.php');
            exit;
        }
        break;
    
    default:
        // Redirect to homepage if action not found
        header('Location: index.php');
        exit;
} 