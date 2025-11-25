<?php
/**
 * Controlador de Trabajador
 * Maneja las operaciones específicas para usuarios con rol de trabajador
 */

require_once '../config/database.php';
require_once '../models/Worker.php';
require_once '../utils/helpers.php';

class WorkerController {
    
    private $database;
    private $db;
    private $worker;

    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
        $this->worker = new Worker($this->db);
    }

    /**
     * Mostrar dashboard de trabajador
     */
    public function showDashboard() {
        // Verificar si el usuario es trabajador
        if (!isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] !== ROLE_WORKER) {
            $_SESSION['error'] = 'Acceso denegado. Requiere permisos de trabajador.';
            header('Location: ../views/auth/login.php');
            exit();
        }
        
        include '../views/worker/dashboard.php';
    }

    /**
     * Mostrar lista de clientes
     */
    public function showClients() {
        // Verificar si el usuario es trabajador
        if (!isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] !== ROLE_WORKER) {
            $_SESSION['error'] = 'Acceso denegado. Requiere permisos de trabajador.';
            header('Location: ../views/auth/login.php');
            exit();
        }
        
        $clients = $this->worker->getAllClients();
        include '../views/worker/clients.php';
    }

    /**
     * Mostrar formulario para editar cliente
     */
    public function showEditClient($client_id) {
        // Verificar si el usuario es trabajador
        if (!isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] !== ROLE_WORKER) {
            $_SESSION['error'] = 'Acceso denegado. Requiere permisos de trabajador.';
            header('Location: ../views/auth/login.php');
            exit();
        }
        
        $client = $this->worker->getUserById($client_id);
        if (!$client) {
            $_SESSION['error'] = 'Cliente no encontrado.';
            header('Location: ../views/worker/clients.php');
            exit();
        }
        
        // Asegurarse de que sea un cliente
        if ($client['rol_id'] != ROLE_CLIENT) {
            $_SESSION['error'] = 'El usuario no es un cliente.';
            header('Location: ../views/worker/clients.php');
            exit();
        }
        
        include '../views/worker/edit_client.php';
    }

    /**
     * Actualizar un cliente
     */
    public function updateClient($client_id) {
        // Verificar si el usuario es trabajador
        if (!isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] !== ROLE_WORKER) {
            $_SESSION['error'] = 'Acceso denegado. Requiere permisos de trabajador.';
            header('Location: ../views/auth/login.php');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);

            // Validar inputs
            if (empty($nombre) || empty($email)) {
                $_SESSION['error'] = 'Por favor complete todos los campos.';
                header("Location: ../views/worker/edit_client.php?id=$client_id");
                exit();
            }

            $result = $this->worker->updateClient($client_id, $nombre, $email);

            if ($result) {
                $_SESSION['success'] = 'Cliente actualizado exitosamente.';
                header('Location: ../views/worker/clients.php');
            } else {
                $_SESSION['error'] = 'Error al actualizar cliente.';
                header("Location: ../views/worker/edit_client.php?id=$client_id");
            }
            exit();
        } else {
            header("Location: ../views/worker/edit_client.php?id=$client_id");
            exit();
        }
    }
}