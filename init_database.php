<?php
/**
 * Archivo para inicializar la base de datos
 * Crea las tablas necesarias y agrega datos de ejemplo
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Admin.php';

// Crear tablas
echo "Creando tablas...\n";
createTables();

// Conectar a la base de datos
$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "Conexión a la base de datos exitosa.\n";
    
    // Crear usuarios de ejemplo
    $admin = new Admin($db);
    
    // Crear administrador por defecto
    if ($admin->createAdmin("Administrador", "admin@ejemplo.com", "admin123")) {
        echo "Usuario administrador creado: admin@ejemplo.com / admin123\n";
    } else {
        echo "Error al crear usuario administrador o ya existe\n";
    }
    
    // Crear trabajador por defecto
    if ($admin->createWorker("Trabajador", "worker@ejemplo.com", "worker123")) {
        echo "Usuario trabajador creado: worker@ejemplo.com / worker123\n";
    } else {
        echo "Error al crear usuario trabajador o ya existe\n";
    }
    
    // Crear cliente por defecto
    if ($admin->createClient("Cliente", "client@ejemplo.com", "client123")) {
        echo "Usuario cliente creado: client@ejemplo.com / client123\n";
    } else {
        echo "Error al crear usuario cliente o ya existe\n";
    }
    
    echo "Base de datos inicializada correctamente.\n";
} else {
    echo "Error al conectar a la base de datos.\n";
}