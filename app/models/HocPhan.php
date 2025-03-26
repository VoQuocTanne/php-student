<?php
class HocPhan {
    private $conn;
    private $table_name = "HocPhan";

    public $MaHP;
    public $TenHP;
    public $SoTinChi;
    public $SoLuongSV; // New field to track student limit

    public function __construct($db) {
        $this->conn = $db;
        
        // Create SoLuongSV column if it doesn't exist
        $this->createSoLuongSVColumnIfNotExists();
    }
    
    // Create SoLuongSV column if it doesn't exist
    private function createSoLuongSVColumnIfNotExists() {
        try {
            $query = "SHOW COLUMNS FROM " . $this->table_name . " LIKE 'SoLuongSV'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            if($stmt->rowCount() == 0) {
                $query = "ALTER TABLE " . $this->table_name . " ADD COLUMN SoLuongSV INT DEFAULT 50 NOT NULL";
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                
                // Update existing records with default value
                $query = "UPDATE " . $this->table_name . " SET SoLuongSV = 50 WHERE SoLuongSV IS NULL";
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
            }
        } catch(PDOException $e) {
            // Silently handle error
        }
    }

    // Get all courses
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY MaHP";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Get single course
    public function getSingleCourse() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE MaHP = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->MaHP);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->MaHP = $row['MaHP'];
            $this->TenHP = $row['TenHP'];
            $this->SoTinChi = $row['SoTinChi'];
            $this->SoLuongSV = isset($row['SoLuongSV']) ? $row['SoLuongSV'] : 50; // Default to 50 if not set
            return true;
        }
        
        return false;
    }
    
    // Create course
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET MaHP=:MaHP, TenHP=:TenHP, SoTinChi=:SoTinChi, SoLuongSV=:SoLuongSV";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs and ensure they're not null
        $this->MaHP = isset($this->MaHP) ? htmlspecialchars(strip_tags($this->MaHP)) : '';
        $this->TenHP = isset($this->TenHP) ? htmlspecialchars(strip_tags($this->TenHP)) : '';
        $this->SoTinChi = isset($this->SoTinChi) ? htmlspecialchars(strip_tags($this->SoTinChi)) : 0;
        $this->SoLuongSV = isset($this->SoLuongSV) ? htmlspecialchars(strip_tags($this->SoLuongSV)) : 50;
        
        // Limit MaHP length to 10 characters to prevent database error
        $this->MaHP = substr($this->MaHP, 0, 10);
        
        // Bind parameters
        $stmt->bindParam(":MaHP", $this->MaHP);
        $stmt->bindParam(":TenHP", $this->TenHP);
        $stmt->bindParam(":SoTinChi", $this->SoTinChi);
        $stmt->bindParam(":SoLuongSV", $this->SoLuongSV);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Update course
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET TenHP=:TenHP, SoTinChi=:SoTinChi, SoLuongSV=:SoLuongSV WHERE MaHP=:MaHP";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs and ensure they're not null
        $this->MaHP = isset($this->MaHP) ? htmlspecialchars(strip_tags($this->MaHP)) : '';
        $this->TenHP = isset($this->TenHP) ? htmlspecialchars(strip_tags($this->TenHP)) : '';
        $this->SoTinChi = isset($this->SoTinChi) ? htmlspecialchars(strip_tags($this->SoTinChi)) : 0;
        $this->SoLuongSV = isset($this->SoLuongSV) ? htmlspecialchars(strip_tags($this->SoLuongSV)) : 50;
        
        // Limit MaHP length to 10 characters to prevent database error
        $this->MaHP = substr($this->MaHP, 0, 10);
        
        // Bind parameters
        $stmt->bindParam(":MaHP", $this->MaHP);
        $stmt->bindParam(":TenHP", $this->TenHP);
        $stmt->bindParam(":SoTinChi", $this->SoTinChi);
        $stmt->bindParam(":SoLuongSV", $this->SoLuongSV);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Decrease student count when a student registers
    public function decreaseStudentCount($course_id) {
        $query = "UPDATE " . $this->table_name . " SET SoLuongSV = SoLuongSV - 1 WHERE MaHP = :MaHP AND SoLuongSV > 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":MaHP", $course_id);
        
        return $stmt->execute();
    }
    
    // Check if a course has available slots
    public function hasAvailableSlots($course_id) {
        $query = "SELECT SoLuongSV FROM " . $this->table_name . " WHERE MaHP = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $course_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row && $row['SoLuongSV'] > 0) {
            return true;
        }
        
        return false;
    }
    
    // Delete course
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE MaHP = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->MaHP);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
} 