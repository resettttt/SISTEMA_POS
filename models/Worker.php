<?php
/**
 * Modelo de Trabajador
 * Extiende la funcionalidad del modelo User con permisos específicos para trabajadores
 */

require_once '../config/database.php';
require_once 'User.php';

class Worker extends User {
    
    public function __construct($db) {
        parent::__construct($db);
    }

    /**
     * Verificar si el usuario es trabajador
     * @param int $user_id
     * @return bool
     */
    public function isWorker($user_id) {
        $query = "SELECT rol_id FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['rol_id'] == ROLE_WORKER;
        }
        
        return false;
    }

    /**
     * Obtener todos los clientes (solo lectura para trabajadores)
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

    /**
     * Actualizar datos de un cliente (solo trabajadores pueden hacerlo)
     * @param int $client_id
     * @param string $nombre
     * @param string $email
     * @return bool
     */
    public function updateClient($client_id, $nombre, $email) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre=:nombre, email=:email 
                  WHERE id = :id AND rol_id = :rol_id";

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $nombre = htmlspecialchars(strip_tags($nombre));
        $email = htmlspecialchars(strip_tags($email));
        $client_id = (int)$client_id;

        // Vincular valores
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":id", $client_id);
        $stmt->bindParam(":rol_id", ROLE_CLIENT);

        return $stmt->execute();
    }
}