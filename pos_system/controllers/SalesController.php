<?php
require_once 'controllers/BaseController.php';
require_once 'models/SalesModel.php';
require_once 'models/ProductModel.php';
require_once 'models/CustomerModel.php';

class SalesController extends BaseController {
    private $salesModel;
    private $productModel;
    private $customerModel;
    
    public function __construct() {
        $this->salesModel = new SalesModel();
        $this->productModel = new ProductModel();
        $this->customerModel = new CustomerModel();
    }
    
    public function index() {
        $sales = $this->salesModel->getAllSales();
        
        $data = [
            'sales' => $sales
        ];
        
        $this->loadView('sales/index', $data);
    }
    
    public function pos() {
        $products = $this->productModel->getAllProducts();
        $customers = $this->customerModel->getAllCustomers();
        
        $data = [
            'products' => $products,
            'customers' => $customers
        ];
        
        $this->loadView('sales/pos', $data);
    }
    
    public function create() {
        $products = $this->productModel->getAllProducts();
        $customers = $this->customerModel->getAllCustomers();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customer_id = $_POST['customer_id'] ?? null;
            $items = json_decode($_POST['items'], true);
            $total_amount = $_POST['total_amount'];
            $payment_method = $_POST['payment_method'];
            $discount = $_POST['discount'] ?? 0;
            
            // Create the sale
            $sale_id = $this->salesModel->createSale($customer_id, $total_amount, $payment_method, $discount);
            
            if ($sale_id) {
                // Add items to the sale
                foreach ($items as $item) {
                    $this->salesModel->addSaleItem($sale_id, $item['product_id'], $item['quantity'], $item['price']);
                    
                    // Update product stock
                    $product = $this->productModel->getProductById($item['product_id']);
                    $new_stock = $product['stock'] - $item['quantity'];
                    $this->productModel->updateProduct($item['product_id'], $product['name'], $product['price'], $new_stock, $product['category_id']);
                }
                
                $_SESSION['message'] = "Sale created successfully!";
                $this->redirect('Sales', 'receipt', ['id' => $sale_id]);
            } else {
                $data['error'] = "Error creating sale!";
                $this->loadView('sales/pos', $data);
            }
        } else {
            $this->loadView('sales/pos', $data);
        }
    }
    
    public function receipt() {
        if (isset($_GET['id'])) {
            $sale_id = $_GET['id'];
            $sale = $this->salesModel->getSaleById($sale_id);
            $items = $this->salesModel->getSaleItems($sale_id);
            
            $data = [
                'sale' => $sale,
                'items' => $items
            ];
            
            $this->loadView('sales/receipt', $data);
        } else {
            $this->redirect('Sales', 'index');
        }
    }
}
?>