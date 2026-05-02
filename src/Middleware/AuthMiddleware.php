<?php

namespace App\Middleware;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;

/**
 * Middleware to enforce authentication.
 * If the user is not logged in, it returns a 401 for API requests or redirects to login for web requests.
 * This is a simple session-based auth check for demonstration purposes.  It was added now so  routes can reference it.
 */
class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response, callable $next): void
    {
        session_start();

        if (empty($_SESSION['user_id'])) {
            // API request → JSON 401
            if ($request->expectsJson()) {
                $response->unauthorized('You must be logged in.');
            }

            // Web request → redirect to login page
            $response->redirect('/login');
        }

        $next();
    }
}