<?php
// This endpoint expects a JSON POST body with: name, email, position, status
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request payload.']);
    exit;
}

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$position = trim($data['position'] ?? '');
$status = trim($data['status'] ?? '');

// Basic server-side validation
if (!$name || !$email || !$position || !in_array($status, ['selected', 'rejected'])) {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

$subject = "Application Update - $position";

if ($status === 'selected') {
    $body = "Dear $name\n\nWe are pleased to inform you that you have been selected for the position of $position.\n\nPlease reply to this email to confirm your acceptance.\n\nBest regards,\nHR Team";
} else {
    $body = "Dear $name\n\nThank you for applying for the position of $position.\n\nWe regret to inform you that we have decided to move forward with other candidates.\n\nBest regards,\nHR Team";
}

// Use PHPMailer (preferred) if available
$autoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    echo json_encode(['success' => false, 'message' => 'Mailer not installed. Run `composer require phpmailer/phpmailer` and configure SMTP in config.php.']);
    exit;
}

require $autoload;
$config = require __DIR__ . '/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = $config['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['smtp_user'];
    $mail->Password = $config['smtp_pass'];
    $mail->SMTPSecure = $config['smtp_secure'];
    $mail->Port = $config['smtp_port'];

    $mail->setFrom($config['from_email'], $config['from_name']);
    $mail->addAddress($email, $name);

    // Content
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AltBody = $body;

    if ($mail->send()) {
        echo json_encode(['success' => true, 'message' => 'Email sent successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send email.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
}

?>
