<?php
/**
 * Modelo de Cliente
 * Extiende la funcionalidad del modelo User con permisos específicos para clientes
 */

require_once '../config/database.php';
require_once 'User.php';

class Client extends User {
    
    public function __construct($db) {
        parent::__construct($db);
    }

    /**
     * Verificar si el usuario es cliente
     * @param int $user_id
     * @return bool
     */
    public function isClient($user_id) {
        $query = "SELECT rol_id FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['rol_id'] == ROLE_CLIENT;
        }
        
        return false;
    }
}