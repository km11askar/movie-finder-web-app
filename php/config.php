<?php
// config.php

// SMTP settings for PHPMailer
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'askar123km@gmail.com'); // <-- CHANGE THIS
define('SMTP_PASS', 'zdoptxdecxwiixiz'); // <-- CHANGE THIS

// Admin emails
define('ADMIN_EMAILS', [
    'dumidu.kodithuwakku@ebeyonds.com',
    'prabhath.senadheera@ebeyonds.com'
]);

// Data file path
define('SUBMISSIONS_FILE', dirname(__DIR__) . '/data/submissions.json');


// Rate limiting
define('RATE_LIMIT_SECONDS', 60); // 1 submission per minute per IP

// Error log
define('ERROR_LOG_FILE', dirname(__DIR__) . '/data/error.log');
