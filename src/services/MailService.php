<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailService {
    private $mail;
    private $config;
    
    public function __construct() {
        // Load mail configuration
        $this->config = require_once __DIR__ . '/../config/mail.php';
        
        // Include PHPMailer autoloader
        require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';
        require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
        require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';
        
        $this->mail = new PHPMailer(true);
        
        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host       = $this->config['mail_host'];
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = $this->config['mail_username'];
        $this->mail->Password   = $this->config['mail_password'];
        $this->mail->SMTPSecure = $this->config['mail_encryption'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = $this->config['mail_port'];
        // Enable verbose debug output
        $this->mail->SMTPDebug = 0; // Set to 2 for more detailed debug output
    }
    
    public function getConfig() {
        return $this->config;
    }
    
    public function sendRegistrationEmail($to, $name) {
        // Render email template
        ob_start();
        include __DIR__ . '/../views/emails/registration.php';
        $body = ob_get_clean();
        
        try {
            // Recipients
            $this->mail->setFrom($this->config['mail_from_address'], $this->config['mail_from_name']);
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
    
    public function sendVerificationEmail($to, $name, $verificationToken) {
        // Get domain configuration
        $domainConfig = require_once __DIR__ . '/../config/domain.php';
        $domain = $domainConfig['domain'];
        
        // Create verification link
        $verificationLink = $domain . '/verify-email?token=' . $verificationToken;
        
        // Data for email template
        $data = [
            'name' => $name,
            'verificationLink' => $verificationLink
        ];
        
        // Render email template
        ob_start();
        include __DIR__ . '/../views/emails/verification.php';
        $body = ob_get_clean();
        
        try {
            // Recipients
            $this->mail->setFrom($this->config['mail_from_address'], $this->config['mail_from_name']);
            $this->mail->addAddress($to, $name);
            
            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Verify your email address for Lib4All';
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
            $this->mail->setFrom($this->config['mail_from_address'], $this->config['mail_from_name']);
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
    
    public function sendTestEmail($to, $toName = 'Lib4All User') {
        try {
            // Recipients
            $this->mail->setFrom($this->config['mail_from_address'], $this->config['mail_from_name']);
            $this->mail->addAddress($to, $toName);
            
            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Lib4All Email Configuration Test';
            $this->mail->Body    = '<h1>Lib4All Email Test</h1><p>This is a test email to confirm that the email configuration is working correctly.</p><p>If you received this email, it means the email system is properly configured.</p>';
            $this->mail->AltBody = 'Lib4All Email Test - This is a test email to confirm that the email configuration is working correctly. If you received this email, it means the email system is properly configured.';
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
            // Re-throw the exception so the controller can catch it
            throw new Exception("Mailer Error: " . $this->mail->ErrorInfo);
        }
    }
    
    public function sendContactConfirmation($to, $name, $subject, $message) {
        // Data for email template
        $data = [
            'name' => $name,
            'subject' => $subject,
            'message' => $message
        ];
        
        // Render email template
        ob_start();
        include __DIR__ . '/../views/emails/contact_confirmation.php';
        $body = ob_get_clean();
        
        try {
            // Recipients
            $this->mail->setFrom($this->config['mail_from_address'], $this->config['mail_from_name']);
            $this->mail->addAddress($to, $name);
            
            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Thank You for Contacting Lib4All';
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