<?php
require_once 'config/database.php';

class ProductModel {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    public function getAllProducts() {
        $sql = "SELECT * FROM products ORDER BY created_at DESC";
        $result = $this->conn->query($sql);
        
        $products = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        
        return $products;
    }
    
    public function getProductById($id) {
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    public function createProduct($name, $price, $stock, $category_id) {
        $sql = "INSERT INTO products (name, price, stock, category_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sdii", $name, $price, $stock, $category_id);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    public function updateProduct($id, $name, $price, $stock, $category_id) {
        $sql = "UPDATE products SET name = ?, price = ?, stock = ?, category_id = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sdiii", $name, $price, $stock, $category_id, $id);
        
        return $stmt->execute();
    }
    
    public function deleteProduct($id) {
        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
    
    public function searchProducts($keyword) {
        $sql = "SELECT * FROM products WHERE name LIKE ? OR description LIKE ?";
        $searchTerm = "%$keyword%";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        
        return $products;
    }
}
?>