<?php

// Load Autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Config;
use App\Constants\AppConstants;

// Initialize Environment Variables
try {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
} catch (\Exception $e) {
    http_response_code(500);
    echo "<h1>Configuration Error</h1>";
    echo "<p>Could not load environment variables. Please ensure your .env file exists.</p>";
    
    if (getenv('APP_ENV') !== 'production') {
        echo "<pre>{$e->getMessage()}</pre>";
    }
    exit;
}

// Boot Config Registry
Config::initialize();
date_default_timezone_set(AppConstants::DEFAULT_TIMEZONE);

/**
 * Debugging & Error Reporting
 */
if (Config::get('app.debug')) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Routing Logic - Simple example for demonstration
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method     = $_SERVER['REQUEST_METHOD'];

header('Content-Type: text/html; charset=UTF-8');

switch ($requestUri) {
    case '/':
        echo "<h1>Welcome to " . htmlspecialchars(Config::get('app.name')) . "</h1>";
        echo "<p>Environment: " . htmlspecialchars(Config::get('app.env')) . "</p>";
        break;

    case '/api/status':
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'online', 
            'timestamp' => time(),
            'sandbox_mode' => Config::isSandbox()
        ]);
        break;
    case '/test-email':
        // For testing email sending (not for production use)
        if (!\App\Core\Config::isDev()) {
            http_response_code(403);
            echo "<h1>403 - Forbidden</h1>";
            break;
        }
        $notificationHandler = new \App\Handlers\NotificationHandler();
        $result = $notificationHandler->sendReservationConfirmation(
            'test@example.com',
            'John Doe',
            ['date' => '2025-10-15', 'time' => '19:00', 'guests' => 4]
        );
        echo $result ? "Email sent successfully!" : "Failed to send email.";
        break;
    default:
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        break;
}