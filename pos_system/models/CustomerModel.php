<?php
require_once 'config/database.php';

class CustomerModel {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    public function getAllCustomers() {
        $sql = "SELECT * FROM customers ORDER BY name";
        $result = $this->conn->query($sql);
        
        $customers = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $customers[] = $row;
            }
        }
        
        return $customers;
    }
    
    public function getCustomerById($id) {
        $sql = "SELECT * FROM customers WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    public function createCustomer($name, $email, $phone, $address) {
        $sql = "INSERT INTO customers (name, email, phone, address) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $phone, $address);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    public function updateCustomer($id, $name, $email, $phone, $address) {
        $sql = "UPDATE customers SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $phone, $address, $id);
        
        return $stmt->execute();
    }
    
    public function deleteCustomer($id) {
        $sql = "DELETE FROM customers WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
    
    public function searchCustomers($keyword) {
        $sql = "SELECT * FROM customers WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?";
        $searchTerm = "%$keyword%";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $customers = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $customers[] = $row;
            }
        }
        
        return $customers;
    }
}
?>