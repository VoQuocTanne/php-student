<?php
require_once 'app/config/database.php';
require_once 'app/models/DangKy.php';
require_once 'app/models/SinhVien.php';
require_once 'app/models/HocPhan.php';

class DangKyController {
    private $db;
    private $dangKy;
    private $sinhVien;
    private $hocPhan;
    
    public function __construct() {
        // Initialize database connection
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Initialize models
        $this->dangKy = new DangKy($this->db);
        $this->sinhVien = new SinhVien($this->db);
        $this->hocPhan = new HocPhan($this->db);
    }
    
    // Display registration form for a student
    public function create() {
        if(isset($_GET['student_id'])) {
            // Get student details
            $this->sinhVien->MaSV = $_GET['student_id'];
            if($this->sinhVien->getSingleStudent()) {
                // Get all courses for selection
                $stmt = $this->hocPhan->getAll();
                $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get registered courses for this student to exclude
                $this->dangKy->MaSV = $_GET['student_id'];
                $registeredCourses = $this->dangKy->getRegisteredCourses();
                
                // Include view
                include 'app/views/registrations/create.php';
            } else {
                // Student not found
                header('Location: index.php?controller=sinhvien&action=index');
                exit;
            }
        } else {
            // No student ID provided
            header('Location: index.php?controller=sinhvien&action=index');
            exit;
        }
    }
    
    // Process form submission for creating registration
    public function store() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Set registration properties
            $this->dangKy->MaSV = $_POST['MaSV'];
            $this->dangKy->NgayDK = date('Y-m-d'); // Current date
            
            // Get selected courses
            if(isset($_POST['courses']) && is_array($_POST['courses'])) {
                $this->dangKy->courses = $_POST['courses'];
            } else {
                // No courses selected
                $this->sinhVien->MaSV = $_POST['MaSV'];
                $this->sinhVien->getSingleStudent();
                
                $stmt = $this->hocPhan->getAll();
                $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get registered courses for this student to exclude
                $this->dangKy->MaSV = $_POST['MaSV'];
                $registeredCourses = $this->dangKy->getRegisteredCourses();
                
                $error = "Vui lòng chọn ít nhất một học phần.";
                include 'app/views/registrations/create.php';
                return;
            }
            
            // Check for duplicate course registrations
            $duplicates = $this->dangKy->checkDuplicateCourses();
            if (!empty($duplicates)) {
                // Prepare error message
                $error = "Sinh viên đã đăng ký các học phần sau: <ul>";
                foreach ($duplicates as $dup) {
                    $error .= "<li>" . $dup['MaHP'] . " - " . $dup['TenHP'] . "</li>";
                }
                $error .= "</ul>Vui lòng chọn các học phần khác.";
                
                // Get student details
                $this->sinhVien->MaSV = $_POST['MaSV'];
                $this->sinhVien->getSingleStudent();
                
                // Get all courses for selection
                $stmt = $this->hocPhan->getAll();
                $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get registered courses for this student to exclude
                $registeredCourses = $this->dangKy->getRegisteredCourses();
                
                include 'app/views/registrations/create.php';
                return;
            }
            
            // Check for available slots in courses
            $unavailableCourses = $this->dangKy->checkAvailableSlots();
            if (!empty($unavailableCourses)) {
                // Prepare error message
                $error = "Các học phần sau đã hết chỗ: <ul>";
                foreach ($unavailableCourses as $course) {
                    $error .= "<li>" . $course['MaHP'] . " - " . $course['TenHP'] . "</li>";
                }
                $error .= "</ul>Vui lòng chọn các học phần khác.";
                
                // Get student details
                $this->sinhVien->MaSV = $_POST['MaSV'];
                $this->sinhVien->getSingleStudent();
                
                // Get all courses for selection
                $stmt = $this->hocPhan->getAll();
                $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get registered courses for this student to exclude
                $registeredCourses = $this->dangKy->getRegisteredCourses();
                
                include 'app/views/registrations/create.php';
                return;
            }
            
            // Create registration
            if($this->dangKy->create()) {
                // Redirect to student details
                header('Location: index.php?controller=sinhvien&action=show&id=' . $this->dangKy->MaSV);
                exit;
            } else {
                // Get student details
                $this->sinhVien->MaSV = $_POST['MaSV'];
                $this->sinhVien->getSingleStudent();
                
                // Get all courses for selection
                $stmt = $this->hocPhan->getAll();
                $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get registered courses for this student to exclude
                $registeredCourses = $this->dangKy->getRegisteredCourses();
                
                $error = "Không thể đăng ký học phần. Vui lòng thử lại.";
                include 'app/views/registrations/create.php';
            }
        }
    }
    
    // Display registrations for a student
    public function index() {
        if(isset($_GET['student_id'])) {
            // Get student details
            $this->sinhVien->MaSV = $_GET['student_id'];
            if($this->sinhVien->getSingleStudent()) {
                // Set student ID for registration model
                $this->dangKy->MaSV = $_GET['student_id'];
                
                // Get all registered courses with details
                $allCourses = $this->dangKy->getAllRegisteredCoursesWithDetails();
                
                // Get total credits and course count
                $totalCredits = $this->dangKy->getTotalCredits();
                $totalCourses = $this->dangKy->getTotalCourses();
                
                // Get registrations info for grouping by registration date if needed
                $stmt = $this->dangKy->getByStudent();
                $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get course details for each registration (keeping for backward compatibility)
                $registrationsWithCourses = array();
                foreach($registrations as $registration) {
                    $this->dangKy->MaDK = $registration['MaDK'];
                    $courseStmt = $this->dangKy->getCourses();
                    $courses = $courseStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $registration['courses'] = $courses;
                    $registrationsWithCourses[] = $registration;
                }
                
                // Include view
                include 'app/views/registrations/index.php';
            } else {
                // Student not found
                header('Location: index.php?controller=sinhvien&action=index');
                exit;
            }
        } else {
            // No student ID provided
            header('Location: index.php?controller=sinhvien&action=index');
            exit;
        }
    }
    
    // Display details of a registration
    public function show() {
        if(isset($_GET['id'])) {
            // Set registration ID
            $this->dangKy->MaDK = $_GET['id'];
            
            // Get courses for this registration
            $stmt = $this->dangKy->getCourses();
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Include view
            include 'app/views/registrations/show.php';
        } else {
            // No ID provided
            header('Location: index.php?controller=sinhvien&action=index');
            exit;
        }
    }
    
    // Process request to delete registration
    public function delete() {
        if(isset($_GET['id']) && isset($_GET['student_id'])) {
            // Set registration ID
            $this->dangKy->MaDK = $_GET['id'];
            $student_id = $_GET['student_id'];
            
            // Check if we're deleting a single course or entire registration
            if(isset($_GET['course_only']) && !empty($_GET['course_only'])) {
                // Delete single course from registration
                if($this->dangKy->deleteCourse($_GET['course_only'])) {
                    // Redirect to registrations list
                    header('Location: index.php?controller=dangky&action=index&student_id=' . $student_id);
                    exit;
                } else {
                    // Failed to delete course
                    header('Location: index.php?controller=dangky&action=index&student_id=' . $student_id . '&error=delete_course_failed');
                    exit;
                }
            } else {
                // Delete entire registration
                if($this->dangKy->delete()) {
                    // Redirect to registrations list
                    header('Location: index.php?controller=dangky&action=index&student_id=' . $student_id);
                    exit;
                } else {
                    // Failed to delete
                    header('Location: index.php?controller=dangky&action=show&id=' . $this->dangKy->MaDK);
                    exit;
                }
            }
        } else {
            // No IDs provided
            header('Location: index.php?controller=sinhvien&action=index');
            exit;
        }
    }
} 