<?php
require_once 'app/config/database.php';
require_once 'app/models/User.php';

class AuthController {
    private $db;
    private $user;
    
    public function __construct() {
        // Initialize database connection
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Initialize models
        $this->user = new User($this->db);
        
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    // Display login form
    public function login() {
        // If already logged in, redirect to homepage
        if($this->isLoggedIn()) {
            header('Location: index.php');
            exit;
        }
        
        // Include view
        include 'app/views/auth/login.php';
    }
    
    // Process login form submission
    public function authenticate() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Set user properties
            $this->user->username = $_POST['username'];
            $this->user->password = $_POST['password'];
            
            // Attempt to login
            if($this->user->login()) {
                // Create session variables
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['username'] = $this->user->username;
                $_SESSION['name'] = $this->user->name;
                $_SESSION['role'] = $this->user->role;
                
                // Redirect to homepage
                header('Location: index.php');
                exit;
            } else {
                $error = "Invalid username or password.";
                include 'app/views/auth/login.php';
            }
        }
    }
    
    // Process logout request
    public function logout() {
        // Clear session variables
        $_SESSION = array();
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login page
        header('Location: index.php?controller=auth&action=login');
        exit;
    }
    
    // Check if user is logged in
    public function isLoggedIn() {
        return (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true);
    }
    
    // Check if user has admin role
    public function isAdmin() {
        return ($this->isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
    }
    
    // Middleware to require login
    public function requireLogin() {
        if(!$this->isLoggedIn()) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }
    
    // Middleware to require admin role
    public function requireAdmin() {
        $this->requireLogin();
        
        if(!$this->isAdmin()) {
            header('Location: index.php');
            exit;
        }
    }
} 