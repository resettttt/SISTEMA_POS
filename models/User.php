<?php
/**
 * Modelo de Usuario
 * Clase que maneja la lógica de negocio para los usuarios
 */

require_once '../config/database.php';

class User {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $rol_id;
    public $activo;
    public $creado_en;
    public $modificado_en;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Verificar si el usuario existe con email y contraseña
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function login($email, $password) {
        $query = "SELECT id, nombre, email, password, rol_id, activo 
                  FROM " . $this->table_name . " 
                  WHERE email = ? AND activo = 1 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar la contraseña
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->nombre = $row['nombre'];
                $this->email = $row['email'];
                $this->rol_id = $row['rol_id'];
                $this->activo = $row['activo'];
                
                return $row;
            }
        }
        
        return false;
    }

    /**
     * Obtener información del usuario por ID
     * @param int $id
     * @return array|false
     */
    public function getUserById($id) {
        $query = "SELECT id, nombre, email, rol_id, activo, creado_en, modificado_en
                  FROM " . $this->table_name . " 
                  WHERE id = ? 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return false;
    }

    /**
     * Obtener todos los usuarios
     * @return array
     */
    public function getAllUsers() {
        $query = "SELECT u.id, u.nombre, u.email, u.rol_id, u.activo, u.creado_en, r.nombre as rol_nombre
                  FROM " . $this->table_name . " u
                  LEFT JOIN roles r ON u.rol_id = r.id
                  ORDER BY u.creado_en DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crear un nuevo usuario
     * @param string $nombre
     * @param string $email
     * @param string $password
     * @param int $rol_id
     * @return bool
     */
    public function createUser($nombre, $email, $password, $rol_id = ROLE_CLIENT) {
        // Verificar si el email ya existe
        if ($this->emailExists($email)) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  SET nombre=:nombre, email=:email, password=:password, rol_id=:rol_id";

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $nombre = htmlspecialchars(strip_tags($nombre));
        $email = htmlspecialchars(strip_tags($email));
        $rol_id = (int)$rol_id;

        // Hashear la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Vincular valores
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":rol_id", $rol_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Verificar si un email ya existe
     * @param string $email
     * @return bool
     */
    private function emailExists($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Actualizar un usuario existente
     * @param int $id
     * @param string $nombre
     * @param string $email
     * @param string $password (opcional)
     * @param int $rol_id
     * @return bool
     */
    public function updateUser($id, $nombre, $email, $password = null, $rol_id) {
        if ($password) {
            $query = "UPDATE " . $this->table_name . " 
                      SET nombre=:nombre, email=:email, password=:password, rol_id=:rol_id 
                      WHERE id = :id";
        } else {
            $query = "UPDATE " . $this->table_name . " 
                      SET nombre=:nombre, email=:email, rol_id=:rol_id 
                      WHERE id = :id";
        }

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $nombre = htmlspecialchars(strip_tags($nombre));
        $email = htmlspecialchars(strip_tags($email));
        $id = (int)$id;
        $rol_id = (int)$rol_id;

        // Vincular valores
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":rol_id", $rol_id);
        $stmt->bindParam(":id", $id);

        // Si hay contraseña, vincularla también
        if ($password) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindParam(":password", $hashed_password);
        }

        return $stmt->execute();
    }

    /**
     * Eliminar un usuario
     * @param int $id
     * @return bool
     */
    public function deleteUser($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    /**
     * Desactivar un usuario (soft delete)
     * @param int $id
     * @return bool
     */
    public function deactivateUser($id) {
        $query = "UPDATE " . $this->table_name . " SET activo = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }
}