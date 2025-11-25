<?php
require_once 'controllers/BaseController.php';
require_once 'models/CustomerModel.php';

class CustomerController extends BaseController {
    private $customerModel;
    
    public function __construct() {
        $this->customerModel = new CustomerModel();
    }
    
    public function index() {
        $customers = $this->customerModel->getAllCustomers();
        
        $data = [
            'customers' => $customers
        ];
        
        $this->loadView('customer/index', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            
            $result = $this->customerModel->createCustomer($name, $email, $phone, $address);
            
            if ($result) {
                $_SESSION['message'] = "Customer created successfully!";
                $this->redirect('Customer', 'index');
            } else {
                $data['error'] = "Error creating customer!";
                $this->loadView('customer/create', $data);
            }
        } else {
            $this->loadView('customer/create');
        }
    }
    
    public function edit() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $customer = $this->customerModel->getCustomerById($id);
            
            if (!$customer) {
                $_SESSION['error'] = "Customer not found!";
                $this->redirect('Customer', 'index');
                return;
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name = $_POST['name'];
                $email = $_POST['email'];
                $phone = $_POST['phone'];
                $address = $_POST['address'];
                
                $result = $this->customerModel->updateCustomer($id, $name, $email, $phone, $address);
                
                if ($result) {
                    $_SESSION['message'] = "Customer updated successfully!";
                    $this->redirect('Customer', 'index');
                } else {
                    $data['error'] = "Error updating customer!";
                    $data['customer'] = $customer;
                    $this->loadView('customer/edit', $data);
                }
            } else {
                $data = [
                    'customer' => $customer
                ];
                
                $this->loadView('customer/edit', $data);
            }
        } else {
            $this->redirect('Customer', 'index');
        }
    }
    
    public function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            
            $result = $this->customerModel->deleteCustomer($id);
            
            if ($result) {
                $_SESSION['message'] = "Customer deleted successfully!";
            } else {
                $_SESSION['error'] = "Error deleting customer!";
            }
        }
        
        $this->redirect('Customer', 'index');
    }
    
    public function search() {
        if (isset($_GET['keyword'])) {
            $keyword = $_GET['keyword'];
            $customers = $this->customerModel->searchCustomers($keyword);
            
            $data = [
                'customers' => $customers,
                'keyword' => $keyword
            ];
            
            $this->loadView('customer/index', $data);
        } else {
            $this->redirect('Customer', 'index');
        }
    }
}
?>