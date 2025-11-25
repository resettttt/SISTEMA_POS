<?php
// Main entry point for the POS system
require_once 'config/database.php';

// Initialize session
session_start();

// Define the base URL
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']));

// Route handling
$controller = 'ProductController';
$action = 'index';
$params = [];

if (isset($_GET['controller']) && !empty($_GET['controller'])) {
    $controller = ucfirst(strtolower($_GET['controller'])) . 'Controller';
}

if (isset($_GET['action']) && !empty($_GET['action'])) {
    $action = strtolower($_GET['action']);
}

// Include the controller file
$controllerFile = 'controllers/' . $controller . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    // Create an instance of the controller
    $controllerInstance = new $controller();
    
    // Call the action method
    if (method_exists($controllerInstance, $action)) {
        call_user_func_array([$controllerInstance, $action], $params);
    } else {
        echo "Action '$action' does not exist in '$controller'";
    }
} else {
    echo "Controller file '$controllerFile' does not exist";
}
?>