<?php
require_once 'config/database.php';

class TwoFactorModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function generateCode($userId) {
        // Generar un código de 6 dígitos
        $code = sprintf("%06d", rand(0, 999999));
        
        // Eliminar códigos antiguos para este usuario
        $this->deleteUserCodes($userId);
        
        // Insertar nuevo código con expiración de 5 minutos
        $expiresAt = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        
        $query = "INSERT INTO two_factor_codes (user_id, code, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iss", $userId, $code, $expiresAt);
        
        if ($stmt->execute()) {
            return $code;
        }
        
        return false;
    }

    public function validateCode($userId, $code) {
        $query = "SELECT * FROM two_factor_codes WHERE user_id = ? AND code = ? AND expires_at > NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $userId, $code);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Eliminar el código después de usarlo
            $this->deleteCode($userId, $code);
            return true;
        }
        
        return false;
    }

    public function deleteUserCodes($userId) {
        $query = "DELETE FROM two_factor_codes WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        
        return $stmt->execute();
    }

    private function deleteCode($userId, $code) {
        $query = "DELETE FROM two_factor_codes WHERE user_id = ? AND code = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $userId, $code);
        
        return $stmt->execute();
    }
}
?>