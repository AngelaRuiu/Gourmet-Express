<?php

/**
 * Admin Routes
 *
 * Only reachable via admin Website.
 * Every route carries AdminHostMiddleware + AuthMiddleware at minimum.
 * Sensitive routes additionally carry RoleMiddleware.
 */
use App\Core\Request;
use App\Core\Response;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminHostMiddleware;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\AuthController;


$router->get('/', function (Request $_req, Response $res) {
    if (\App\Core\SessionManager::isLoggedIn()) {
        $res->redirect('/dashboard');
    }
    $res->redirect('/login');
});

// Protected admin pages
// All require host + auth
$adminMiddleware = [AdminHostMiddleware::class, AuthMiddleware::class];

// Auth pages
// AdminHostMiddleware (no AuthMiddleware, otherwise we get redirect loops when not logged in)
$router->get('/login', [AuthController::class, 'showLogin'], $adminMiddleware);
$router->post('/login', [AuthController::class, 'login'], $adminMiddleware);
$router->get('/dashboard', [DashboardController::class, 'index'], $adminMiddleware);
$router->post('/logout', [AuthController::class, 'logout'], $adminMiddleware);

