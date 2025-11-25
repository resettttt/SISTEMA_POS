<?php
/**
 * Modelo de Administrador
 * Extiende la funcionalidad del modelo User con permisos específicos para administradores
 */

require_once '../config/database.php';
require_once 'User.php';

class Admin extends User {
    
    public function __construct($db) {
        parent::__construct($db);
    }

    /**
     * Verificar si el usuario es administrador
     * @param int $user_id
     * @return bool
     */
    public function isAdmin($user_id) {
        $query = "SELECT rol_id FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['rol_id'] == ROLE_ADMIN;
        }
        
        return false;
    }

    /**
     * Crear un nuevo usuario administrador
     * @param string $nombre
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function createAdmin($nombre, $email, $password) {
        return $this->createUser($nombre, $email, $password, ROLE_ADMIN);
    }

    /**
     * Crear un nuevo trabajador
     * @param string $nombre
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function createWorker($nombre, $email, $password) {
        return $this->createUser($nombre, $email, $password, ROLE_WORKER);
    }

    /**
     * Crear un nuevo cliente
     * @param string $nombre
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function createClient($nombre, $email, $password) {
        return $this->createUser($nombre, $email, $password, ROLE_CLIENT);
    }

    /**
     * Obtener todos los administradores
     * @return array
     */
    public function getAllAdmins() {
        $query = "SELECT u.id, u.nombre, u.email, u.rol_id, u.activo, u.creado_en
                  FROM " . $this->table_name . " u
                  WHERE u.rol_id = ?
                  ORDER BY u.creado_en DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, ROLE_ADMIN);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todos los trabajadores
     * @return array
     */
    public function getAllWorkers() {
        $query = "SELECT u.id, u.nombre, u.email, u.rol_id, u.activo, u.creado_en
                  FROM " . $this->table_name . " u
                  WHERE u.rol_id = ?
                  ORDER BY u.creado_en DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, ROLE_WORKER);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todos los clientes
     * @return array
     */
    public function getAllClients() {
        $query = "SELECT u.id, u.nombre, u.email, u.rol_id, u.activo, u.creado_en
                  FROM " . $this->table_name . " u
                  WHERE u.rol_id = ?
                  ORDER BY u.creado_en DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, ROLE_CLIENT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}