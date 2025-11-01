<?php
/**
 * Authentication Routes
 */

require_once __DIR__ . '/../controllers/AuthController.php';

function registerAuthRoutes($router) {
    $authController = new AuthController();
    
    // Public routes
    $router->post('/api/auth/register', [$authController, 'register']);
    $router->post('/api/auth/login', [$authController, 'login']);
    
    // Protected routes
    $router->get('/api/auth/me', [$authController, 'me']);
    $router->put('/api/auth/profile', [$authController, 'updateProfile']);
    $router->post('/api/auth/change-password', [$authController, 'changePassword']);
}
