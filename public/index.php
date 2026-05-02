<?php

// Load Autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Config;
use App\Core\Router;
use App\Core\Request;
use App\Core\Response;
use App\Constants\AppConstants;

// Initialize Environment Variables
try {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
} catch (\Exception $e) {
    http_response_code(500);
   echo "<h1>Configuration Error</h1><p>Could not load .env file.</p>";
    if (getenv('APP_ENV') !== 'production') {
        echo "<pre>{$e->getMessage()}</pre>";
    }
    exit;
}

// Boot Config Registry
Config::initialize();

// Set default timezone globally
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

// HTTP Request Handling
$request  = new Request();
$response = new Response();
$router   = new Router();

// Load route definitions from external files for better organization
$router->loadRoutesFrom(Config::get('paths.routes') . '/web.php');
$router->loadRoutesFrom(Config::get('paths.routes') . '/api.php');
if (Config::isDev()) {
    $router->loadRoutesFrom(Config::get('paths.routes') . '/dev.php');
};

// Dispatch the request through the router to get a response
$router->dispatch($request, $response);
