<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Infrastructure\TemplateEngine;

/**
 * BaseController provides common functionality for all controllers.
 */
abstract class BaseController 
{
    /**
     * Render a view template and send it as an HTML response.
     *
     * Usage in a controller:
     *   return $this->view($response, 'pages/menu.php', ['dishes' => $dishes]);
     */
    protected function view( Response $response, string $template, array $data = []): never
    {
        $html = TemplateEngine::render($template, $data);
        $response->html($html);
    }

    /**
     * Redirect to another URL.
     */
    protected function redirect(Response $response, string $url, int $status = 302): never
    {
        $response->redirect($url, $status);
    }

    /**
     * Pull a value from the request body with an optional default.
     * Usage:
     *  $email = $this->input($request, 'email', '');
     */
    protected function input(Request $request, string $key, mixed $default = null): mixed
    {
        return $request->input($key, $default);
    }

}