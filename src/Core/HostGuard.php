<?php

namespace App\Core;

use App\Constants\AppConstants;

/**
 * Enforces which domain can access which part of the application.
 * Called at the top of index.php before routing.
 *
 * Defence in depth: Nginx already blocks by IP.
 * This adds a second layer — host-based routing at the PHP level.
 * @package App\Core
 */
class HostGuard 
{
    public static string $currentHost = '';

    public static function initialize(): void
    { 
        self::$currentHost = strtolower(
            $_SERVER['HTTP_HOST'] ?? ''
        ); 
    }

    public static function isAdminHost(): bool
    {
        return self::$currentHost === AppConstants::ADMIN_HOST;
    }

    public static function isWebsiteHost(): bool
    {
        return self::$currentHost === AppConstants::WEBSITE_HOST;
    }

    /**
     * Abort if the current request is not coming from the admin host.
     * Returns 404 — not 403 — so the existence of the admin panel
     * is not confirmed to someone probing the public domain.
     */
    public static function requireAdminHost(): void
    {
        if (!self::isAdminHost()) {
            http_response_code(404);
            exit;
        }
    }

    /**
     * Abort if the current request is not coming from the public website host.
     * Returns 404 — not 403 — so the existence of the admin panel
     * is not confirmed to someone probing the public domain.
     */
    public static function requireWebsiteHost(): void
    {
        if (!self::isWebsiteHost()) {
            http_response_code(404);
            exit;
        }
    }

    public static function getCurrentHost(): string
    {
        return self::$currentHost;
    }


}