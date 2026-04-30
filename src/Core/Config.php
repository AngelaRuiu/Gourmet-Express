<?php

namespace App\Core;

use App\Constants\AppConstants;

/**
 * Class Config
 * Central Registry for application settings. 
 */
class Config {
    private static array $registry = [];

    public static function initialize(): void {
        $base = dirname(__DIR__, 2); // Base path of the project/ Project root

        self::$registry = [
            'app' => [
                'name'  => $_ENV['APP_NAME'] ?? 'Gourmet Express',
                'env'   => $_ENV['APP_ENV'] ?? 'production',
                'url'   => $_ENV['APP_URL'] ?? 'http://localhost',
                'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'timezone' => $_ENV['APP_TIMEZONE'] ?? AppConstants::DEFAULT_TIMEZONE,
                'currency' => $_ENV['APP_CURRENCY'] ?? AppConstants::DEFAULT_CURRENCY,
                'base_path' => $base,
            ],
            'paths' => [
                'base_path' => $base,
                'public'    => $base . '/public',
                'views'     => $base . '/views',
                'storage'   => $base . '/storage',
                'logs'      => $base . '/storage/logs',
                'cache'     => $base . '/storage/cache',
            ],
            'db' => [
                'host' => $_ENV['DB_HOST'] ?? 'db',
                'port' => $_ENV['DB_PORT'] ?? 3306,
                'name' => $_ENV['DB_NAME'] ?? '',
                'user' => $_ENV['DB_USER'] ?? '',
                'pass' => $_ENV['DB_PASS'] ?? '',
            ],
            'mail' => [
                'host' => $_ENV['MAIL_HOST'] ?? '',
                'port' => $_ENV['MAIL_PORT'] ?? 2525,
                'user' => $_ENV['MAIL_USER'] ?? '',
                'pass' => $_ENV['MAIL_PASS'] ?? '',
                'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'no-reply@gourmet-express.com',
                'from_name'    => $_ENV['MAIL_FROM_NAME']    ?? 'Gourmet Express',
                'encryption'   => $_ENV['MAIL_ENCRYPTION']   ?? 'tls',
            ],
            'api' => [
                'google_key'    => $_ENV['GOOGLE_MAPS_API_KEY'] ?? '',
                'paypal_id'     => $_ENV['PAYPAL_CLIENT_ID'] ?? '',
                'paypal_secret' => $_ENV['PAYPAL_CLIENT_SECRET'] ?? '',
                'paypal_env'    => $_ENV['PAYPAL_ENV'] ?? 'sandbox',
            ],
        ];
    }

    /**
     * Get a config value using dot notation (e.g., 'app.name' or 'api.paypal_id')
     */
    public static function get(string $key, mixed $default = null) {
        $parts = explode('.', $key);
        $value = self::$registry;

        foreach ($parts as $part) {
            if (!isset($value[$part])) {
                return $default;
            }
            $value = $value[$part];
        }

        return $value;
    }

    /**
     * Helper to check if we are in a local/development environment
     */
    public static function isDev(): bool {
        $env = self::get('app.env');
        return $env === 'local' || $env === 'development';
    }

    public static function isProduction(): bool
    {
        return self::get('app.env') === 'production';
    }

    /**
     * Ensures we never hit production APIs during development
     */
    public static function isSandbox(): bool {
        return self::get('api.paypal_env') === 'sandbox';
    }
}