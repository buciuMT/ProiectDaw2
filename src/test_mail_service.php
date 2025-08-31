<?php
// Simple test script to verify mail service
require_once __DIR__ . '/bootstrap.php';

$mailService = new MailService();
$config = $mailService->getConfig();

echo "Mail service config:\n";
echo "Host: " . $config['mail_host'] . "\n";
echo "Port: " . $config['mail_port'] . "\n";
echo "Username: " . $config['mail_username'] . "\n";
echo "From Address: " . $config['mail_from_address'] . "\n";
echo "From Name: " . $config['mail_from_name'] . "\n";