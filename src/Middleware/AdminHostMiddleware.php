<?php
namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\HostGuard;
use App\Core\MiddlewareInterface;

class AdminHostMiddleware implements MiddlewareInterface
{
    public function handle(Request $_req, Response $response, callable $next): void
    {
        if(!HostGuard::isAdminHost()) {
            // Silent 404; do not reveal the admin panel exists
            $response->setStatus(404);
            $response->html('<h1>404 Not Found</h1>', 404);
        } 

        $next();
    }
}
