<?php
require_once 'models/UserModel.php';
require_once 'models/SessionModel.php';
require_once 'models/TwoFactorModel.php';
require_once 'includes/view_helper.php';

class AuthController {
    private $userModel;
    private $sessionModel;
    private $twoFactorModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->sessionModel = new SessionModel();
        $this->twoFactorModel = new TwoFactorModel();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $_SESSION['error'] = 'Por favor ingrese usuario y contraseña';
                header('Location: ?controller=auth&action=login');
                exit;
            }

            $user = $this->userModel->getUserByUsername($username);

            if ($user && $this->userModel->validatePassword($user, $password)) {
                // Verificar si el usuario tiene 2FA habilitado
                if ($this->userModel->isTwoFactorEnabled($user['id'])) {
                    // Guardar datos temporales en sesión
                    $_SESSION['temp_user_id'] = $user['id'];
                    $_SESSION['temp_username'] = $user['username'];
                    $_SESSION['temp_role'] = $user['role'];
                    
                    // Generar código de 2FA
                    $code = $this->twoFactorModel->generateCode($user['id']);
                    
                    // Enviar código (en este ejemplo, simplemente lo mostramos - en la práctica se enviaría por correo)
                    $_SESSION['two_factor_code'] = $code; // Solo para propósitos de demostración
                    $_SESSION['two_factor_time'] = time();
                    
                    header('Location: ?controller=auth&action=verify_2fa');
                    exit;
                } else {
                    // Iniciar sesión directamente si no tiene 2FA
                    $this->completeLogin($user['id'], $user['role']);
                }
            } else {
                $_SESSION['error'] = 'Credenciales inválidas';
                header('Location: ?controller=auth&action=login');
                exit;
            }
        }

        // Mostrar formulario de login
        include 'views/auth/login.php';
    }

    public function verify_2fa() {
        // Verificar que el usuario haya pasado por el login
        if (!isset($_SESSION['temp_user_id'])) {
            header('Location: ?controller=auth&action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = $_POST['code'] ?? '';

            if (empty($code)) {
                $_SESSION['error'] = 'Por favor ingrese el código de verificación';
                header('Location: ?controller=auth&action=verify_2fa');
                exit;
            }

            $userId = $_SESSION['temp_user_id'];
            
            if ($this->twoFactorModel->validateCode($userId, $code)) {
                // Eliminar datos temporales
                $role = $_SESSION['temp_role'];
                unset($_SESSION['temp_user_id'], $_SESSION['temp_username'], $_SESSION['temp_role']);
                
                // Completar login
                $this->completeLogin($userId, $role);
            } else {
                $_SESSION['error'] = 'Código de verificación inválido o expirado';
                header('Location: ?controller=auth&action=verify_2fa');
                exit;
            }
        }

        // Mostrar formulario de verificación
        include 'views/auth/verify_2fa.php';
    }

    private function completeLogin($userId, $role) {
        // Generar token de sesión
        $sessionToken = bin2hex(random_bytes(32));
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // Crear sesión en la base de datos
        $this->sessionModel->createSession($userId, $sessionToken, $ipAddress, $userAgent);

        // Establecer cookie de sesión
        setcookie('session_token', $sessionToken, time() + (24 * 60 * 60), '/', '', true, true); // 24 horas, httponly

        // Guardar información del usuario en sesión
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_role'] = $role;

        // Redirigir según rol
        switch ($role) {
            case 'admin':
                header('Location: ?controller=dashboard&action=admin');
                break;
            case 'worker':
                header('Location: ?controller=dashboard&action=worker');
                break;
            default:
                header('Location: ?controller=dashboard&action=worker');
                break;
        }
        exit;
    }

    public function logout() {
        // Eliminar sesión de la base de datos
        if (isset($_COOKIE['session_token'])) {
            $this->sessionModel->deleteSession($_COOKIE['session_token']);
        }

        // Eliminar cookie de sesión
        setcookie('session_token', '', time() - 3600, '/', '', true, true);

        // Destruir sesión PHP
        session_destroy();

        // Redirigir al login
        header('Location: ?controller=auth&action=login');
        exit;
    }

    public function register() {
        // Verificar que el usuario sea admin para poder registrar usuarios
        $this->checkAuth('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'worker';
            $first_name = $_POST['first_name'] ?? '';
            $last_name = $_POST['last_name'] ?? '';
            $phone = $_POST['phone'] ?? '';

            if (empty($username) || empty($email) || empty($password) || empty($first_name) || empty($last_name)) {
                $_SESSION['error'] = 'Todos los campos obligatorios deben ser completados';
                header('Location: ?controller=auth&action=register');
                exit;
            }

            if ($password !== $_POST['confirm_password']) {
                $_SESSION['error'] = 'Las contraseñas no coinciden';
                header('Location: ?controller=auth&action=register');
                exit;
            }

            // Verificar si el usuario ya existe
            $existingUser = $this->userModel->getUserByUsername($username);
            if ($existingUser) {
                $_SESSION['error'] = 'El nombre de usuario ya existe';
                header('Location: ?controller=auth&action=register');
                exit;
            }

            // Crear el nuevo usuario
            if ($this->userModel->createUser($username, $email, $password, $role, $first_name, $last_name, $phone)) {
                $_SESSION['success'] = 'Usuario registrado exitosamente';
                header('Location: ?controller=auth&action=users');
                exit;
            } else {
                $_SESSION['error'] = 'Error al registrar el usuario';
                header('Location: ?controller=auth&action=register');
                exit;
            }
        }

        include 'views/auth/register.php';
    }

    public function users() {
        // Verificar que el usuario sea admin
        $this->checkAuth('admin');

        $users = $this->userModel->getAllUsers();
        include 'views/auth/users.php';
    }

    public function edit_user() {
        // Verificar que el usuario sea admin
        $this->checkAuth('admin');

        $userId = $_GET['id'] ?? 0;
        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            $_SESSION['error'] = 'Usuario no encontrado';
            header('Location: ?controller=auth&action=users');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $role = $_POST['role'] ?? 'worker';
            $first_name = $_POST['first_name'] ?? '';
            $last_name = $_POST['last_name'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            if (empty($username) || empty($email) || empty($first_name) || empty($last_name)) {
                $_SESSION['error'] = 'Todos los campos obligatorios deben ser completados';
                header("Location: ?controller=auth&action=edit_user&id=$userId");
                exit;
            }

            if ($this->userModel->updateUser($userId, $username, $email, $role, $first_name, $last_name, $phone, $is_active)) {
                $_SESSION['success'] = 'Usuario actualizado exitosamente';
                header('Location: ?controller=auth&action=users');
                exit;
            } else {
                $_SESSION['error'] = 'Error al actualizar el usuario';
                header("Location: ?controller=auth&action=edit_user&id=$userId");
                exit;
            }
        }

        include 'views/auth/edit_user.php';
    }

    public function delete_user() {
        // Verificar que el usuario sea admin
        $this->checkAuth('admin');

        $userId = $_GET['id'] ?? 0;

        if ($this->userModel->deleteUser($userId)) {
            $_SESSION['success'] = 'Usuario desactivado exitosamente';
        } else {
            $_SESSION['error'] = 'Error al desactivar el usuario';
        }

        header('Location: ?controller=auth&action=users');
        exit;
    }

    public function checkAuth($requiredRole = null) {
        // Limpiar sesiones expiradas
        $this->sessionModel->cleanupExpiredSessions();

        // Verificar si hay una cookie de sesión
        if (!isset($_COOKIE['session_token'])) {
            header('Location: ?controller=auth&action=login');
            exit;
        }

        // Verificar la validez del token de sesión
        $session = $this->sessionModel->getSessionByToken($_COOKIE['session_token']);
        
        if (!$session) {
            header('Location: ?controller=auth&action=login');
            exit;
        }

        // Actualizar sesión en $_SESSION
        $_SESSION['user_id'] = $session['user_id'];
        $_SESSION['user_role'] = $session['role'];

        // Verificar rol si se requiere
        if ($requiredRole && $session['role'] !== $requiredRole) {
            // Para trabajadores, permitir ciertas acciones
            if ($requiredRole === 'admin' && $session['role'] === 'worker') {
                $_SESSION['error'] = 'Acceso denegado. No tiene permisos suficientes.';
                header('Location: ?controller=dashboard&action=worker');
                exit;
            } else {
                header('Location: ?controller=auth&action=login');
                exit;
            }
        }

        return true;
    }
}
?>