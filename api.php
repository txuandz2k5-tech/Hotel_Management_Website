<?php
/**
 * Hotel Management REST API - Entry Point
 * 
 * This is the main entry point for all REST API requests
 * Routes all requests to the appropriate controller and action
 */

// Define base path
define('BASE_PATH', __DIR__);

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);

// Convert warnings/notices to exceptions
set_error_handler(function($severity, $message, $file, $line) {
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $message = sprintf("[%s] FATAL: %s in %s on line %d", date('Y-m-d H:i:s'), $error['message'], $error['file'], $error['line']);
        error_log($message . "\n", 3, BASE_PATH . '/storage/logs/api.log');

        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(500);
        }
        echo json_encode([
            'status' => 'error',
            'message' => 'Internal server error',
            'details' => $error['message']
        ], JSON_UNESCAPED_UNICODE);
    }
});

// Start session for API authentication
session_start();

// Create storage/logs directory if it doesn't exist
$logDir = BASE_PATH . '/storage/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

// Autoloader for our namespaced classes
spl_autoload_register(function($class) {
    // Map namespace directly to the src directory
    $file = BASE_PATH . '/src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load database connection
require_once BASE_PATH . '/MVC/Core/connectDB.php';

// Import necessary classes
use Shared\Http\Request;
use Shared\Http\Response;
use Shared\Http\Router;

try {
    // Create request and response objects
    $request = new Request();
    $response = new Response();
    
    // Create router
    $router = new Router($request, $response);
    
    // Load all routes
    $router->loadRoutes(BASE_PATH . '/src/Api/Routes.php');
    
    // Dispatch the request
    $router->dispatch();
    
} catch (\Exception $e) {
    $response = new Response();
    $response->error($e->getMessage(), 500);
}
