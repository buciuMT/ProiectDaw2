<?php
// Simple test script to verify mail configuration
require_once __DIR__ . '/bootstrap.php';

echo "ENV variables:\n";
echo "MAIL_HOST: " . ($_ENV['MAIL_HOST'] ?? 'NOT SET') . "\n";
echo "MAIL_PORT: " . ($_ENV['MAIL_PORT'] ?? 'NOT SET') . "\n";
echo "MAIL_USERNAME: " . ($_ENV['MAIL_USERNAME'] ?? 'NOT SET') . "\n";
echo "MAIL_FROM_ADDRESS: " . ($_ENV['MAIL_FROM_ADDRESS'] ?? 'NOT SET') . "\n";
echo "MAIL_FROM_NAME: " . ($_ENV['MAIL_FROM_NAME'] ?? 'NOT SET') . "\n";

// Load mail configuration
$config = require_once __DIR__ . '/config/mail.php';

echo "\nMail configuration:\n";
echo "Host: " . $config['mail_host'] . "\n";
echo "Port: " . $config['mail_port'] . "\n";
echo "Username: " . $config['mail_username'] . "\n";
echo "From Address: " . $config['mail_from_address'] . "\n";
echo "From Name: " . $config['mail_from_name'] . "\n";