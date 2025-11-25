<?php
/**
 * Controlador de Administrador
 * Maneja las operaciones específicas para usuarios con rol de administrador
 */

require_once '../config/database.php';
require_once '../models/Admin.php';
require_once '../utils/helpers.php';

class AdminController {
    
    private $database;
    private $db;
    private $admin;

    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
        $this->admin = new Admin($this->db);
    }

    /**
     * Mostrar dashboard de administrador
     */
    public function showDashboard() {
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] !== ROLE_ADMIN) {
            $_SESSION['error'] = 'Acceso denegado. Requiere permisos de administrador.';
            header('Location: ../views/auth/login.php');
            exit();
        }
        
        // Obtener estadísticas
        $total_users = count($this->admin->getAllUsers());
        $total_admins = count($this->admin->getAllAdmins());
        $total_workers = count($this->admin->getAllWorkers());
        $total_clients = count($this->admin->getAllClients());
        
        include '../views/admin/dashboard.php';
    }

    /**
     * Mostrar lista de usuarios
     */
    public function showUsers() {
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] !== ROLE_ADMIN) {
            $_SESSION['error'] = 'Acceso denegado. Requiere permisos de administrador.';
            header('Location: ../views/auth/login.php');
            exit();
        }
        
        $users = $this->admin->getAllUsers();
        include '../views/admin/users.php';
    }

    /**
     * Mostrar formulario para crear usuario
     */
    public function showCreateUser() {
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] !== ROLE_ADMIN) {
            $_SESSION['error'] = 'Acceso denegado. Requiere permisos de administrador.';
            header('Location: ../views/auth/login.php');
            exit();
        }
        
        include '../views/admin/create_user.php';
    }

    /**
     * Crear un nuevo usuario
     */
    public function createUser() {
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] !== ROLE_ADMIN) {
            $_SESSION['error'] = 'Acceso denegado. Requiere permisos de administrador.';
            header('Location: ../views/auth/login.php');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $rol_id = (int)$_POST['rol_id'];

            // Validar inputs
            if (empty($nombre) || empty($email) || empty($password) || empty($rol_id)) {
                $_SESSION['error'] = 'Por favor complete todos los campos.';
                header('Location: ../views/admin/create_user.php');
                exit();
            }

            // Validar rol
            if (!in_array($rol_id, [ROLE_ADMIN, ROLE_WORKER, ROLE_CLIENT])) {
                $_SESSION['error'] = 'Rol inválido.';
                header('Location: ../views/admin/create_user.php');
                exit();
            }

            // Determinar qué método usar según el rol
            $result = false;
            switch ($rol_id) {
                case ROLE_ADMIN:
                    $result = $this->admin->createAdmin($nombre, $email, $password);
                    break;
                case ROLE_WORKER:
                    $result = $this->admin->createWorker($nombre, $email, $password);
                    break;
                case ROLE_CLIENT:
                    $result = $this->admin->createClient($nombre, $email, $password);
                    break;
            }

            if ($result) {
                $_SESSION['success'] = 'Usuario creado exitosamente.';
                header('Location: ../views/admin/users.php');
            } else {
                $_SESSION['error'] = 'Error al crear usuario. El email puede estar en uso.';
                header('Location: ../views/admin/create_user.php');
            }
            exit();
        } else {
            header('Location: ../views/admin/create_user.php');
            exit();
        }
    }

    /**
     * Mostrar formulario para editar usuario
     */
    public function showEditUser($user_id) {
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] !== ROLE_ADMIN) {
            $_SESSION['error'] = 'Acceso denegado. Requiere permisos de administrador.';
            header('Location: ../views/auth/login.php');
            exit();
        }
        
        $user = $this->admin->getUserById($user_id);
        if (!$user) {
            $_SESSION['error'] = 'Usuario no encontrado.';
            header('Location: ../views/admin/users.php');
            exit();
        }
        
        include '../views/admin/edit_user.php';
    }

    /**
     * Actualizar un usuario
     */
    public function updateUser($user_id) {
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] !== ROLE_ADMIN) {
            $_SESSION['error'] = 'Acceso denegado. Requiere permisos de administrador.';
            header('Location: ../views/auth/login.php');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $rol_id = (int)$_POST['rol_id'];
            $password = !empty($_POST['password']) ? $_POST['password'] : null; // Contraseña opcional

            // Validar inputs
            if (empty($nombre) || empty($email) || empty($rol_id)) {
                $_SESSION['error'] = 'Por favor complete todos los campos obligatorios.';
                header("Location: ../views/admin/edit_user.php?id=$user_id");
                exit();
            }

            // Validar rol
            if (!in_array($rol_id, [ROLE_ADMIN, ROLE_WORKER, ROLE_CLIENT])) {
                $_SESSION['error'] = 'Rol inválido.';
                header("Location: ../views/admin/edit_user.php?id=$user_id");
                exit();
            }

            $result = $this->admin->updateUser($user_id, $nombre, $email, $password, $rol_id);

            if ($result) {
                $_SESSION['success'] = 'Usuario actualizado exitosamente.';
                header('Location: ../views/admin/users.php');
            } else {
                $_SESSION['error'] = 'Error al actualizar usuario.';
                header("Location: ../views/admin/edit_user.php?id=$user_id");
            }
            exit();
        } else {
            header("Location: ../views/admin/edit_user.php?id=$user_id");
            exit();
        }
    }

    /**
     * Eliminar un usuario
     */
    public function deleteUser($user_id) {
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['user_rol_id']) || $_SESSION['user_rol_id'] !== ROLE_ADMIN) {
            $_SESSION['error'] = 'Acceso denegado. Requiere permisos de administrador.';
            header('Location: ../views/auth/login.php');
            exit();
        }
        
        // No permitir que un administrador se elimine a sí mismo
        if ($user_id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'No puede eliminarse a sí mismo.';
            header('Location: ../views/admin/users.php');
            exit();
        }

        $result = $this->admin->deleteUser($user_id);

        if ($result) {
            $_SESSION['success'] = 'Usuario eliminado exitosamente.';
        } else {
            $_SESSION['error'] = 'Error al eliminar usuario.';
        }

        header('Location: ../views/admin/users.php');
        exit();
    }
}