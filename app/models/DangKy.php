<?php
class DangKy {
    private $conn;
    private $table_name = "DangKy";
    private $table_detail = "ChiTietDangKy";

    public $MaDK;
    public $NgayDK;
    public $MaSV;
    public $courses = array(); // Array of course IDs for registration
    public $unavailableCourses = array(); // Array to track courses without available slots

    public function __construct($db) {
        $this->conn = $db;
    }

    // Check if student already registered for specific courses
    public function checkDuplicateCourses() {
        $duplicates = array();
        
        if (!empty($this->courses)) {
            foreach ($this->courses as $course_id) {
                $query = "SELECT hp.MaHP, hp.TenHP 
                         FROM " . $this->table_detail . " ct
                         JOIN " . $this->table_name . " dk ON ct.MaDK = dk.MaDK
                         JOIN HocPhan hp ON ct.MaHP = hp.MaHP
                         WHERE dk.MaSV = ? AND ct.MaHP = ?";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(1, $this->MaSV);
                $stmt->bindParam(2, $course_id);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $duplicates[] = $row;
                }
            }
        }
        
        return $duplicates;
    }
    
    // Check for available slots in selected courses
    public function checkAvailableSlots() {
        $this->unavailableCourses = array();
        
        if(!empty($this->courses)) {
            foreach($this->courses as $course_id) {
                $query = "SELECT MaHP, TenHP, SoLuongSV 
                         FROM HocPhan 
                         WHERE MaHP = ? AND SoLuongSV <= 0";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(1, $course_id);
                $stmt->execute();
                
                if($stmt->rowCount() > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $this->unavailableCourses[] = $row;
                }
            }
        }
        
        return $this->unavailableCourses;
    }

    // Create registration
    public function create() {
        try {
            // Start transaction
            $this->conn->beginTransaction();
            
            // First insert into DangKy table
            $query = "INSERT INTO " . $this->table_name . " SET NgayDK=:NgayDK, MaSV=:MaSV";
            
            $stmt = $this->conn->prepare($query);
            
            // Sanitize inputs
            $this->MaSV = htmlspecialchars(strip_tags($this->MaSV));
            
            // Bind parameters
            $stmt->bindParam(":NgayDK", $this->NgayDK);
            $stmt->bindParam(":MaSV", $this->MaSV);
            
            if(!$stmt->execute()) {
                $this->conn->rollBack();
                return false;
            }
            
            // Get the last inserted ID
            $this->MaDK = $this->conn->lastInsertId();
            
            // Then insert into ChiTietDangKy for each course
            if(!empty($this->courses)) {
                foreach($this->courses as $course_id) {
                    // First decrease student count in the course
                    $query = "UPDATE HocPhan SET SoLuongSV = SoLuongSV - 1 WHERE MaHP = :MaHP AND SoLuongSV > 0";
                    
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(":MaHP", $course_id);
                    
                    if(!$stmt->execute() || $stmt->rowCount() == 0) {
                        // If update failed or no rows were updated (count already 0), rollback
                        $this->conn->rollBack();
                        return false;
                    }
                    
                    // Now insert into ChiTietDangKy
                    $query = "INSERT INTO " . $this->table_detail . " SET MaDK=:MaDK, MaHP=:MaHP";
                    
                    $stmt = $this->conn->prepare($query);
                    
                    // Sanitize course ID
                    $course_id = htmlspecialchars(strip_tags($course_id));
                    
                    // Bind parameters
                    $stmt->bindParam(":MaDK", $this->MaDK);
                    $stmt->bindParam(":MaHP", $course_id);
                    
                    if(!$stmt->execute()) {
                        $this->conn->rollBack();
                        return false;
                    }
                }
            }
            
            // Commit transaction
            $this->conn->commit();
            return true;
            
        } catch(PDOException $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    // Get all registrations for a student
    public function getByStudent() {
        $query = "SELECT dk.*, sv.HoTen FROM " . $this->table_name . " dk
                 LEFT JOIN SinhVien sv ON dk.MaSV = sv.MaSV
                 WHERE dk.MaSV = ?
                 ORDER BY dk.NgayDK DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->MaSV);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Get courses for a registration
    public function getCourses() {
        $query = "SELECT hp.* FROM " . $this->table_detail . " ct
                 LEFT JOIN HocPhan hp ON ct.MaHP = hp.MaHP
                 WHERE ct.MaDK = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->MaDK);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Delete registration and increase student counts back
    public function delete() {
        try {
            // Start transaction
            $this->conn->beginTransaction();
            
            // Get courses for this registration to increase their counts later
            $query = "SELECT MaHP FROM " . $this->table_detail . " WHERE MaDK = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->MaDK);
            $stmt->execute();
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // First delete from ChiTietDangKy
            $query = "DELETE FROM " . $this->table_detail . " WHERE MaDK = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->MaDK);
            
            if(!$stmt->execute()) {
                $this->conn->rollBack();
                return false;
            }
            
            // Then delete from DangKy
            $query = "DELETE FROM " . $this->table_name . " WHERE MaDK = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->MaDK);
            
            if(!$stmt->execute()) {
                $this->conn->rollBack();
                return false;
            }
            
            // Increase student counts back for each course
            foreach($courses as $course) {
                $query = "UPDATE HocPhan SET SoLuongSV = SoLuongSV + 1 WHERE MaHP = :MaHP";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":MaHP", $course['MaHP']);
                $stmt->execute();
            }
            
            // Commit transaction
            $this->conn->commit();
            return true;
            
        } catch(PDOException $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Get courses that a student has already registered for
    public function getRegisteredCourses() {
        $query = "SELECT DISTINCT ct.MaHP, hp.TenHP
                 FROM " . $this->table_detail . " ct
                 JOIN " . $this->table_name . " dk ON ct.MaDK = dk.MaDK
                 JOIN HocPhan hp ON ct.MaHP = hp.MaHP
                 WHERE dk.MaSV = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->MaSV);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all registered courses for a student with full details
    public function getAllRegisteredCoursesWithDetails() {
        $query = "SELECT hp.MaHP, hp.TenHP, hp.SoTinChi, dk.MaDK, dk.NgayDK
                 FROM " . $this->table_detail . " ct
                 JOIN " . $this->table_name . " dk ON ct.MaDK = dk.MaDK
                 JOIN HocPhan hp ON ct.MaHP = hp.MaHP
                 WHERE dk.MaSV = ?
                 ORDER BY dk.NgayDK DESC, hp.TenHP ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->MaSV);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get total number of credits for a student
    public function getTotalCredits() {
        $query = "SELECT SUM(hp.SoTinChi) as TotalCredits
                 FROM " . $this->table_detail . " ct
                 JOIN " . $this->table_name . " dk ON ct.MaDK = dk.MaDK
                 JOIN HocPhan hp ON ct.MaHP = hp.MaHP
                 WHERE dk.MaSV = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->MaSV);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['TotalCredits'] : 0;
    }
    
    // Get total number of courses for a student
    public function getTotalCourses() {
        $query = "SELECT COUNT(DISTINCT ct.MaHP) as TotalCourses
                 FROM " . $this->table_detail . " ct
                 JOIN " . $this->table_name . " dk ON ct.MaDK = dk.MaDK
                 WHERE dk.MaSV = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->MaSV);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['TotalCourses'] : 0;
    }

    // Delete a single course from registration
    public function deleteCourse($maHP) {
        try {
            // Start transaction
            $this->conn->beginTransaction();
            
            // Check if the course exists in the registration
            $query = "SELECT COUNT(*) FROM " . $this->table_detail . " WHERE MaDK = ? AND MaHP = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->MaDK);
            $stmt->bindParam(2, $maHP);
            $stmt->execute();
            
            if($stmt->fetchColumn() == 0) {
                // Course not found in this registration
                $this->conn->rollBack();
                return false;
            }
            
            // Delete the course from this registration
            $query = "DELETE FROM " . $this->table_detail . " WHERE MaDK = ? AND MaHP = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->MaDK);
            $stmt->bindParam(2, $maHP);
            
            if(!$stmt->execute()) {
                $this->conn->rollBack();
                return false;
            }
            
            // Increase student count back for this course
            $query = "UPDATE HocPhan SET SoLuongSV = SoLuongSV + 1 WHERE MaHP = :MaHP";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":MaHP", $maHP);
            $stmt->execute();
            
            // Check if this registration has any courses left
            $query = "SELECT COUNT(*) FROM " . $this->table_detail . " WHERE MaDK = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->MaDK);
            $stmt->execute();
            
            if($stmt->fetchColumn() == 0) {
                // No courses left, delete the registration
                $query = "DELETE FROM " . $this->table_name . " WHERE MaDK = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(1, $this->MaDK);
                
                if(!$stmt->execute()) {
                    $this->conn->rollBack();
                    return false;
                }
            }
            
            // Commit transaction
            $this->conn->commit();
            return true;
            
        } catch(PDOException $e) {
            $this->conn->rollBack();
            return false;
        }
    }
} 