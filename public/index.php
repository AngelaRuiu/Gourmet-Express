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

//Web Routes (For demonstration, these are hardcoded here, will load these from a separate routes file or i'll annotations.)
//$router->get('/', [\App\Controllers\HomeController::class, 'index']);
$router->get('/menu', [\App\Controllers\MenuController::class, 'index']);
//$router->get('/reservations', [\App\Controllers\ReservationController::class, 'index']);
//$router->post('/reservations', [\App\Controllers\ReservationController::class, 'store']);
//$router->get('/contact', [\App\Controllers\ContactController::class, 'index']);
//$router->post('/contact', [\App\Controllers\ContactController::class, 'submit']]);

//API Routes (all prefixed with /api/v1)
$router->group('/api/v1', function (Router $router) {

    // Status / health-check (no auth required)
    $router->get('/status', function (Request $req, Response $res) {
        $res->success([
            'app'          => Config::get('app.name'),
            'version'      => '1.0.0',
            'sandbox_mode' => Config::isSandbox(),
        ]);
    });

    // Menu (WIP - for demonstration, not fully implemented)
    $router->get('/menu',        [\App\Controllers\Api\MenuApiController::class, 'index']);
    $router->get('/menu/{id}',   [\App\Controllers\Api\MenuApiController::class, 'show']);
    $router->post('/menu',       [\App\Controllers\Api\MenuApiController::class, 'store']);
    $router->put('/menu/{id}',   [\App\Controllers\Api\MenuApiController::class, 'update']);
    $router->delete('/menu/{id}',[\App\Controllers\Api\MenuApiController::class, 'destroy']);

    // Reservations
    $router->get('/reservations',      [\App\Controllers\Api\ReservationApiController::class, 'index']);
    $router->post('/reservations',     [\App\Controllers\Api\ReservationApiController::class, 'store']);
    $router->patch('/reservations/{id}',[\App\Controllers\Api\ReservationApiController::class, 'updateStatus']);

    // Orders
    $router->get('/orders',            [\App\Controllers\Api\OrderApiController::class, 'index']);
    $router->get('/orders/{id}',       [\App\Controllers\Api\OrderApiController::class, 'show']);
    $router->post('/orders',           [\App\Controllers\Api\OrderApiController::class, 'store']);
    $router->patch('/orders/{id}',     [\App\Controllers\Api\OrderApiController::class, 'updateStatus']);

});

// Dev-only routes (not reachable in production)
if (Config::isDev()) {
    $router->get('/test-email', function (Request $req, Response $res) {
        $handler = new \App\Handlers\NotificationHandler();
        $result  = $handler->sendReservationConfirmation(
            'test@example.com',
            'John Doe',
            ['date' => '2025-10-15', 'time' => '19:00', 'guests' => 4]
        );
        $res->json(['sent' => $result], $result ? 200 : 500);
    });
}

// Dispatch the request through the router to get a response
$router->dispatch($request, $response);
