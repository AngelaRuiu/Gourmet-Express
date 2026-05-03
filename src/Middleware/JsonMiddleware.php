<?php

namespace App\Middleware;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;

/**
 * Middleware to enforce JSON content type for write operations.
 * For POST, PUT, PATCH requests, it checks if the Content-Type is application/json.
 * If not, it returns a 415 Unsupported Media Type error.
 */
class JsonMiddleware implements MiddlewareInterface
{
    public function handle(Request $_req, Response $response, callable $next): void
    {
        if (in_array($_req->getMethod(), ['POST', 'PUT', 'PATCH'])) {
            if (!$_req->isJson()) {
                $response->error(
                    'Content-Type must be application/json',
                    415  // 415 Unsupported Media Type
                );
            }
        }

        $next();
    }
}