<?php
require_once 'models/UserModel.php';
require_once 'models/ProductModel.php';
require_once 'models/CustomerModel.php';
require_once 'models/SalesModel.php';
require_once 'includes/view_helper.php';

class DashboardController {
    private $userModel;
    private $productModel;
    private $customerModel;
    private $salesModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->productModel = new ProductModel();
        $this->customerModel = new CustomerModel();
        $this->salesModel = new SalesModel();
    }

    public function admin() {
        // Verificar autenticación y rol de admin
        $authController = new AuthController();
        $authController->checkAuth('admin');

        // Obtener métricas para el dashboard
        $totalProducts = $this->productModel->getTotalProducts();
        $totalCustomers = $this->customerModel->getTotalCustomers();
        $totalSales = $this->salesModel->getTotalSales();
        $recentSales = $this->salesModel->getRecentSales(5);

        include 'views/dashboard/admin.php';
    }

    public function worker() {
        // Verificar autenticación y rol de worker
        $authController = new AuthController();
        $authController->checkAuth(); // Cualquier rol autenticado puede acceder

        // Verificar que el rol sea worker o admin
        if ($_SESSION['user_role'] !== 'worker' && $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = 'Acceso denegado';
            header('Location: ?controller=auth&action=login');
            exit;
        }

        // Obtener métricas para el dashboard de trabajador
        $totalProducts = $this->productModel->getTotalProducts();
        $totalCustomers = $this->customerModel->getTotalCustomers();
        $totalSales = $this->salesModel->getTotalSales();
        $recentSales = $this->salesModel->getRecentSales(5);

        include 'views/dashboard/worker.php';
    }

    public function customer() {
        // Los clientes casuales no necesitan autenticación
        // Esta es una vista pública para ventas rápidas
        include 'views/dashboard/customer.php';
    }
}
?>