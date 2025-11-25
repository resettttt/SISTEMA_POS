<?php
require_once 'config/database.php';

class CategoryModel {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    public function getAllCategories() {
        $sql = "SELECT * FROM categories ORDER BY name";
        $result = $this->conn->query($sql);
        
        $categories = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }
        
        return $categories;
    }
    
    public function getCategoryById($id) {
        $sql = "SELECT * FROM categories WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    public function createCategory($name, $description) {
        $sql = "INSERT INTO categories (name, description) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $name, $description);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    public function updateCategory($id, $name, $description) {
        $sql = "UPDATE categories SET name = ?, description = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $description, $id);
        
        return $stmt->execute();
    }
    
    public function deleteCategory($id) {
        $sql = "DELETE FROM categories WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
}
?>