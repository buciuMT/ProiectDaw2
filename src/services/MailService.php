<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailService {
    private $mail;
    
    public function __construct() {
        // Include PHPMailer autoloader
        require_once __DIR__ . '/lib/src/Exception.php';
        require_once __DIR__ . '/lib/src/PHPMailer.php';
        require_once __DIR__ . '/lib/src/SMTP.php';
        
        $this->mail = new PHPMailer(true);
        
        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host       = 'smtp.example.com'; // Set the SMTP server to send through
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = 'user@example.com'; // SMTP username
        $this->mail->Password   = 'secret'; // SMTP password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = 587;
    }
    
    public function sendRegistrationEmail($to, $name) {
        // Render email template
        ob_start();
        include __DIR__ . '/../views/emails/registration.php';
        $body = ob_get_clean();
        
        try {
            // Recipients
            $this->mail->setFrom('noreply@lib4all.com', 'Lib4All');
            $this->mail->addAddress($to, $name);
            
            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Welcome to Lib4All!';
            $this->mail->Body    = $body;
            $this->mail->AltBody = strip_tags($body);
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }
    
    public function sendReservationConfirmation($to, $name, $bookTitle) {
        // Data for email template
        $data = [
            'name' => $name,
            'bookTitle' => $bookTitle
        ];
        
        // Render email template
        ob_start();
        include __DIR__ . '/../views/emails/reservation_confirmation.php';
        $body = ob_get_clean();
        
        try {
            // Recipients
            $this->mail->setFrom('noreply@lib4all.com', 'Lib4All');
            $this->mail->addAddress($to, $name);
            
            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Book Reservation Confirmation';
            $this->mail->Body    = $body;
            $this->mail->AltBody = strip_tags($body);
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }
}