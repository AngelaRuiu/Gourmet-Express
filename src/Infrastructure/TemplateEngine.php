<?php

namespace App\Infrastructure;

/**
 * Class TemplateEngine
 * A lightweight utility to render PHP files as strings with injected data.
 */
class TemplateEngine {
    /**
     * Renders a template file with given data.
     * * 
     * @param string $templatePath Relative path to the template file (e.g., 'emails/reservation.php')
     * @param array $data Associative array of variables to pass to the template
     */
    public static function render(string $templatePath, array $data = []): string {
        $fullPath = __DIR__ . '/../../views/' . $templatePath;

        if (!file_exists($fullPath)) {
            throw new \Exception("Template not found: {$templatePath}");
        }

        // Extract the array into local variables
        extract($data);

        // Start output buffering
        ob_start();

        // Include the file (it will have access to the extracted variables)
        include $fullPath;

        // Return the buffer content and clean it
        return ob_get_clean();
    }
}