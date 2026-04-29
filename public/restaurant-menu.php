<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    // Load Environment and Initialize Config
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
    \App\Core\Config::initialize();

    // Fetch Data using the MenuManager
    $menuManager = new \App\Managers\MenuManager(); 
    
    // Explicitly order by category for a better user experience
    $dishes = $menuManager->findAll('category_id ASC');

    // The view is rendered only if no exceptions were thrown during data fetch.
    include __DIR__ . '/../templates/views/menu-list.php';

} catch (\Exception $e) {
    // If in development mode, show the full error. Otherwise, show a polite message.
    if (\App\Core\Config::isDev()) {
        echo "<h1>Developer Error</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        echo "<h1>Something went wrong</h1>";
        echo "<p>We're having trouble loading the menu right now. Please try again later.</p>";
    }
    
    // Log the error for the admins to review, but don't expose details to the user in production.
    error_log($e->getMessage());
}