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

$router->get('/debug-login', function (Request $_req, Response $res) {
    $users = new \App\Managers\UserManager();

    // Check if user exists by username
    $user = $users->findByUsername(''); // change to an existing username in DB or create one for testing

    if (!$user) {
        $res->success(['step' => 'FAILED', 'reason' => 'User not found in database']);
    }

    // Check is_active
    if (!$user['is_active']) {
        $res->success(['step' => 'FAILED', 'reason' => 'User is_active = 0', 'user' => $user]);
    }

    // Check password verification
    $plain   = ''; // the password to be tested
    $matches = password_verify($plain, $user['password']);

    $res->success([
        'step'          => $matches ? 'PASSED' : 'FAILED',
        'reason'        => $matches ? 'Password correct' : 'password_verify returned false',
        'hash_in_db'    => $user['password'],
        'hash_length'   => strlen($user['password']),
        'is_active'     => $user['is_active'],
        'role'          => $user['role'],
    ]);
});

$router->get('/generate-hash', function (Request $_req, Response $res) {
    $password = ''; // set this to the plain-text password you want to hash for testing
    $hash     = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    $res->success([
        'password' => $password,
        'hash'     => $hash,
        'verify'   => password_verify($password, $hash),
    ]);
});