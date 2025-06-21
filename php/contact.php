<?php
// contact.php
header('Content-Type: application/json');
require_once 'config.php';

session_start();


// Rate limiting
$ip = $_SERVER['REMOTE_ADDR'];
$last_time = $_SESSION['last_submit_time'][$ip] ?? 0;
if (time() - $last_time < RATE_LIMIT_SECONDS) {
    echo json_encode(['success' => false, 'message' => 'Please wait before submitting again.']);
    exit;
}
$_SESSION['last_submit_time'][$ip] = time();

// Sanitize and validate input
function clean($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}
$firstName = clean($_POST['firstName'] ?? '');
$lastName = clean($_POST['lastName'] ?? '');
$email = clean($_POST['email'] ?? '');
$phone = clean($_POST['phone'] ?? '');
$comments = clean($_POST['comments'] ?? '');

if (!$firstName || !$lastName || !$email || !$comments) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

// Save to JSON
$submission = [
    'id' => uniqid(),
    'firstName' => $firstName,
    'lastName' => $lastName,
    'email' => $email,
    'phone' => $phone,
    'comments' => $comments,
    'timestamp' => date('Y-m-d H:i:s'),
    'ip_address' => $ip
];

$file = SUBMISSIONS_FILE;
if (!file_exists($file)) file_put_contents($file, '{"submissions":[]}');
$fp = fopen($file, 'r+');
if (flock($fp, LOCK_EX)) {
    $data = json_decode(fread($fp, filesize($file)), true);
    if (!$data) $data = ['submissions' => []];
    $data['submissions'][] = $submission;
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));
    fflush($fp);
    flock($fp, LOCK_UN);
}
fclose($fp);

// Send emails
require_once 'send-email.php';
try {
    send_user_email($submission);
    send_admin_email($submission);
} catch (Exception $e) {
    error_log($e->getMessage(), 3, ERROR_LOG_FILE);
    echo json_encode(['success' => false, 'message' => 'Submission saved, but email failed.']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Thank you for contacting us! We have received your message.']);
