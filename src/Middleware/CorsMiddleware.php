<?php

namespace App\Middleware;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;

/**
 * Middleware to handle Cross-Origin Resource Sharing (CORS).
 * This allows our API to be accessed from different origins (e.g., frontend apps).
 */
class CorsMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response, callable $next): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        // OPTIONS is a preflight check — browsers send it before the real request
        if ($request->getMethod() === 'OPTIONS') {
            http_response_code(204);
            exit;
        }

        $next();
    }
}