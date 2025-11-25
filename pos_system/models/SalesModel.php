<?php
require_once 'config/database.php';

class SalesModel {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    public function getAllSales() {
        $sql = "SELECT s.*, c.name as customer_name FROM sales s LEFT JOIN customers c ON s.customer_id = c.id ORDER BY s.created_at DESC";
        $result = $this->conn->query($sql);
        
        $sales = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $sales[] = $row;
            }
        }
        
        return $sales;
    }
    
    public function getSaleById($id) {
        $sql = "SELECT s.*, c.name as customer_name FROM sales s LEFT JOIN customers c ON s.customer_id = c.id WHERE s.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    public function createSale($customer_id, $total_amount, $payment_method, $discount = 0) {
        $sql = "INSERT INTO sales (customer_id, total_amount, payment_method, discount) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("idid", $customer_id, $total_amount, $payment_method, $discount);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    public function addSaleItem($sale_id, $product_id, $quantity, $price) {
        $sql = "INSERT INTO sale_items (sale_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiid", $sale_id, $product_id, $quantity, $price);
        
        return $stmt->execute();
    }
    
    public function getSaleItems($sale_id) {
        $sql = "SELECT si.*, p.name as product_name FROM sale_items si JOIN products p ON si.product_id = p.id WHERE si.sale_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $sale_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        
        return $items;
    }
}
?>