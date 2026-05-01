<?php

/**
 * Dev Routes
 *
 * Only loaded when APP_ENV is 'local' or 'development'.
 * This file is never required in production — these routes
 * do not exist at runtime, they cannot be accessed or exploited by attackers.
 */

use App\Core\Request;
use App\Core\Response;
use App\Handlers\NotificationHandler;

$router->get('/test-email', function (Request $req, Response $res) {
    $handler = new NotificationHandler();
    $result  = $handler->sendReservationConfirmation(
        'test@example.com',
        'John Doe',
        ['date' => '2025-10-15', 'time' => '19:00', 'guests' => 4]
    );
    $res->json(['sent' => $result], $result ? 200 : 500, $result ? 'Email sent.' : 'Failed.');
});

$router->get('/test-db', function (Request $req, Response $res) {
    $db  = \App\Core\Database::getInstance();
    $row = $db->fetchOne('SELECT NOW() AS time, VERSION() AS version');
    $res->success($row, 'Database connection OK.');
});

$router->get('/config-check', function (Request $req, Response $res) {
    $res->success([
        'env'          => \App\Core\Config::get('app.env'),
        'debug'        => \App\Core\Config::get('app.debug'),
        'timezone'     => date_default_timezone_get(),
        'mail_host'    => \App\Core\Config::get('mail.host'),
        'sandbox_mode' => \App\Core\Config::isSandbox(),
    ]);
});