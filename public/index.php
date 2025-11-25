<?php
/**
 * Archivo principal del sistema
 * Maneja las rutas y redirecciones del sistema
 */

require_once '../config/config.php';
require_once '../utils/helpers.php';

// Verificar si se ha solicitado una acción específica
$action = $_GET['action'] ?? $_POST['action'] ?? 'default';

// Rutas para autenticación
if (strpos($_SERVER['REQUEST_URI'], 'AuthController.php') !== false) {
    require_once '../controllers/AuthController.php';
    $controller = new AuthController();
    
    switch ($action) {
        case 'login':
            $controller->processLogin();
            break;
        case 'verify_2fa':
            $controller->process2FA();
            break;
        case 'logout':
            $controller->logout();
            break;
        default:
            $controller->showLogin();
            break;
    }
    exit();
}

// Rutas para administrador
if (strpos($_SERVER['REQUEST_URI'], 'AdminController.php') !== false) {
    require_once '../controllers/AdminController.php';
    $controller = new AdminController();
    
    switch ($action) {
        case 'showUsers':
            $controller->showUsers();
            break;
        case 'showCreateUser':
            $controller->showCreateUser();
            break;
        case 'createUser':
            $controller->createUser();
            break;
        case 'showEditUser':
            $user_id = $_GET['user_id'] ?? null;
            if ($user_id) {
                $controller->showEditUser($user_id);
            } else {
                header('Location: ../views/admin/users.php');
            }
            break;
        case 'updateUser':
            $user_id = $_POST['user_id'] ?? $_GET['user_id'] ?? null;
            if ($user_id) {
                $controller->updateUser($user_id);
            } else {
                header('Location: ../views/admin/users.php');
            }
            break;
        case 'deleteUser':
            $user_id = $_GET['user_id'] ?? null;
            if ($user_id) {
                $controller->deleteUser($user_id);
            } else {
                header('Location: ../views/admin/users.php');
            }
            break;
        default:
            $controller->showDashboard();
            break;
    }
    exit();
}

// Rutas para trabajador
if (strpos($_SERVER['REQUEST_URI'], 'WorkerController.php') !== false) {
    require_once '../controllers/WorkerController.php';
    $controller = new WorkerController();
    
    switch ($action) {
        case 'showClients':
            $controller->showClients();
            break;
        case 'showEditClient':
            $client_id = $_GET['client_id'] ?? null;
            if ($client_id) {
                $controller->showEditClient($client_id);
            } else {
                header('Location: ../views/worker/clients.php');
            }
            break;
        case 'updateClient':
            $client_id = $_POST['client_id'] ?? $_GET['client_id'] ?? null;
            if ($client_id) {
                $controller->updateClient($client_id);
            } else {
                header('Location: ../views/worker/clients.php');
            }
            break;
        default:
            $controller->showDashboard();
            break;
    }
    exit();
}

// Rutas para cliente
if (strpos($_SERVER['REQUEST_URI'], 'ClientController.php') !== false) {
    require_once '../controllers/ClientController.php';
    $controller = new ClientController();
    
    switch ($action) {
        case 'processQuickSale':
            $controller->processQuickSale();
            break;
        default:
            $controller->showQuickSale();
            break;
    }
    exit();
}

// Si no se encuentra ninguna ruta específica, mostrar página de inicio
header('Location: ../views/auth/login.php');
exit();