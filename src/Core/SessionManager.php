<?php

namespace App\Core;

use App\Constants\AppConstants;

/**
 * SessionManager
 *
 * Single source of truth for all session operations.
 * Call SessionManager::start() once at boot in index.php.
 * Nothing else in the app touches $_SESSION directly.
 */
class SessionManager
{
    private static bool $started = false;

    // Boot the session with secure cookie settings
    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Scope the session cookie to whichever host initiated it
        $host = HostGuard::getCurrentHost();

        session_set_cookie_params([
            'lifetime' => AppConstants::SESSION_LIFETIME_MINUTES * 60,
            'path'     => '/',
            'domain'   => $host, // Cookie only sent to the host that set it
            'secure'   => isset($_SERVER['HTTPS']),  // HTTPS only in production
            'httponly' => true,   // JS cannot read the cookie — XSS protection
            'samesite' => 'Lax', // CSRF protection
        ]);

        session_name('GE_' . md5($host));
        session_start();
        self::$started = true;
    }

    // Read / Write 
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    // Auth helpers 
    public static function setUser(array $user): void
    {
        // Regenerate session ID on privilege change (login/logout)
        session_regenerate_id(true);

        self::set('user_id',   $user['id']);
        self::set('user_name', $user['username']);
        self::set('user_role', $user['role']);
    }

    public static function getUser(): ?array
    {
        if (!self::has('user_id')) {
            return null;
        }

        return [
            'id'   => self::get('user_id'),
            'name' => self::get('user_name'),
            'role' => self::get('user_role'),
        ];
    }

    public static function isLoggedIn(): bool
    {
        return self::has('user_id');
    }

    // Flash messages (only persist for the next request, then auto-delete)
    public static function flash(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }

    public static function getFlash(string $key): ?string
    {
        $message = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $message;
    }

    // Destroy session on logout
    public static function destroy(): void
    {
        session_unset();
        session_destroy();
        self::$started = false;
    }
}