<?php

namespace App\Infrastructure;

use App\Core\Config;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private PHPMailer $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);

        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host       = Config::get('mail.host');
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = Config::get('mail.user');
        $this->mailer->Password   = Config::get('mail.pass');
        $this->mailer->Port       = Config::get('mail.port');
        $this->mailer->SMTPSecure = Config::get('mail.encryption') == 'ssl' 
            ? PHPMailer::ENCRYPTION_SMTPS 
            : PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->CharSet    = 'UTF-8';

        // Sender settings
        $this->mailer->setFrom(
            Config::get('mail.from_address'), 
            Config::get('mail.from_name')
        );
    }

    public function send(string $to, string $subject, string $body): bool {
        try {
            $this->mailer->clearAddresses(); // prevent bleed between calls
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;
            $this->mailer->AltBody = strip_tags($body); // plain-text fallback

            return $this->mailer->send();
        } catch (Exception $e) {
            // Log the error if debug is on
            if (Config::get('app.debug')) {
                Logger::error("Mailer failed for {$to}", ['error' => $this->mailer->ErrorInfo]);
            }
            return false;
        }
    }
}