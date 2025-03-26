<?php
class User {
    private $conn;
    private $table_name = "Users";

    public $id;
    public $username;
    public $password;
    public $name;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
        
        // Create Users table if it doesn't exist
        $this->createTableIfNotExists();
    }
    
    // Create Users table if not exists
    private function createTableIfNotExists() {
        $query = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(100) NOT NULL,
            role VARCHAR(20) NOT NULL DEFAULT 'student'
        )";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        // Check if admin user exists, if not create one
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = 'admin' LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        if($stmt->rowCount() == 0) {
            $query = "INSERT INTO " . $this->table_name . " 
                    SET username = 'admin', 
                        password = :password, 
                        name = 'Administrator', 
                        role = 'admin'";
            
            $stmt = $this->conn->prepare($query);
            
            // Hash the password - default is 'admin123'
            $password_hash = password_hash('admin123', PASSWORD_BCRYPT);
            $stmt->bindParam(":password", $password_hash);
            
            $stmt->execute();
        }
    }
    
    // Login user
    public function login() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->username);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->role = $row['role'];
                return true;
            }
        }
        
        return false;
    }
    
    // Create new user
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                SET username = :username, 
                    password = :password, 
                    name = :name, 
                    role = :role";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->role = htmlspecialchars(strip_tags($this->role));
        
        // Hash the password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        
        // Bind parameters
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":role", $this->role);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
} 