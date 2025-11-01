<?php
/**
 * Authentication Controller
 * Handles user registration, login, and authentication
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/JWT.php';
require_once __DIR__ . '/../utils/Logger.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class AuthController {
    
    /**
     * User registration
     * POST /api/auth/register
     */
    public function register() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $requiredFields = ['email', 'password', 'full_name', 'role'];
            $errors = Validator::required($data, $requiredFields);
            
            if (!empty($errors)) {
                Response::validationError($errors);
            }
            
            // Validate email format
            if (!Validator::email($data['email'])) {
                Response::validationError(['email' => 'Invalid email format']);
            }
            
            // Validate password strength
            $passwordErrors = Validator::strongPassword($data['password']);
            if (!empty($passwordErrors)) {
                Response::validationError(['password' => $passwordErrors]);
            }
            
            // Validate role
            $allowedRoles = ['organizer', 'venue_manager'];
            if (!Validator::enum($data['role'], $allowedRoles)) {
                Response::validationError(['role' => 'Invalid role. Must be organizer or venue_manager']);
            }
            
            // Validate phone if provided
            if (isset($data['phone']) && !empty($data['phone'])) {
                if (!Validator::phone($data['phone'])) {
                    Response::validationError(['phone' => 'Invalid phone number format']);
                }
            }
            
            // Create user instance
            $user = new User();
            
            // Check if email already exists
            if ($user->emailExists($data['email'])) {
                Response::error('Email already registered', 409);
            }
            
            // Set user properties
            $user->email = Validator::sanitizeEmail($data['email']);
            $user->password_hash = $data['password'];
            $user->full_name = Validator::sanitizeString($data['full_name']);
            $user->phone = isset($data['phone']) ? Validator::sanitizeString($data['phone']) : null;
            $user->role = $data['role'];
            $user->profile_image = null;
            $user->is_active = true;
            $user->email_verified = false;
            
            // Create user
            if ($user->create()) {
                Logger::info('User registered successfully', ['user_id' => $user->user_id, 'email' => $user->email]);
                
                // Generate JWT token
                $token = JWT::generate([
                    'user_id' => $user->user_id,
                    'email' => $user->email,
                    'role' => $user->role
                ]);
                
                Response::success([
                    'user' => [
                        'user_id' => $user->user_id,
                        'email' => $user->email,
                        'full_name' => $user->full_name,
                        'role' => $user->role
                    ],
                    'token' => $token
                ], 'Registration successful', 201);
            } else {
                Logger::error('User registration failed', ['email' => $data['email']]);
                Response::serverError('Registration failed');
            }
            
        } catch (Exception $e) {
            Logger::error('Registration error', ['error' => $e->getMessage()]);
            Response::serverError('Registration failed');
        }
    }
    
    /**
     * User login
     * POST /api/auth/login
     */
    public function login() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $requiredFields = ['email', 'password'];
            $errors = Validator::required($data, $requiredFields);
            
            if (!empty($errors)) {
                Response::validationError($errors);
            }
            
            // Create user instance
            $user = new User();
            
            // Find user by email
            $userData = $user->findByEmail($data['email']);
            
            if (!$userData) {
                Logger::warning('Login failed: User not found', ['email' => $data['email']]);
                Response::error('Invalid credentials', 401);
            }
            
            // Verify password
            if (!$user->verifyPassword($data['password'], $userData['password_hash'])) {
                Logger::warning('Login failed: Invalid password', ['email' => $data['email']]);
                Response::error('Invalid credentials', 401);
            }
            
            // Check if user is active
            if (!$userData['is_active']) {
                Logger::warning('Login failed: Account inactive', ['email' => $data['email']]);
                Response::error('Account is inactive', 403);
            }
            
            // Update last login
            $user->updateLastLogin($userData['user_id']);
            
            // Generate JWT token
            $token = JWT::generate([
                'user_id' => $userData['user_id'],
                'email' => $userData['email'],
                'role' => $userData['role']
            ]);
            
            Logger::info('User logged in successfully', ['user_id' => $userData['user_id']]);
            
            Response::success([
                'user' => [
                    'user_id' => $userData['user_id'],
                    'email' => $userData['email'],
                    'full_name' => $userData['full_name'],
                    'phone' => $userData['phone'],
                    'role' => $userData['role'],
                    'profile_image' => $userData['profile_image']
                ],
                'token' => $token
            ], 'Login successful');
            
        } catch (Exception $e) {
            Logger::error('Login error', ['error' => $e->getMessage()]);
            Response::serverError('Login failed');
        }
    }
    
    /**
     * Get current user profile
     * GET /api/auth/me
     */
    public function me() {
        try {
            $currentUser = AuthMiddleware::authenticate();
            
            Response::success([
                'user' => [
                    'user_id' => $currentUser['user_id'],
                    'email' => $currentUser['email'],
                    'full_name' => $currentUser['full_name'],
                    'phone' => $currentUser['phone'],
                    'role' => $currentUser['role'],
                    'profile_image' => $currentUser['profile_image'],
                    'email_verified' => $currentUser['email_verified']
                ]
            ]);
            
        } catch (Exception $e) {
            Logger::error('Get profile error', ['error' => $e->getMessage()]);
            Response::serverError('Failed to retrieve profile');
        }
    }
    
    /**
     * Update user profile
     * PUT /api/auth/profile
     */
    public function updateProfile() {
        try {
            $currentUser = AuthMiddleware::authenticate();
            $data = json_decode(file_get_contents('php://input'), true);
            
            $user = new User();
            $user->user_id = $currentUser['user_id'];
            $user->full_name = isset($data['full_name']) ? Validator::sanitizeString($data['full_name']) : $currentUser['full_name'];
            $user->phone = isset($data['phone']) ? Validator::sanitizeString($data['phone']) : $currentUser['phone'];
            $user->profile_image = isset($data['profile_image']) ? $data['profile_image'] : $currentUser['profile_image'];
            
            // Validate phone if provided
            if ($user->phone && !Validator::phone($user->phone)) {
                Response::validationError(['phone' => 'Invalid phone number format']);
            }
            
            if ($user->update()) {
                Logger::info('Profile updated', ['user_id' => $user->user_id]);
                
                Response::success([
                    'user' => [
                        'user_id' => $user->user_id,
                        'full_name' => $user->full_name,
                        'phone' => $user->phone,
                        'profile_image' => $user->profile_image
                    ]
                ], 'Profile updated successfully');
            } else {
                Response::serverError('Failed to update profile');
            }
            
        } catch (Exception $e) {
            Logger::error('Update profile error', ['error' => $e->getMessage()]);
            Response::serverError('Failed to update profile');
        }
    }
    
    /**
     * Change password
     * POST /api/auth/change-password
     */
    public function changePassword() {
        try {
            $currentUser = AuthMiddleware::authenticate();
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $requiredFields = ['current_password', 'new_password'];
            $errors = Validator::required($data, $requiredFields);
            
            if (!empty($errors)) {
                Response::validationError($errors);
            }
            
            // Validate new password strength
            $passwordErrors = Validator::strongPassword($data['new_password']);
            if (!empty($passwordErrors)) {
                Response::validationError(['new_password' => $passwordErrors]);
            }
            
            $user = new User();
            $userData = $user->findById($currentUser['user_id']);
            
            // Verify current password
            if (!$user->verifyPassword($data['current_password'], $userData['password_hash'])) {
                Response::error('Current password is incorrect', 401);
            }
            
            // Change password
            if ($user->changePassword($currentUser['user_id'], $data['new_password'])) {
                Logger::info('Password changed', ['user_id' => $currentUser['user_id']]);
                Response::success(null, 'Password changed successfully');
            } else {
                Response::serverError('Failed to change password');
            }
            
        } catch (Exception $e) {
            Logger::error('Change password error', ['error' => $e->getMessage()]);
            Response::serverError('Failed to change password');
        }
    }
}
