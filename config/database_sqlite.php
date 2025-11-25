<?php
/**
 * Archivo de configuración de la base de datos SQLite
 * Alternativa ligera para desarrollo local
 */

require_once 'config.php';

class Database {
    private $conn;

    /**
     * Obtener conexión a la base de datos SQLite
     * @return PDO|null
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            // Usar una base de datos SQLite en lugar de MySQL
            $this->conn = new PDO('sqlite:/tmp/sistema_autenticacion.db');
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Error de conexión SQLite: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}

/**
 * Función para crear las tablas necesarias en la base de datos SQLite
 */
function createTables() {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        try {
            // Tabla de roles (primero porque es referenciada por usuarios)
            $query = "CREATE TABLE IF NOT EXISTS roles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT UNIQUE NOT NULL,
                descripcion TEXT
            )";
            $db->exec($query);
            
            // Insertar roles predeterminados
            $stmt = $db->prepare("INSERT OR IGNORE INTO roles (id, nombre, descripcion) VALUES (?, ?, ?)");
            $stmt->execute([ROLE_ADMIN, 'Administrador', 'Usuario con acceso total al sistema']);
            $stmt->execute([ROLE_WORKER, 'Trabajador', 'Usuario con permisos limitados']);
            $stmt->execute([ROLE_CLIENT, 'Cliente', 'Cliente ocasional']);
            
            // Tabla de usuarios
            $query = "CREATE TABLE IF NOT EXISTS usuarios (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                rol_id INTEGER NOT NULL DEFAULT 3,
                activo INTEGER DEFAULT 1,
                creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
                modificado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (rol_id) REFERENCES roles(id)
            )";
            $db->exec($query);
            
            // Tabla de 2FA
            $query = "CREATE TABLE IF NOT EXISTS two_factor_auth (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                usuario_id INTEGER NOT NULL,
                codigo TEXT NOT NULL,
                creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
                expira_en DATETIME NOT NULL,
                usado INTEGER DEFAULT 0,
                FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
            )";
            $db->exec($query);
            
            // Insertar usuarios de ejemplo
            $stmt = $db->prepare("INSERT OR IGNORE INTO usuarios (id, nombre, email, password, rol_id) VALUES (?, ?, ?, ?, ?)");
            $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
            $worker_password = password_hash('worker123', PASSWORD_DEFAULT);
            $client_password = password_hash('client123', PASSWORD_DEFAULT);
            
            $stmt->execute([1, 'Administrador', 'admin@ejemplo.com', $admin_password, ROLE_ADMIN]);
            $stmt->execute([2, 'Trabajador', 'worker@ejemplo.com', $worker_password, ROLE_WORKER]);
            $stmt->execute([3, 'Cliente', 'client@ejemplo.com', $client_password, ROLE_CLIENT]);
            
            echo "Tablas SQLite creadas exitosamente.";
        } catch (PDOException $e) {
            echo "Error al crear tablas SQLite: " . $e->getMessage();
        }
    }
}

// Si se ejecuta directamente este archivo, crear las tablas
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    createTables();
}