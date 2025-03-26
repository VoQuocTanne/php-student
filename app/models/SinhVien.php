<?php
class SinhVien {
    private $conn;
    private $table_name = "SinhVien";

    public $MaSV;
    public $HoTen;
    public $GioiTinh;
    public $NgaySinh;
    public $Hinh;
    public $MaNganh;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all students
    public function getAll() {
        $query = "SELECT sv.*, nh.TenNganh 
                FROM " . $this->table_name . " sv
                LEFT JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh
                ORDER BY sv.MaSV";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Get single student
    public function getSingleStudent() {
        $query = "SELECT sv.*, nh.TenNganh 
                FROM " . $this->table_name . " sv
                LEFT JOIN NganhHoc nh ON sv.MaNganh = nh.MaNganh
                WHERE sv.MaSV = ?
                LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->MaSV);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->MaSV = $row['MaSV'];
            $this->HoTen = $row['HoTen'];
            $this->GioiTinh = $row['GioiTinh'];
            $this->NgaySinh = $row['NgaySinh'];
            $this->Hinh = $row['Hinh'];
            $this->MaNganh = $row['MaNganh'];
            return true;
        }
        
        return false;
    }
    
    // Create student
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET MaSV=:MaSV, HoTen=:HoTen, GioiTinh=:GioiTinh, 
                    NgaySinh=:NgaySinh, Hinh=:Hinh, MaNganh=:MaNganh";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->MaSV = htmlspecialchars(strip_tags($this->MaSV));
        $this->HoTen = htmlspecialchars(strip_tags($this->HoTen));
        $this->GioiTinh = htmlspecialchars(strip_tags($this->GioiTinh));
        $this->Hinh = htmlspecialchars(strip_tags($this->Hinh));
        $this->MaNganh = htmlspecialchars(strip_tags($this->MaNganh));
        
        // Bind parameters
        $stmt->bindParam(":MaSV", $this->MaSV);
        $stmt->bindParam(":HoTen", $this->HoTen);
        $stmt->bindParam(":GioiTinh", $this->GioiTinh);
        $stmt->bindParam(":NgaySinh", $this->NgaySinh);
        $stmt->bindParam(":Hinh", $this->Hinh);
        $stmt->bindParam(":MaNganh", $this->MaNganh);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Update student
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET HoTen=:HoTen, GioiTinh=:GioiTinh, NgaySinh=:NgaySinh,
                    Hinh=:Hinh, MaNganh=:MaNganh
                WHERE MaSV=:MaSV";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->MaSV = htmlspecialchars(strip_tags($this->MaSV));
        $this->HoTen = htmlspecialchars(strip_tags($this->HoTen));
        $this->GioiTinh = htmlspecialchars(strip_tags($this->GioiTinh));
        $this->Hinh = htmlspecialchars(strip_tags($this->Hinh));
        $this->MaNganh = htmlspecialchars(strip_tags($this->MaNganh));
        
        // Bind parameters
        $stmt->bindParam(":MaSV", $this->MaSV);
        $stmt->bindParam(":HoTen", $this->HoTen);
        $stmt->bindParam(":GioiTinh", $this->GioiTinh);
        $stmt->bindParam(":NgaySinh", $this->NgaySinh);
        $stmt->bindParam(":Hinh", $this->Hinh);
        $stmt->bindParam(":MaNganh", $this->MaNganh);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Delete student
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE MaSV = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->MaSV);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
} 