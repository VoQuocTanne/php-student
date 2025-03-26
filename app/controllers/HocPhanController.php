<?php
require_once 'app/config/database.php';
require_once 'app/models/HocPhan.php';

class HocPhanController {
    private $db;
    private $hocPhan;
    
    public function __construct() {
        // Initialize database connection
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Initialize models
        $this->hocPhan = new HocPhan($this->db);
    }
    
    // Display list of courses
    public function index() {
        // Get all courses
        $stmt = $this->hocPhan->getAll();
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Include view
        include 'app/views/courses/index.php';
    }
    
    // Display form to create new course
    public function create() {
        // Include view
        include 'app/views/courses/create.php';
    }
    
    // Process form submission for creating course
    public function store() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Set course properties
            $this->hocPhan->MaHP = $_POST['MaHP'];
            $this->hocPhan->TenHP = $_POST['TenHP'];
            $this->hocPhan->SoTinChi = $_POST['SoTinChi'];
            
            // Create course
            if($this->hocPhan->create()) {
                // Redirect to course list
                header('Location: index.php?controller=hocphan&action=index');
                exit;
            } else {
                $error = "Failed to create course. Please try again.";
                include 'app/views/courses/create.php';
            }
        }
    }
    
    // Display course details
    public function show() {
        if(isset($_GET['id'])) {
            // Get course details
            $this->hocPhan->MaHP = $_GET['id'];
            if($this->hocPhan->getSingleCourse()) {
                // Include view
                include 'app/views/courses/show.php';
            } else {
                // Course not found
                header('Location: index.php?controller=hocphan&action=index');
                exit;
            }
        } else {
            // No ID provided
            header('Location: index.php?controller=hocphan&action=index');
            exit;
        }
    }
    
    // Display form to edit course
    public function edit() {
        if(isset($_GET['id'])) {
            // Get course details
            $this->hocPhan->MaHP = $_GET['id'];
            if($this->hocPhan->getSingleCourse()) {
                // Include view
                include 'app/views/courses/edit.php';
            } else {
                // Course not found
                header('Location: index.php?controller=hocphan&action=index');
                exit;
            }
        } else {
            // No ID provided
            header('Location: index.php?controller=hocphan&action=index');
            exit;
        }
    }
    
    // Process form submission for updating course
    public function update() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Set course properties
            $this->hocPhan->MaHP = $_POST['MaHP'];
            $this->hocPhan->TenHP = $_POST['TenHP'];
            $this->hocPhan->SoTinChi = $_POST['SoTinChi'];
            
            // Update course
            if($this->hocPhan->update()) {
                // Redirect to course details
                header('Location: index.php?controller=hocphan&action=show&id=' . $this->hocPhan->MaHP);
                exit;
            } else {
                $error = "Failed to update course. Please try again.";
                include 'app/views/courses/edit.php';
            }
        }
    }
    
    // Process request to delete course
    public function delete() {
        if(isset($_GET['id'])) {
            // Set course ID
            $this->hocPhan->MaHP = $_GET['id'];
            
            // Delete course
            if($this->hocPhan->delete()) {
                // Redirect to course list
                header('Location: index.php?controller=hocphan&action=index');
                exit;
            } else {
                // Failed to delete
                header('Location: index.php?controller=hocphan&action=show&id=' . $this->hocPhan->MaHP);
                exit;
            }
        } else {
            // No ID provided
            header('Location: index.php?controller=hocphan&action=index');
            exit;
        }
    }
} 