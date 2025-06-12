<?php
require_once '../config/database.php';

/**
 * UserModel - Model voor gebruikers en authenticatie
 * Bevat alle database operaties voor gebruikers en sessies
 */
class UserModel {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Verwijder sessie (logout)
     */
    public function destroySession($sessionId) {
        $sql = "DELETE FROM user_sessions WHERE id = :session_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':session_id', $sessionId);
        return $stmt->execute();
    }
    
    /**
     * Ruim verlopen sessies op
     */
    public function cleanupExpiredSessions() {
        $sql = "DELETE FROM user_sessions WHERE expires_at < NOW()";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute();
    }
    
    /**
     * Ruim alle sessies van gebruiker op
     */
    public function cleanupUserSessions($userId) {
        $sql = "DELETE FROM user_sessions WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Haal alle gebruikers op
     */
    public function getAllUsers() {
        $sql = "SELECT id, username, email, role, is_active, created_at FROM users ORDER BY username ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Voeg nieuwe gebruiker toe
     */
    public function addUser($username, $email, $password, $role = 'editor') {
        $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', DatabaseUtils::hashPassword($password));
        $stmt->bindValue(':role', $role);
        return $stmt->execute();
    }
    
    /**
     * Update gebruiker
     */
    public function updateUser($id, $username, $email, $role, $isActive) {
        $sql = "UPDATE users SET username = :username, email = :email, role = :role, is_active = :is_active, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':role', $role);
        $stmt->bindValue(':is_active', $isActive, PDO::PARAM_BOOL);
        return $stmt->execute();
    }
    
    /**
     * Update wachtwoord
     */
    public function updatePassword($id, $newPassword) {
        $sql = "UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':password', DatabaseUtils::hashPassword($newPassword));
        return $stmt->execute();
    }
    
    /**
     * Controleer of gebruikersnaam al bestaat
     */
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = :username";
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':username', $username);
        
        if ($excludeId) {
            $stmt->bindValue(':exclude_id', $excludeId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    /**
     * Controleer of email al bestaat
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email);
        
        if ($excludeId) {
            $stmt->bindValue(':exclude_id', $excludeId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}
?>
     * Authenticeer gebruiker
     */
    public function authenticateUser($username, $password) {
        $sql = "SELECT * FROM users WHERE username = :username AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch();
        
        if ($user && DatabaseUtils::verifyPassword($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Haal gebruiker op via ID
     */
    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = :id AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Maak nieuwe sessie aan
     */
    public function createSession($userId, $sessionId, $ipAddress, $userAgent) {
        // Verwijder oude sessies voor deze gebruiker
        $this->cleanupUserSessions($userId);
        
        $sql = "INSERT INTO user_sessions (id, user_id, ip_address, user_agent, expires_at) 
                VALUES (:session_id, :user_id, :ip_address, :user_agent, DATE_ADD(NOW(), INTERVAL 24 HOUR))";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':session_id', $sessionId);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':ip_address', $ipAddress);
        $stmt->bindValue(':user_agent', $userAgent);
        
        return $stmt->execute();
    }
    
    /**
     * Valideer sessie
     */
    public function validateSession($sessionId) {
        $sql = "SELECT s.*, u.* FROM user_sessions s 
                INNER JOIN users u ON s.user_id = u.id 
                WHERE s.id = :session_id AND s.expires_at > NOW() AND u.is_active = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':session_id', $sessionId);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**