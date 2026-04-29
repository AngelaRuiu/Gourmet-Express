<?php
namespace App\Infrastructure;

class Config {
    public const SITE_NAME = "The Gourmet Bistro";
    public const API_VERSION = "v1";
    
    // Path helpers
    public static function getTemplatePath(): string {
        return dirname(__DIR__, 2) . '/templates';
    }
}