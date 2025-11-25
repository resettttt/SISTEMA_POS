<?php
/**
 * Archivo de configuración general del sistema
 * Contiene las constantes y configuraciones generales
 */

// Configuración de la aplicación
define('APP_NAME', 'Sistema de Autenticación Avanzado');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/sistema_autenticacion');

// Configuración de la sesión
define('SESSION_NAME', 'sistema_auth');
define('SESSION_LIFETIME', 3600); // 1 hora

// Configuración de seguridad
define('HASH_ALGORITHM', 'sha256');
define('SALT_LENGTH', 16);

// Configuración de roles
define('ROLE_ADMIN', 1);
define('ROLE_WORKER', 2);
define('ROLE_CLIENT', 3);

// Configuración de 2FA
define('OTP_LENGTH', 6);
define('OTP_EXPIRATION', 300); // 5 minutos

// Incluir la configuración de la base de datos
require_once 'database_sqlite.php';