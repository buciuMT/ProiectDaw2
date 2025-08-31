<?php

// Mail configuration
// Load from direct config file for environments where .env is not accessible
$mailConfig = require_once __DIR__ . '/mail_config.php';

return [
    'mail_host' => $mailConfig['mail_host'],
    'mail_username' => $mailConfig['mail_username'],
    'mail_password' => $mailConfig['mail_password'],
    'mail_port' => $mailConfig['mail_port'],
    'mail_encryption' => $mailConfig['mail_encryption'],
    'mail_from_address' => $mailConfig['mail_from_address'],
    'mail_from_name' => $mailConfig['mail_from_name']
];