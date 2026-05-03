<?php
namespace App\Constants;

class AppConstants {
    public const ADMIN_HOST               = 'admin.gourmet-express.local';
    public const WEBSITE_HOST             = 'gourmet-express.local';
    public const SESSION_LIFETIME_MINUTES = 60; 
    public const BCRYPT_COST              = 12; 
    public const MAX_RESERVATION_GUESTS   = 10;
    public const MAX_FUTURE_BOOKING_DAYS  = 60;
    public const DEFAULT_CURRENCY         = 'EUR';
    public const DEFAULT_TIMEZONE         = 'Europe/Berlin';
    public const ITEMS_PER_PAGE           = 15;
}