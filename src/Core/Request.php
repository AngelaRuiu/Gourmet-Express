<?php

namespace App\Core;

/**
 * A simple Request class to encapsulate HTTP request data.
 */
class Request
{
    private string $method;
    private string $uri;
    private array $params = []; //route parameters e.g. /orders/{id}

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    }

    // Route & Method Helpers
    public function getMethod(): string { return $this->method; }
    public function getUri(): string    { return $this->uri; }

    public function isGet(): bool    { return $this->method === 'GET'; }
    public function isPost(): bool   { return $this->method === 'POST'; }
    public function isPut(): bool    { return $this->method === 'PUT'; }
    public function isDelete(): bool { return $this->method === 'DELETE'; }
    public function isPatch(): bool  { return $this->method === 'PATCH'; }

    public function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public function isJson(): bool
    {
        return str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');
    }

     public function acceptsJson(): bool
    {
        return str_contains ($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }

    public function expectsJson(): bool
    {
        return str_contains ($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }

     // Route Parameters  e.g. /orders/{id}
    public function setParams(array $params): void { $this->params = $params; }

    /**
    * Get a route parameter by name, with an optional default if not set.
    *
    * Usage:
    *   $email = $request->param('email', '');
    *   $id = $request->param('id', 0);
    *   $name = $request->param('name', 'Guest');
    *   $date = $request->param('date', date('Y-m-d'));
    *   $isAdmin = $request->param('is_admin', false);
    *   $tags = $request->param('tags', []);
    *   $preferences = $request->param('preferences', ['newsletter' => true]);
    */
    public function param (string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    // Query string parameters (e.g. /search?q=term)
    public function query(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }
    
    public function allQuery(): array { return $_GET; }

    // POST / Form data
    public function input(string $key, mixed $default = null): mixed
    {
        $body = $this->parsedBody();
        return $body[$key] ?? $default;
    }

    public function all(): array { return $this->parsedBody(); }

    public function has(string $key): bool
    {
        $body = $this->parsedBody();
        return isset($body[$key]);
    }

    /**
     * Returns only the listed keys from the request body.
     * TO be used in controllers to avoid boilerplate of fetching and validating individual fields.
     * Example:
     *   $data = $request->only(['email', 'password']);
     *   $email = $data['email'] ?? '';
     *   $password = $data['password'] ?? '';
     */
    public function only(array $keys): array
    {
        $body = $this->parsedBody();
        return array_intersect_key($body, array_flip($keys));
    }

    // Files 
    public function file(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    // Internal helper to parse JSON body or fallback to $_POST for form submissions. Caches result for efficiency.
    private ?array $parsedBodyCache = null;

    private function parsedBody(): array
    {
        if ($this->parsedBodyCache !== null) {
            return $this->parsedBodyCache;
        }

        // JSON body (API requests)
        if ($this->isJson()) {
            $raw = file_get_contents('php://input');
            $this->parsedBodyCache = json_decode($raw, true) ?? [];
        } else {
            $this->parsedBodyCache = $_POST;
        }

        return $this->parsedBodyCache;
    }
}