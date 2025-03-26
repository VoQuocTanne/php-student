<?php
class NganhHoc {
    private $conn;
    private $table_name = "NganhHoc";

    public $MaNganh;
    public $TenNganh;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all majors
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY MaNganh";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Get single major
    public function getSingleMajor() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE MaNganh = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->MaNganh);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->MaNganh = $row['MaNganh'];
            $this->TenNganh = $row['TenNganh'];
            return true;
        }
        
        return false;
    }
} 