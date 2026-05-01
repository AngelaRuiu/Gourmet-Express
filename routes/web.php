<?php

/**
 * Web Routes
 *
 * These routes return HTML responses rendered via TemplateEngine.
 * Every handler maps to a Controller that extends BaseController.
 */

use App\Controllers\StartPageController;
use App\Controllers\MenuController;
use App\Controllers\ContactController;

//WIP: Add auth routes, reservation routes, etc.
$router->get('/',        [StartPageController::class,    'index']);
$router->get('/menu',    [MenuController::class,         'index']);
$router->get('/contact', [ContactController::class,      'index']);
$router->post('/contact',[ContactController::class,      'store']);

