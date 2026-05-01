<?php

namespace App\Core;

/**
 *Router class to handle incoming requests and direct them to the appropriate controllers.
 * Supports dynamic route parameters (e.g. /orders/{id}), HTTP method-based routing, and route grouping with prefixes.
 * 
 * Example usage:
 * $router = new Router();
 * $router->get('/menu', [MenuController::class, 'index']);
 * $router->post('/orders', [OrderController::class, 'create']);
 * $router->get('/orders/{id}', [OrderController::class, 'show']);
 * $router->group('/api/v1', function(Router $r) {
 *     $r->get('/menu', [MenuApiController::class, 'index']);
 * });
 *
 * The dispatch method is called in the front controller (e.g. public/index.php) to process the incoming HTTP request and generate a response.
*/

class Router 
{
     /** @var array<int, array{method: string, pattern: string, handler: array|callable}> */
    private array  $routes   = [];
    private string $groupPrefix = '';


    //Route Registration
    public function get(string $uri, array|callable $handler): void
    {
        $this->addRoute('GET', $uri, $handler);
    }

    public function post(string $uri, array|callable $handler): void
    {
        $this->addRoute('POST', $uri, $handler);
    }

    public function put(string $uri, array|callable $handler): void
    {
        $this->addRoute('PUT', $uri, $handler);
    }

    public function patch(string $uri, array|callable $handler): void
    {
        $this->addRoute('PATCH', $uri, $handler);
    }

    public function delete(string $uri, array|callable $handler): void
    {
        $this->addRoute('DELETE', $uri, $handler);
    }

    /** Grouping routes under a shared prefix, e.g. /api, /admin, etc. 
     * $router->group('/api/v1', function(Router $r) {
     *     $r->get('/menu', [MenuApiController::class, 'index']);
     * });
     * 
     * // Registers: GET /api/v1/menu
    */
    public function group(string $prefix, callable $callback): void
    {
        $previous          = $this->groupPrefix;
        $this->groupPrefix = $previous . $prefix;
        $callback($this);
        $this->groupPrefix = $previous; // restore after group
    }

    //Dispatching
    public function dispatch(Request $request, Response $response): void
    {
        $method = $request->getMethod();
        $uri    = rtrim($request->getUri(), '/') ?: '/'; // Normalize URI, treat empty as "/"

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match($route['pattern'], $uri);

            if ($params === null) {
                continue;
            }

            // Inject matched route params into Request
            $request->setParams($params);

            $this->invoke($route['handler'], $request, $response);
            return;
        }

        // No route matched, return 404
        $response->notFound("Route not found for {$method} {$uri}");
    }

    //Internal Helpers
    private function addRoute(string $method, string $uri, array|callable $handler): void
    {
        $pattern = $this->groupPrefix . $uri;

        $this->routes[] = [
            'method'  => $method,
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }

    /**
     * Match a URI against a route pattern.
     * Returns named param map on match, null on no match.
     *
     * Pattern: /orders/{id}/items/{itemId}
     * URI:     /orders/42/items/7
     * Returns: ['id' => '42', 'itemId' => '7']
     */
    private function match(string $pattern, string $uri): ?array
    {
        // Normalise trailing slashes
        $pattern = rtrim($pattern, '/') ?: '/';

        // Convert {param} → named capture group
        $regex = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $uri, $matches)) {
            return null;
        }

        // Return only named string keys (strip numeric indices from preg_match)
        return array_filter(
            $matches,
            fn($key) => is_string($key),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Invoke either a [ControllerClass, 'method'] pair or a raw callable.
     */
    private function invoke(array|callable $handler, Request $request, Response $response): void
    {
        if (is_callable($handler)) {
            $handler($request, $response);
            return;
        }

        // [ClassName::class, 'methodName']
        [$class, $method] = $handler;

        if (!class_exists($class)) {
            throw new \RuntimeException("Controller not found: {$class}");
        }

        $controller = new $class();

        if (!method_exists($controller, $method)) {
            throw new \RuntimeException("Method {$method} not found on {$class}");
        }

        $controller->{$method}($request, $response);
    }

/**
 * Load routes from an external file.
 *
 * The file receives $router as a local variable so it can
 * call $router->get(...) / $router->group(...) directly.
 *
 * Usage in index.php:
 *   $router->loadRoutesFrom(BASE_PATH . '/routes/web.php');
 *   $router->loadRoutesFrom(BASE_PATH . '/routes/api.php');
 */
public function loadRoutesFrom(string $filePath): void
{
    if (!file_exists($filePath)) {
        throw new \RuntimeException("Routes file not found: {$filePath}");
    }

    // $router is available inside the routes file as a local variable
    $router = $this;
    require $filePath;
}
}