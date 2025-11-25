<?php
require_once 'controllers/BaseController.php';
require_once 'models/CategoryModel.php';

class CategoryController extends BaseController {
    private $categoryModel;
    
    public function __construct() {
        $this->categoryModel = new CategoryModel();
    }
    
    public function index() {
        $categories = $this->categoryModel->getAllCategories();
        
        $data = [
            'categories' => $categories
        ];
        
        $this->loadView('category/index', $data);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            
            $result = $this->categoryModel->createCategory($name, $description);
            
            if ($result) {
                $_SESSION['message'] = "Category created successfully!";
                $this->redirect('Category', 'index');
            } else {
                $data['error'] = "Error creating category!";
                $this->loadView('category/create', $data);
            }
        } else {
            $this->loadView('category/create');
        }
    }
    
    public function edit() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $category = $this->categoryModel->getCategoryById($id);
            
            if (!$category) {
                $_SESSION['error'] = "Category not found!";
                $this->redirect('Category', 'index');
                return;
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name = $_POST['name'];
                $description = $_POST['description'];
                
                $result = $this->categoryModel->updateCategory($id, $name, $description);
                
                if ($result) {
                    $_SESSION['message'] = "Category updated successfully!";
                    $this->redirect('Category', 'index');
                } else {
                    $data['error'] = "Error updating category!";
                    $data['category'] = $category;
                    $this->loadView('category/edit', $data);
                }
            } else {
                $data = [
                    'category' => $category
                ];
                
                $this->loadView('category/edit', $data);
            }
        } else {
            $this->redirect('Category', 'index');
        }
    }
    
    public function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            
            $result = $this->categoryModel->deleteCategory($id);
            
            if ($result) {
                $_SESSION['message'] = "Category deleted successfully!";
            } else {
                $_SESSION['error'] = "Error deleting category!";
            }
        }
        
        $this->redirect('Category', 'index');
    }
}
?>