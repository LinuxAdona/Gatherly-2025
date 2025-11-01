<?php
/**
 * Authentication Middleware
 * Verifies JWT tokens and user authentication
 */

require_once __DIR__ . '/../utils/JWT.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Logger.php';
require_once __DIR__ . '/../config/database.php';

class AuthMiddleware {
    
    /**
     * Verify authentication token
     */
    public static function authenticate() {
        $token = JWT::extractFromHeader();
        
        if (!$token) {
            Logger::warning('Authentication failed: No token provided');
            Response::unauthorized('Authentication token required');
        }
        
        $payload = JWT::verify($token);
        
        if (!$payload) {
            Logger::warning('Authentication failed: Invalid token');
            Response::unauthorized('Invalid or expired token');
        }
        
        // Verify user still exists and is active
        $user = self::getUserById($payload['user_id']);
        
        if (!$user) {
            Logger::warning('Authentication failed: User not found', ['user_id' => $payload['user_id']]);
            Response::unauthorized('User not found');
        }
        
        if (!$user['is_active']) {
            Logger::warning('Authentication failed: User inactive', ['user_id' => $payload['user_id']]);
            Response::unauthorized('Account is inactive');
        }
        
        // Store user data in global variable for use in controllers
        $GLOBALS['current_user'] = $user;
        
        return $user;
    }
    
    /**
     * Check if user has required role
     */
    public static function requireRole($allowedRoles) {
        $user = self::authenticate();
        
        if (!in_array($user['role'], $allowedRoles)) {
            Logger::warning('Authorization failed: Insufficient permissions', [
                'user_id' => $user['user_id'],
                'user_role' => $user['role'],
                'required_roles' => $allowedRoles
            ]);
            Response::forbidden('Insufficient permissions');
        }
        
        return $user;
    }
    
    /**
     * Get user by ID from database
     */
    private static function getUserById($userId) {
        try {
            $db = new Database();
            $conn = $db->getConnection();
            
            $query = "SELECT user_id, email, full_name, phone, role, is_active, email_verified 
                      FROM users 
                      WHERE user_id = :user_id";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            Logger::error('Database error in getUserById', ['error' => $e->getMessage()]);
            return null;
        }
    }
    
    /**
     * Get current authenticated user
     */
    public static function getCurrentUser() {
        return $GLOBALS['current_user'] ?? null;
    }
}
