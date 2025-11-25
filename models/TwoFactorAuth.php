<?php
/**
 * Modelo de Autenticación de Dos Factores (2FA)
 * Clase que maneja la lógica de negocio para la autenticación de dos factores
 */

require_once '../config/database.php';

class TwoFactorAuth {
    private $conn;
    private $table_name = "two_factor_auth";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Generar un código OTP (One Time Password) de 6 dígitos
     * @return string
     */
    public function generateOTP() {
        $otp = '';
        for ($i = 0; $i < OTP_LENGTH; $i++) {
            $otp .= mt_rand(0, 9);
        }
        return $otp;
    }

    /**
     * Crear un registro de autenticación de dos factores
     * @param int $user_id
     * @param string $otp
     * @return bool
     */
    public function createOTP($user_id, $otp) {
        // Calcular la hora de expiración
        $expira_en = date('Y-m-d H:i:s', time() + OTP_EXPIRATION);
        
        $query = "INSERT INTO " . $this->table_name . " 
                  SET usuario_id=:usuario_id, codigo=:codigo, expira_en=:expira_en";

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos
        $user_id = (int)$user_id;

        // Vincular valores
        $stmt->bindParam(":usuario_id", $user_id);
        $stmt->bindParam(":codigo", $otp);
        $stmt->bindParam(":expira_en", $expira_en);

        return $stmt->execute();
    }

    /**
     * Verificar un código OTP
     * @param int $user_id
     * @param string $otp
     * @return bool
     */
    public function verifyOTP($user_id, $otp) {
        // Eliminar códigos expirados
        $this->cleanExpiredCodes();
        
        $query = "SELECT id, usado, expira_en 
                  FROM " . $this->table_name . " 
                  WHERE usuario_id = :usuario_id AND codigo = :codigo 
                  ORDER BY creado_en DESC 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $user_id);
        $stmt->bindParam(":codigo", $otp);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar si el código ya ha sido usado
            if ($row['usado'] == 1) {
                return false;
            }
            
            // Verificar si el código ha expirado
            if (strtotime($row['expira_en']) < time()) {
                return false;
            }
            
            // Marcar el código como usado
            $this->markAsUsed($row['id']);
            
            return true;
        }
        
        return false;
    }

    /**
     * Marcar un código OTP como usado
     * @param int $otp_id
     * @return bool
     */
    private function markAsUsed($otp_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET usado = 1 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $otp_id);

        return $stmt->execute();
    }

    /**
     * Eliminar códigos expirados
     * @return void
     */
    private function cleanExpiredCodes() {
        $now = date('Y-m-d H:i:s');
        $query = "DELETE FROM " . $this->table_name . " WHERE expira_en < :now";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":now", $now);
        $stmt->execute();
    }

    /**
     * Enviar código OTP (simulado - en un sistema real se enviaría por email o SMS)
     * @param string $email
     * @param string $otp
     * @return bool
     */
    public function sendOTP($email, $otp) {
        // En un sistema real, aquí se enviaría el código por email o SMS
        // Por ahora, simplemente devolvemos true para simular el envío
        // En una implementación real, se usaría PHPMailer o una API de SMS
        
        echo "Código OTP enviado a $email: $otp\n"; // Solo para pruebas
        
        return true;
    }
}