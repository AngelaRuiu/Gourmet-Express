<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\SessionManager;
use App\Constants\UserRole;
use  App\Core\MiddlewareInterface;

/**
 * RoleMiddleware
 *
 * Usage in routes:
 *   new RoleMiddleware(UserRole::ADMIN)    — admin only
 *   new RoleMiddleware(UserRole::MANAGER)  — admin + manager
 */

class RoleMiddleware implements MiddlewareInterface
{
    private UserRole $minimumRole;

    public function __construct(UserRole $minimumRole = UserRole::MANAGER)
    {
        $this->minimumRole = $minimumRole;
    }

    /**
     * Checks if the user is logged in and has the required role.
     * If not logged in, redirects to login page.
     * If logged in but insufficient role, returns 403 forbidden.
     */
    public function handle(Request $_req, Response $response, callable $next): void
    {
        $user = SessionManager::getUser();

        if (!$user) {
            $response->redirect('/login');
        }

        $role = UserRole::from($user['role']);

        $allowed = match($this->minimumRole) {
            UserRole::ADMIN   => [UserRole::ADMIN],
            UserRole::MANAGER => [UserRole::ADMIN, UserRole::MANAGER],
            UserRole::STAFF   => [UserRole::ADMIN, UserRole::MANAGER, UserRole::STAFF],
        };

        if (!in_array($role, $allowed, true)) {
            $response->forbidden('Unfortunately, you do not have permission to access this page.');
        }

        $next();
    }
}