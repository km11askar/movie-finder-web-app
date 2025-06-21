<?php
// send-email.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once 'config.php';

/**
 * Initializes and configures a PHPMailer instance with settings from config.php.
 * @return PHPMailer
 * @throws Exception
 */
function initializeMailer() {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Using the class constant is more robust
    $mail->Port = SMTP_PORT;
    return $mail;
}

function send_user_email($submission) {
    $mail = initializeMailer();
    $mail->setFrom(SMTP_USER, 'eBEYONDS');
    $mail->addAddress($submission['email'], $submission['firstName'] . ' ' . $submission['lastName']);
    $mail->isHTML(true);
    $mail->Subject = 'Thank you for contacting us - eBEYONDS';
    $mail->Body = "
        <h2>Thank you for contacting us!</h2>
        <p>Dear {$submission['firstName']} {$submission['lastName']},</p>
        <p>We have received your message and will get back to you soon.</p>
        <h3>Your Submission:</h3>
        <ul>
            <li><strong>Name:</strong> {$submission['firstName']} {$submission['lastName']}</li>
            <li><strong>Email:</strong> {$submission['email']}</li>
            <li><strong>Phone:</strong> {$submission['phone']}</li>
            <li><strong>Message:</strong> {$submission['comments']}</li>
        </ul>
        <p>Best regards,<br>eBEYONDS Team</p>
    ";
    $mail->AltBody = "Thank you for contacting us, {$submission['firstName']} {$submission['lastName']}.

We have received your message and will get back to you soon.

Your Submission:
Name: {$submission['firstName']} {$submission['lastName']}
Email: {$submission['email']}
Phone: {$submission['phone']}
Message: {$submission['comments']}

Best regards,
eBEYONDS Team";
    $mail->send();
}

function send_admin_email($submission) {
    $mail = initializeMailer();
    $mail->setFrom(SMTP_USER, 'eBEYONDS Website');
    foreach (ADMIN_EMAILS as $admin) {
        $mail->addAddress($admin);
    }
    $mail->isHTML(true);
    $mail->Subject = 'New Contact Form Submission - Website';
    $mail->Body = "
        <h2>New Contact Form Submission</h2>
        <ul>
            <li><strong>Name:</strong> {$submission['firstName']} {$submission['lastName']}</li>
            <li><strong>Email:</strong> {$submission['email']}</li>
            <li><strong>Phone:</strong> {$submission['phone']}</li>
            <li><strong>Message:</strong> {$submission['comments']}</li>
            <li><strong>Timestamp:</strong> {$submission['timestamp']}</li>
            <li><strong>IP Address:</strong> {$submission['ip_address']}</li>
        </ul>
    ";
    $mail->AltBody = "New Contact Form Submission\nName: {$submission['firstName']} {$submission['lastName']}\nEmail: {$submission['email']}\nPhone: {$submission['phone']}\nMessage: {$submission['comments']}\nTimestamp: {$submission['timestamp']}\nIP Address: {$submission['ip_address']}";
    $mail->send();
} 