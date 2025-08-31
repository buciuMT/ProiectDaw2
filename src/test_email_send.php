<?php
// Test script to verify email configuration and sending
require_once __DIR__ . '/bootstrap.php';

echo "Testing email configuration...
";

try {
    $mailService = new MailService();
    $config = $mailService->getConfig();
    
    echo "Mail configuration:
";
    echo "Host: " . $config['mail_host'] . "
";
    echo "Port: " . $config['mail_port'] . "
";
    echo "Username: " . $config['mail_username'] . "
";
    echo "From Address: " . $config['mail_from_address'] . "
";
    echo "From Name: " . $config['mail_from_name'] . "
";
    
    // Test sending email
    echo "
Testing email sending...
";
    $sent = $mailService->sendTestEmail('buciutheodormarian@gmail.com', 'Lib4All Test');
    
    if ($sent) {
        echo "Email sent successfully!
";
    } else {
        echo "Failed to send email.
";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "
";
    echo "Trace: " . $e->getTraceAsString() . "
";
}