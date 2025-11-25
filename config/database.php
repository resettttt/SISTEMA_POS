<?php
/**
 * Archivo de configuración de la base de datos
 * Contiene la conexión a la base de datos y la clase Database
 */

require_once 'config.php';

class Database {
    private $host = 'localhost';
    private $db_name = 'sistema_autenticacion';
    private $username = 'root';
    private $password = '';
    private $conn;

    /**
     * Obtener conexión a la base de datos
     * @return PDO|null
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}

/**
 * Función para crear las tablas necesarias en la base de datos
 */
function createTables() {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        try {
            // Tabla de usuarios
            $query = "CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                rol_id INT NOT NULL DEFAULT 3,
                activo BOOLEAN DEFAULT TRUE,
                creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                modificado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (rol_id) REFERENCES roles(id)
            )";
            $db->exec($query);
            
            // Tabla de roles
            $query = "CREATE TABLE IF NOT EXISTS roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(50) UNIQUE NOT NULL,
                descripcion TEXT
            )";
            $db->exec($query);
            
            // Insertar roles predeterminados
            $stmt = $db->prepare("INSERT IGNORE INTO roles (id, nombre, descripcion) VALUES (?, ?, ?)");
            $stmt->execute([ROLE_ADMIN, 'Administrador', 'Usuario con acceso total al sistema']);
            $stmt->execute([ROLE_WORKER, 'Trabajador', 'Usuario con permisos limitados']);
            $stmt->execute([ROLE_CLIENT, 'Cliente', 'Cliente ocasional']);
            
            // Tabla de 2FA
            $query = "CREATE TABLE IF NOT EXISTS two_factor_auth (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL,
                codigo VARCHAR(10) NOT NULL,
                creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                expira_en TIMESTAMP NOT NULL,
                usado BOOLEAN DEFAULT FALSE,
                FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
            )";
            $db->exec($query);
            
            echo "Tablas creadas exitosamente.";
        } catch (PDOException $e) {
            echo "Error al crear tablas: " . $e->getMessage();
        }
    }
}

// Si se ejecuta directamente este archivo, crear las tablas
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    createTables();
}