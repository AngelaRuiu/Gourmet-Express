<?php

/**
 * API Routes
 *
 * All routes here are prefixed /api/v1 via the group() wrapper.
 * Every handler maps to a Controller that extends BaseApiController.
 * All responses are JSON — no HTML is ever returned from these routes.
 */

use App\Controllers\Api\MenuApiController;
use App\Core\Request;
use App\Core\Response;
use App\Core\Config;
use App\Core\Router;

$router->group('/api/v1', function (Router $router) {

    // Health check — no auth required
    $router->get('/status', function (Request $req, Response $res) {
        $res->success([
            'app'          => Config::get('app.name'),
            'version'      => '1.0.0',
            'environment'  => Config::get('app.env'),
            'sandbox_mode' => Config::isSandbox(),
        ]);
    });

    // Menu
    $router->get('/menu',          [MenuApiController::class, 'index']);
    $router->get('/menu/{id}',     [MenuApiController::class, 'show']);
    $router->post('/menu',         [MenuApiController::class, 'store']);
    $router->put('/menu/{id}',     [MenuApiController::class, 'update']);
    $router->delete('/menu/{id}',  [MenuApiController::class, 'destroy']);

});