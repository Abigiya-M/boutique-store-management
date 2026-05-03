<?php
/**
 * Boutique Store Management System
 * Main Application Entry Point
 */

// Define base path constants
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__) . '/app');
    define('CONFIG_PATH', dirname(__DIR__) . '/config');
    define('STORAGE_PATH', dirname(__DIR__) . '/storage');
    define('PUBLIC_PATH', __DIR__);
    define('FRONTEND_PATH', dirname(__DIR__) . '/frontend');
    define('ROOT_PATH', dirname(__DIR__));
}

// Load bootstrap with simplified setup
require_once ROOT_PATH . '/bootstrap.php';

use App\Core\Router;
use App\Core\Database;

// Set up error handling
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    log_message("PHP Error [$errno]: $errstr in $errfile on line $errline", 'error');
    if (in_array($errno, [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        include APP_PATH . '/views/errors/500.php';
        exit;
    }
    return true;
});

// Set up exception handling
set_exception_handler(function($exception) {
    $code = $exception->getCode() ?: 500;
    log_message($exception->getMessage() . " in " . $exception->getFile() . ":" . $exception->getLine(), 'error');
    
    http_response_code($code);
    
    // Return JSON for API requests
    if (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $exception->getMessage()
        ]);
    } else {
        include APP_PATH . '/views/errors/500.php';
    }
    exit;
});

try {
    // Initialize database
    $database = Database::getInstance();
    
    // Initialize router
    $router = new Router();
    
    // Load routes
    $routeLoader = require_once ROOT_PATH . '/routes/web.php';
    $routeLoader($router);
    
    // Get request details
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Normalize URI
    $uri = '/' . ltrim($uri, '/');
    
    // Check if static file
    if (preg_match('/\.(html|css|js|json|png|jpg|jpeg|gif|svg|ico)$/', $uri)) {
        serveStaticFile($uri);
    } else {
        // Dispatch route
        $router->dispatch($method, $uri);
    }
    
} catch (\App\Exceptions\HttpException $e) {
    http_response_code($e->getCode());
    include APP_PATH . '/views/errors/http.php';
} catch (\App\Exceptions\NotFoundException $e) {
    http_response_code(404);
    include APP_PATH . '/views/errors/404.php';
} catch (\Exception $e) {
    http_response_code(500);
    log_message($e->getMessage(), 'error');
    include APP_PATH . '/views/errors/500.php';
}

/**
 * Serve static file from frontend directory
 */
function serveStaticFile($uri) {
    $filePath = FRONTEND_PATH . $uri;
    
    // Prevent directory traversal
    $realPath = realpath($filePath);
    if ($realPath === false || strpos($realPath, FRONTEND_PATH) !== 0) {
        throw new \App\Exceptions\NotFoundException("File not found");
    }
    
    if (!file_exists($realPath)) {
        throw new \App\Exceptions\NotFoundException("File not found");
    }
    
    // Determine mime type
    $ext = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'html' => 'text/html; charset=utf-8',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
    ];
    
    header('Content-Type: ' . ($mimeTypes[$ext] ?? 'application/octet-stream'));
    header('Cache-Control: public, max-age=3600');
    readfile($realPath);
    exit;
}

// Close database on shutdown
register_shutdown_function(function () {
    try {
        $db = Database::getInstance();
        $db->close();
    } catch (\Exception $e) {
        // Ignore
    }
});
