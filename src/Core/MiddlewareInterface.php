<?php

namespace App\Core;

interface MiddlewareInterface
{
    /**
     * Handle the request.
     * Call $next() to pass to the next middleware or controller.
     * Call $response->error() / forbidden() / unauthorized() to abort.
     */
    public function handle(Request $_req, Response $response, callable $next): void;
}