<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\HostGuard;
use App\Core\SessionManager;
use App\Constants\AppConstants;
use App\Core\MiddlewareInterface;

/**
 * Middleware to enforce authentication.
 * If the user is not logged in, it returns a 401 for API requests or redirects to login for web requests.
 * This is a simple session-based auth check for demonstration purposes.  It was added now so  routes can reference it.
 */
class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $_req, Response $response, callable $next): void
    {
         if (!SessionManager::isLoggedIn()) {
            if ($_req->expectsJson()) {
                $response->unauthorized('Authentication required.');
            }

            // Store intended URL so we can redirect back after login
            SessionManager::flash('intended_url', $_req->getUri());
            $loginUrl = HostGuard::isAdminHost()
                ? 'http://' . AppConstants::ADMIN_HOST . '/login'
                : '/login';
            $response->redirect($loginUrl);
        }

        $next();
    }
}