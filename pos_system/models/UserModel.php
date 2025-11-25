<?php
require_once 'config/database.php';

class UserModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createUser($username, $email, $password, $role, $first_name, $last_name, $phone = null) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO users (username, email, password, role, first_name, last_name, phone) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssss", $username, $email, $hashed_password, $role, $first_name, $last_name, $phone);
        
        return $stmt->execute();
    }

    public function getUserByUsername($username) {
        $query = "SELECT * FROM users WHERE username = ? AND is_active = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    public function getUserById($id) {
        $query = "SELECT id, username, email, role, first_name, last_name, phone FROM users WHERE id = ? AND is_active = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    public function validatePassword($user, $password) {
        return password_verify($password, $user['password']);
    }
    
    public function getAllUsers() {
        $query = "SELECT id, username, email, role, first_name, last_name, phone, is_active, created_at FROM users ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function updateUser($id, $username, $email, $role, $first_name, $last_name, $phone = null, $is_active = 1) {
        $query = "UPDATE users SET username = ?, email = ?, role = ?, first_name = ?, last_name = ?, phone = ?, is_active = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssssii", $username, $email, $role, $first_name, $last_name, $phone, $is_active, $id);
        
        return $stmt->execute();
    }
    
    public function deleteUser($id) {
        // Instead of deleting, we deactivate the user
        $query = "UPDATE users SET is_active = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
    
    public function updateTwoFactorSecret($userId, $secret) {
        $query = "UPDATE users SET two_factor_secret = ?, two_factor_enabled = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $secret, $userId);
        
        return $stmt->execute();
    }
    
    public function disableTwoFactor($userId) {
        $query = "UPDATE users SET two_factor_secret = NULL, two_factor_enabled = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        
        return $stmt->execute();
    }
    
    public function isTwoFactorEnabled($userId) {
        $query = "SELECT two_factor_enabled FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        return $user ? $user['two_factor_enabled'] : false;
    }
}
?>