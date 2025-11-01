<?php
/**
 * User Model
 * Handles user data operations
 */

require_once __DIR__ . '/../config/database.php';

class User {
    
    private $conn;
    private $table = 'users';
    
    // User properties
    public $user_id;
    public $email;
    public $password_hash;
    public $full_name;
    public $phone;
    public $role;
    public $profile_image;
    public $is_active;
    public $email_verified;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Create new user
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (email, password_hash, full_name, phone, role, profile_image, is_active, email_verified)
                  VALUES (:email, :password_hash, :full_name, :phone, :role, :profile_image, :is_active, :email_verified)";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash password
        $hashedPassword = password_hash($this->password_hash, PASSWORD_BCRYPT);
        
        // Bind parameters
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password_hash', $hashedPassword);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':profile_image', $this->profile_image);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':email_verified', $this->email_verified);
        
        if ($stmt->execute()) {
            $this->user_id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Find user by ID
     */
    public function findById($userId) {
        $query = "SELECT user_id, email, full_name, phone, role, profile_image, is_active, 
                         email_verified, created_at, updated_at, last_login
                  FROM " . $this->table . " 
                  WHERE user_id = :user_id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Update last login timestamp
     */
    public function updateLastLogin($userId) {
        $query = "UPDATE " . $this->table . " SET last_login = NOW() WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        
        return $stmt->execute();
    }
    
    /**
     * Update user profile
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET full_name = :full_name, 
                      phone = :phone, 
                      profile_image = :profile_image,
                      updated_at = NOW()
                  WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':profile_image', $this->profile_image);
        
        return $stmt->execute();
    }
    
    /**
     * Change password
     */
    public function changePassword($userId, $newPassword) {
        $query = "UPDATE " . $this->table . " 
                  SET password_hash = :password_hash, updated_at = NOW()
                  WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':password_hash', $hashedPassword);
        
        return $stmt->execute();
    }
    
    /**
     * Get all users (admin only)
     */
    public function getAll($role = null, $limit = 50, $offset = 0) {
        $query = "SELECT user_id, email, full_name, phone, role, is_active, 
                         email_verified, created_at, last_login
                  FROM " . $this->table;
        
        if ($role) {
            $query .= " WHERE role = :role";
        }
        
        $query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        if ($role) {
            $stmt->bindParam(':role', $role);
        }
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email) {
        $query = "SELECT user_id FROM " . $this->table . " WHERE email = :email LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Activate/Deactivate user
     */
    public function setActiveStatus($userId, $isActive) {
        $query = "UPDATE " . $this->table . " 
                  SET is_active = :is_active, updated_at = NOW()
                  WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':is_active', $isActive, PDO::PARAM_BOOL);
        
        return $stmt->execute();
    }
}
