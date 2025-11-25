<?php
/**
 * Middleware de Autenticación
 * Verifica que el usuario esté autenticado antes de acceder a páginas protegidas
 */

require_once '../utils/helpers.php';

class AuthMiddleware {
    
    /**
     * Verificar si el usuario está autenticado
     * @param bool $requireAuth Indica si se requiere autenticación (por defecto true)
     * @param string $redirectUrl URL a la que redirigir si no está autenticado
     * @return bool
     */
    public static function handle($requireAuth = true, $redirectUrl = '../views/auth/login.php') {
        if ($requireAuth) {
            if (!isAuthenticated()) {
                $_SESSION['error'] = 'Debe iniciar sesión para acceder a esta página.';
                header("Location: $redirectUrl");
                exit();
            }
        }
        return true;
    }
    
    /**
     * Verificar si el usuario tiene el rol adecuado
     * @param int $requiredRole Rol requerido (ROLE_ADMIN, ROLE_WORKER, ROLE_CLIENT)
     * @param string $redirectUrl URL a la que redirigir si no tiene permisos
     * @return bool
     */
    public static function checkRole($requiredRole, $redirectUrl = '../views/auth/login.php') {
        self::handle(true, $redirectUrl); // Asegurar que esté autenticado primero
        
        $userRole = $_SESSION['user_rol_id'] ?? 0;
        
        // Los administradores pueden acceder a todo
        if ($userRole === ROLE_ADMIN) {
            return true;
        }
        
        // Verificar rol específico
        if ($userRole === $requiredRole) {
            return true;
        }
        
        // Verificar si es trabajador y se requiere trabajador (los admins también pueden)
        if ($requiredRole === ROLE_WORKER && ($userRole === ROLE_WORKER || $userRole === ROLE_ADMIN)) {
            return true;
        }
        
        // Verificar si es cliente y se requiere cliente (todos pueden acceder al área de cliente)
        if ($requiredRole === ROLE_CLIENT && ($userRole === ROLE_CLIENT || $userRole === ROLE_WORKER || $userRole === ROLE_ADMIN)) {
            return true;
        }
        
        $_SESSION['error'] = 'No tiene permisos suficientes para acceder a esta página.';
        header("Location: $redirectUrl");
        exit();
    }
}