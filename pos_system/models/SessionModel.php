<?php
require_once 'config/database.php';

class SessionModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createSession($userId, $sessionToken, $ipAddress, $userAgent) {
        // Eliminar sesiones antiguas del mismo usuario
        $this->deleteUserSessions($userId);
        
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours')); // La sesión expira en 24 horas
        
        $query = "INSERT INTO sessions (user_id, session_token, ip_address, user_agent, expires_at) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("issss", $userId, $sessionToken, $ipAddress, $userAgent, $expiresAt);
        
        return $stmt->execute();
    }

    public function getSessionByToken($sessionToken) {
        $query = "SELECT s.*, u.role FROM sessions s 
                  JOIN users u ON s.user_id = u.id 
                  WHERE s.session_token = ? AND s.expires_at > NOW() AND u.is_active = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $sessionToken);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    public function deleteSession($sessionToken) {
        $query = "DELETE FROM sessions WHERE session_token = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $sessionToken);
        
        return $stmt->execute();
    }

    public function deleteUserSessions($userId) {
        $query = "DELETE FROM sessions WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        
        return $stmt->execute();
    }

    public function cleanupExpiredSessions() {
        $query = "DELETE FROM sessions WHERE expires_at <= NOW()";
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute();
    }
}
?>