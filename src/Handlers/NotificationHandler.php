<?php

namespace App\Handlers;

use App\Infrastructure\EmailService;
use App\Infrastructure\TemplateEngine;
use App\Core\Config;

class NotificationHandler {
    private EmailService $emailService;

    public function __construct() {
        $this->emailService = new EmailService();
    }

    public function sendReservationConfirmation(string $email, string $name, array $details): bool {
        $subject = "Reservation Confirmed: Your Table at Gourmet Express Awaits";
        
        // Use the TemplateEngine to generate the HTML string
        $htmlBody = TemplateEngine::render('emails/reservation-confirmed.php', [
            'name'    => $name,
            'details' => $details,
            'app_url' => Config::get('app.url')
        ]);

        return $this->emailService->send($email, $subject, $htmlBody);
    }
}