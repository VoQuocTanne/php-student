<?php
require_once 'app/config/database.php';
require_once 'app/models/SinhVien.php';
require_once 'app/models/NganhHoc.php';

class SinhVienController {
    private $db;
    private $sinhVien;
    private $nganhHoc;
    
    public function __construct() {
        // Initialize database connection
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Initialize models
        $this->sinhVien = new SinhVien($this->db);
        $this->nganhHoc = new NganhHoc($this->db);
    }
    
    // Display list of students
    public function index() {
        // Get all students
        $stmt = $this->sinhVien->getAll();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Include view
        include 'app/views/students/index.php';
    }
    
    // Display form to create new student
    public function create() {
        // Get all majors for dropdown
        $stmt = $this->nganhHoc->getAll();
        $majors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Include view
        include 'app/views/students/create.php';
    }
    
    // Process form submission for creating student
    public function store() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Set student properties
            $this->sinhVien->MaSV = $_POST['MaSV'];
            $this->sinhVien->HoTen = $_POST['HoTen'];
            $this->sinhVien->GioiTinh = $_POST['GioiTinh'];
            $this->sinhVien->NgaySinh = $_POST['NgaySinh'];
            $this->sinhVien->MaNganh = $_POST['MaNganh'];
            
            // Handle image upload
            if(isset($_FILES['Hinh']) && $_FILES['Hinh']['error'] == 0) {
                $target_dir = "app/public/images/";
                $file_extension = pathinfo($_FILES["Hinh"]["name"], PATHINFO_EXTENSION);
                $new_filename = uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $new_filename;
                
                // Check if directory exists, if not create it
                if(!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                // Move uploaded file to target directory
                if(move_uploaded_file($_FILES["Hinh"]["tmp_name"], $target_file)) {
                    $this->sinhVien->Hinh = $target_dir . $new_filename;
                } else {
                    $this->sinhVien->Hinh = '';
                }
            } else {
                $this->sinhVien->Hinh = '';
            }
            
            // Create student
            if($this->sinhVien->create()) {
                // Redirect to student list
                header('Location: index.php?controller=sinhvien&action=index');
                exit;
            } else {
                // Get all majors for dropdown
                $stmt = $this->nganhHoc->getAll();
                $majors = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $error = "Failed to create student. Please try again.";
                include 'app/views/students/create.php';
            }
        }
    }
    
    // Display student details
    public function show() {
        if(isset($_GET['id'])) {
            // Get student details
            $this->sinhVien->MaSV = $_GET['id'];
            if($this->sinhVien->getSingleStudent()) {
                // Get major details
                $this->nganhHoc->MaNganh = $this->sinhVien->MaNganh;
                $this->nganhHoc->getSingleMajor();
                
                // Include view
                include 'app/views/students/show.php';
            } else {
                // Student not found
                header('Location: index.php?controller=sinhvien&action=index');
                exit;
            }
        } else {
            // No ID provided
            header('Location: index.php?controller=sinhvien&action=index');
            exit;
        }
    }
    
    // Display form to edit student
    public function edit() {
        if(isset($_GET['id'])) {
            // Get student details
            $this->sinhVien->MaSV = $_GET['id'];
            if($this->sinhVien->getSingleStudent()) {
                // Get all majors for dropdown
                $stmt = $this->nganhHoc->getAll();
                $majors = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Include view
                include 'app/views/students/edit.php';
            } else {
                // Student not found
                header('Location: index.php?controller=sinhvien&action=index');
                exit;
            }
        } else {
            // No ID provided
            header('Location: index.php?controller=sinhvien&action=index');
            exit;
        }
    }
    
    // Process form submission for updating student
    public function update() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // First get current student data (especially for the image)
            $this->sinhVien->MaSV = $_POST['MaSV'];
            $this->sinhVien->getSingleStudent();
            $current_image = $this->sinhVien->Hinh;
            
            // Now set new values from the form
            $this->sinhVien->MaSV = $_POST['MaSV'];
            $this->sinhVien->HoTen = $_POST['HoTen'];
            $this->sinhVien->GioiTinh = $_POST['GioiTinh'];
            $this->sinhVien->NgaySinh = $_POST['NgaySinh'];
            $this->sinhVien->MaNganh = $_POST['MaNganh'];
            
            // Keep the current image unless a new one is uploaded
            $this->sinhVien->Hinh = $current_image;
            
            // Handle image upload
            if(isset($_FILES['Hinh']) && $_FILES['Hinh']['error'] == 0) {
                $target_dir = "app/public/images/";
                $file_extension = pathinfo($_FILES["Hinh"]["name"], PATHINFO_EXTENSION);
                $new_filename = uniqid() . '.' . $file_extension;
                $target_file = $target_dir . $new_filename;
                
                // Check if directory exists, if not create it
                if(!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                // Move uploaded file to target directory
                if(move_uploaded_file($_FILES["Hinh"]["tmp_name"], $target_file)) {
                    $this->sinhVien->Hinh = $target_dir . $new_filename;
                }
            }
            
            // Update student
            if($this->sinhVien->update()) {
                // Redirect to student details
                header('Location: index.php?controller=sinhvien&action=show&id=' . $this->sinhVien->MaSV);
                exit;
            } else {
                // Get all majors for dropdown
                $stmt = $this->nganhHoc->getAll();
                $majors = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $error = "Failed to update student. Please try again.";
                include 'app/views/students/edit.php';
            }
        }
    }
    
    // Process request to delete student
    public function delete() {
        if(isset($_GET['id'])) {
            // Set student ID
            $this->sinhVien->MaSV = $_GET['id'];
            
            // Delete student
            if($this->sinhVien->delete()) {
                // Redirect to student list
                header('Location: index.php?controller=sinhvien&action=index');
                exit;
            } else {
                // Failed to delete
                header('Location: index.php?controller=sinhvien&action=show&id=' . $this->sinhVien->MaSV);
                exit;
            }
        } else {
            // No ID provided
            header('Location: index.php?controller=sinhvien&action=index');
            exit;
        }
    }
} 