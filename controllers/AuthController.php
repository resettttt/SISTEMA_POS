<?php
/**
 * Controlador de Autenticación
 * Maneja el login, logout y verificación de 2FA
 */

require_once '../config/database.php';
require_once '../models/User.php';
require_once '../models/TwoFactorAuth.php';
require_once '../utils/helpers.php';

class AuthController {
    
    private $database;
    private $db;
    private $user;
    private $twoFactorAuth;

    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
        $this->user = new User($this->db);
        $this->twoFactorAuth = new TwoFactorAuth($this->db);
    }

    /**
     * Mostrar formulario de login
     */
    public function showLogin() {
        include '../views/auth/login.php';
    }

    /**
     * Procesar el login
     */
    public function processLogin() {
        // Verificar si se enviaron los datos
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            // Validar inputs
            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'Por favor complete todos los campos.';
                header('Location: ../views/auth/login.php');
                exit();
            }

            // Intentar login
            $user_data = $this->user->login($email, $password);
            
            if ($user_data) {
                // Guardar información del usuario en sesión
                $_SESSION['user_id'] = $user_data['id'];
                $_SESSION['user_nombre'] = $user_data['nombre'];
                $_SESSION['user_email'] = $user_data['email'];
                $_SESSION['user_rol_id'] = $user_data['rol_id'];
                
                // Generar y enviar código OTP
                $otp = $this->twoFactorAuth->generateOTP();
                
                if ($this->twoFactorAuth->createOTP($user_data['id'], $otp)) {
                    $this->twoFactorAuth->sendOTP($email, $otp);
                    
                    // Redirigir a verificación de 2FA
                    $_SESSION['needs_2fa'] = true;
                    header('Location: ../views/auth/verify_2fa.php');
                    exit();
                } else {
                    $_SESSION['error'] = 'Error al generar el código de verificación.';
                    header('Location: ../views/auth/login.php');
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Credenciales incorrectas.';
                header('Location: ../views/auth/login.php');
                exit();
            }
        } else {
            header('Location: ../views/auth/login.php');
            exit();
        }
    }

    /**
     * Procesar verificación de 2FA
     */
    public function process2FA() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $otp = trim($_POST['otp']);
            $user_id = $_SESSION['user_id'];

            if (empty($otp)) {
                $_SESSION['error'] = 'Por favor ingrese el código de verificación.';
                header('Location: ../views/auth/verify_2fa.php');
                exit();
            }

            if ($this->twoFactorAuth->verifyOTP($user_id, $otp)) {
                // 2FA verificado exitosamente
                unset($_SESSION['needs_2fa']);
                $_SESSION['authenticated'] = true;
                
                // Redirigir según rol del usuario
                $this->redirectToDashboard();
            } else {
                $_SESSION['error'] = 'Código de verificación incorrecto o expirado.';
                header('Location: ../views/auth/verify_2fa.php');
                exit();
            }
        } else {
            header('Location: ../views/auth/verify_2fa.php');
            exit();
        }
    }

    /**
     * Redirigir al dashboard según el rol del usuario
     */
    private function redirectToDashboard() {
        $rol_id = $_SESSION['user_rol_id'];
        
        switch ($rol_id) {
            case ROLE_ADMIN:
                header('Location: ../views/admin/dashboard.php');
                break;
            case ROLE_WORKER:
                header('Location: ../views/worker/dashboard.php');
                break;
            case ROLE_CLIENT:
                header('Location: ../views/client/quick_sale.php');
                break;
            default:
                $_SESSION['error'] = 'Rol de usuario no válido.';
                header('Location: ../views/auth/login.php');
                break;
        }
        exit();
    }

    /**
     * Logout del sistema
     */
    public function logout() {
        // Destruir todas las variables de sesión
        session_unset();
        session_destroy();
        
        // Redirigir al login
        header('Location: ../views/auth/login.php');
        exit();
    }
}