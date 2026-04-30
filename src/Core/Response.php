<?php

namespace App\Core;

use App\Constants\AppConstants;

/**
 * A simple response class to standardize API responses across the application.
 */
class Response 
{
    private int   $statusCode = 200;
    private array $headers    = [];

    // Status
    public function setStatus(int $code): static
    {
        $this->statusCode = $code;
        return $this;
    }

    public function setHeader(string $key, string $value): static
    {
        $this->headers[$key] = $value;
        return $this;
    }

    //JSON Response

    /**
     * Sends a standardized JSON response and terminates the script.
     *
     * @param mixed  $data       The data to include in the response (optional).
     * @param int    $statusCode The HTTP status code to send (default: 200).
     * @param string $message    An optional message to include in the response.
     */
    public function json(mixed $data = null, int $statusCode = 200, string $message = '' ): never 
    {
        $this->setStatus($statusCode)
                ->setHeader('Content-Type', 'application/json; charset=utf-8');

        $this->sendheaders();

        echo json_encode([
            'success' => $statusCode < 400,
            'message' => $message,
            'data'    => $data,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function success(mixed $data = null, string $message = ''): never
    {
        $this->json($data, 200, $message);
    }

    public function error(string $message, int $statusCode = 400, mixed $data = null): never
    {
        $this->json($data, $statusCode, $message);
    }

    public function created(mixed $data = null, string $message = 'Resource created successfully.'): never
    {
        $this->json($data, 201, $message);
    }

    public function noContent(): never
    {
        $this->setStatus(204)->sendheaders();
        exit;
    }

    // HTML/Views
    
    public function html(string $content, int $statusCode = 200): never
    {
        $this->setStatus($statusCode)
             ->setHeader('Content-Type', 'text/html; charset=utf-8')->sendheaders();

        echo $content;
        exit;
    }

     // Redirects
    public function redirect(string $url, int $statusCode = 302): never
    {
        $this->setStatus($statusCode)
             ->setHeader('Location', $url)->sendheaders();
        exit;
    }

    public function back(string $fallbackUrl = '/'): never
    {
        $url = $_SERVER['HTTP_REFERER'] ?? $fallbackUrl;
        $this->redirect($url);
    }

    //Common error responses

    public function notFound(string $message = 'Not found.'): never
    {
        $this->error($message, 404);
    }

    public function unauthorized(string $message = 'Unauthorized.'): never
    {
        $this->error($message, 401);
    }

    public function forbidden(string $message = 'Forbidden.'): never
    {
        $this->error($message, 403);
    }

    public function serverError(string $message = 'Internal Server Error.'): never
    {
        $this->error($message, 500);
    }

    // Internal helpers
    private function sendheaders(): void
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }
    }
}
