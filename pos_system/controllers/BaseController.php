<?php
require_once 'includes/view_helper.php';

class BaseController {
    
    protected function loadView($viewName, $data = [], $title = 'POS System') {
        $viewPath = 'views/' . $viewName . '.php';
        
        if (file_exists($viewPath)) {
            renderView($viewName, $data, $title);
        } else {
            echo "View file '$viewPath' does not exist";
        }
    }
    
    protected function redirect($controller, $action, $params = []) {
        $url = '?controller=' . $controller . '&action=' . $action;
        
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $url .= '&' . $key . '=' . $value;
            }
        }
        
        header('Location: ' . $url);
        exit();
    }
}
?>