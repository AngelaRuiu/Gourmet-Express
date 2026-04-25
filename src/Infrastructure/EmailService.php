<?php

namespace App\Infrastructure;

use App\Core\Config;
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
        
        // Sender settings
        $this->mailer->setFrom(
            Config::get('mail.from_address', 'no-reply@gourmet-express.com'), 
            Config::get('mail.from_name', 'Gourmet Express')
        );
    }

    public function send(string $to, string $subject, string $body): bool {
        try {
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;

            return $this->mailer->send();
        } catch (Exception $e) {
            // Log the error if debug is on
            if (Config::get('app.debug')) {
                error_log("Mailer Error: {$this->mailer->ErrorInfo}");
            }
            return false;
        }
    }
}