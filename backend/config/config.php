<?php
/**
 * Application Configuration
 * Loads environment variables and sets global constants
 */

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            $value = trim($value, '"\'');
            
            // Set environment variable
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
    return true;
}

// Load .env file
$envFile = __DIR__ . '/../../.env';
if (!loadEnv($envFile)) {
    // Fallback to .env.example if .env doesn't exist
    loadEnv(__DIR__ . '/../../.env.example');
}

// Application Constants
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Gatherly GEMS');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN));
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost/Gatherly-2025');

// Security
define('JWT_SECRET', $_ENV['JWT_SECRET'] ?? 'change_this_secret_key');
define('SESSION_LIFETIME', (int)($_ENV['SESSION_LIFETIME'] ?? 7200));

// File Upload
define('MAX_UPLOAD_SIZE', (int)($_ENV['MAX_UPLOAD_SIZE'] ?? 5242880)); // 5MB
define('ALLOWED_EXTENSIONS', explode(',', $_ENV['ALLOWED_EXTENSIONS'] ?? 'jpg,jpeg,png,pdf'));

// Paths
define('ROOT_PATH', dirname(__DIR__, 2));
define('BACKEND_PATH', ROOT_PATH . '/backend');
define('FRONTEND_PATH', ROOT_PATH . '/frontend');
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('CONTRACT_PATH', ROOT_PATH . '/contracts');
define('LOG_PATH', ROOT_PATH . '/logs');

// API Keys
define('GOOGLE_MAPS_API_KEY', $_ENV['GOOGLE_MAPS_API_KEY'] ?? '');
define('PAYPAL_CLIENT_ID', $_ENV['PAYPAL_CLIENT_ID'] ?? '');
define('PAYPAL_SECRET', $_ENV['PAYPAL_SECRET'] ?? '');

// Error Reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Asia/Manila');

// CORS Headers (for API)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=UTF-8');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
