<?php

/**
 * Gourmet Express - Main Entry Point
 */

use App\Core\Config;
use Dotenv\Dotenv;

// Load Autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Initialize Environment Variables
try {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
} catch (\Exception $e) {
    // In production, you might use real environment variables instead of .env
}

// Boot Config Registry
Config::initialize();

// Debugging & Error Reporting
if (Config::isDev()) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Routing Logic - Simple Dispatcher
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method     = $_SERVER['REQUEST_METHOD'];

header('Content-Type: text/html; charset=UTF-8');

switch ($requestUri) {
    case '/':
        echo "<h1>Welcome to " . htmlspecialchars(Config::get('app_name')) . "</h1>";
        echo "<p>Environment: " . Config::get('env') . "</p>";
        break;

    case '/api/status':
        header('Content-Type: application/json');
        echo json_encode(['status' => 'online', 'timestamp' => time()]);
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        break;
}