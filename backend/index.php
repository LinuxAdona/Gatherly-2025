<?php
/**
 * API Entry Point
 * Main index file for the backend API
 */

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/routes/Router.php';
require_once __DIR__ . '/routes/auth.php';
require_once __DIR__ . '/utils/Response.php';
require_once __DIR__ . '/utils/Logger.php';

// Create router instance
$router = new Router();

// Register routes
registerAuthRoutes($router);

// Health check endpoint
$router->get('/api/health', function() {
    Response::success([
        'status' => 'healthy',
        'timestamp' => date('c'),
        'version' => '1.0.0'
    ], 'API is running');
});

// 404 handler
$router->notFound(function() {
    Response::notFound('Endpoint not found');
});

// Run the router
try {
    $router->run();
} catch (Exception $e) {
    Logger::critical('Unhandled exception', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    Response::serverError('An unexpected error occurred');
}
