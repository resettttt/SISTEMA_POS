<?php
/**
 * Archivo de funciones de utilidad
 * Contiene funciones auxiliares para el sistema
 */

session_start();

/**
 * Verificar si el usuario está autenticado
 * @return bool
 */
function isAuthenticated() {
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
}

/**
 * Verificar si el usuario es administrador
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['user_rol_id']) && $_SESSION['user_rol_id'] === ROLE_ADMIN;
}

/**
 * Verificar si el usuario es trabajador
 * @return bool
 */
function isWorker() {
    return isset($_SESSION['user_rol_id']) && $_SESSION['user_rol_id'] === ROLE_WORKER;
}

/**
 * Verificar si el usuario es cliente
 * @return bool
 */
function isClient() {
    return isset($_SESSION['user_rol_id']) && $_SESSION['user_rol_id'] === ROLE_CLIENT;
}

/**
 * Redirigir si el usuario no está autenticado
 * @param string $redirect_url
 * @return void
 */
function requireAuth($redirect_url = '../views/auth/login.php') {
    if (!isAuthenticated()) {
        $_SESSION['error'] = 'Debe iniciar sesión para acceder a esta página.';
        header("Location: $redirect_url");
        exit();
    }
}

/**
 * Redirigir si el usuario no tiene permisos de administrador
 * @param string $redirect_url
 * @return void
 */
function requireAdmin($redirect_url = '../views/auth/login.php') {
    requireAuth($redirect_url);
    if (!isAdmin()) {
        $_SESSION['error'] = 'Acceso denegado. Requiere permisos de administrador.';
        header("Location: $redirect_url");
        exit();
    }
}

/**
 * Redirigir si el usuario no tiene permisos de trabajador
 * @param string $redirect_url
 * @return void
 */
function requireWorker($redirect_url = '../views/auth/login.php') {
    requireAuth($redirect_url);
    if (!isWorker() && !isAdmin()) { // Los administradores también pueden acceder a áreas de trabajador
        $_SESSION['error'] = 'Acceso denegado. Requiere permisos de trabajador.';
        header("Location: $redirect_url");
        exit();
    }
}

/**
 * Redirigir si el usuario no tiene permisos de cliente
 * @param string $redirect_url
 * @return void
 */
function requireClient($redirect_url = '../views/auth/login.php') {
    requireAuth($redirect_url);
    if (!isClient() && !isAdmin() && !isWorker()) {
        $_SESSION['error'] = 'Acceso denegado. Requiere permisos de cliente.';
        header("Location: $redirect_url");
        exit();
    }
}

/**
 * Limpiar y escapar datos para prevenir XSS
 * @param string $data
 * @return string
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Verificar si hay un mensaje de error en sesión
 * @return string|null
 */
function getErrorMessage() {
    if (isset($_SESSION['error'])) {
        $error = $_SESSION['error'];
        unset($_SESSION['error']);
        return $error;
    }
    return null;
}

/**
 * Verificar si hay un mensaje de éxito en sesión
 * @return string|null
 */
function getSuccessMessage() {
    if (isset($_SESSION['success'])) {
        $success = $_SESSION['success'];
        unset($_SESSION['success']);
        return $success;
    }
    return null;
}