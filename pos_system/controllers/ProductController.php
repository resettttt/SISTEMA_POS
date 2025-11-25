<?php
require_once 'controllers/BaseController.php';
require_once 'models/ProductModel.php';
require_once 'models/CategoryModel.php';

class ProductController extends BaseController {
    private $productModel;
    private $categoryModel;
    
    public function __construct() {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }
    
    public function index() {
        $products = $this->productModel->getAllProducts();
        $categories = $this->categoryModel->getAllCategories();
        
        $data = [
            'products' => $products,
            'categories' => $categories
        ];
        
        $this->loadView('product/index', $data);
    }
    
    public function create() {
        $categories = $this->categoryModel->getAllCategories();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $price = $_POST['price'];
            $stock = $_POST['stock'];
            $category_id = $_POST['category_id'];
            
            $result = $this->productModel->createProduct($name, $price, $stock, $category_id);
            
            if ($result) {
                $_SESSION['message'] = "Product created successfully!";
                $this->redirect('Product', 'index');
            } else {
                $data['error'] = "Error creating product!";
                $data['categories'] = $categories;
                $this->loadView('product/create', $data);
            }
        } else {
            $data = [
                'categories' => $categories
            ];
            
            $this->loadView('product/create', $data);
        }
    }
    
    public function edit() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $product = $this->productModel->getProductById($id);
            $categories = $this->categoryModel->getAllCategories();
            
            if (!$product) {
                $_SESSION['error'] = "Product not found!";
                $this->redirect('Product', 'index');
                return;
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name = $_POST['name'];
                $price = $_POST['price'];
                $stock = $_POST['stock'];
                $category_id = $_POST['category_id'];
                
                $result = $this->productModel->updateProduct($id, $name, $price, $stock, $category_id);
                
                if ($result) {
                    $_SESSION['message'] = "Product updated successfully!";
                    $this->redirect('Product', 'index');
                } else {
                    $data['error'] = "Error updating product!";
                    $data['product'] = $product;
                    $data['categories'] = $categories;
                    $this->loadView('product/edit', $data);
                }
            } else {
                $data = [
                    'product' => $product,
                    'categories' => $categories
                ];
                
                $this->loadView('product/edit', $data);
            }
        } else {
            $this->redirect('Product', 'index');
        }
    }
    
    public function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            
            $result = $this->productModel->deleteProduct($id);
            
            if ($result) {
                $_SESSION['message'] = "Product deleted successfully!";
            } else {
                $_SESSION['error'] = "Error deleting product!";
            }
        }
        
        $this->redirect('Product', 'index');
    }
    
    public function search() {
        if (isset($_GET['keyword'])) {
            $keyword = $_GET['keyword'];
            $products = $this->productModel->searchProducts($keyword);
            $categories = $this->categoryModel->getAllCategories();
            
            $data = [
                'products' => $products,
                'categories' => $categories,
                'keyword' => $keyword
            ];
            
            $this->loadView('product/index', $data);
        } else {
            $this->redirect('Product', 'index');
        }
    }
}
?>