<?php
namespace App\Infrastructure;

class ApiService {
    /**
     * Sends a standardized JSON response.
     */
    public static function response(bool $success, mixed $data = null, string $message = ''): void {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data'    => $data,
        ]);
        exit;
    }

    public static function success(mixed $data = null): void {
        self::response(true, $data);
    }

    public static function error(string $message, int $code = 400): void {
        http_response_code($code);
        self::response(false, null, $message);
    }
}