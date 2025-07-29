<?php
// Contact Form Processing Script
header('Content-Type: application/json');

// Allow CORS for development
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get form data
$name = trim($_POST['name'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validate required fields
if (empty($name) || empty($mobile) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Validate mobile number (basic validation for Indian numbers)
if (!preg_match('/^[0-9]{10,15}$/', $mobile)) {
    echo json_encode(['success' => false, 'message' => 'Invalid mobile number']);
    exit;
}

// Sanitize inputs
$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$mobile = htmlspecialchars($mobile, ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$subject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

// Create data array
$contactData = [
    'timestamp' => date('Y-m-d H:i:s'),
    'name' => $name,
    'mobile' => $mobile,
    'email' => $email,
    'subject' => $subject,
    'message' => $message,
    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
];

// Store data to file
$dataFile = 'contact_submissions.json';
$submissions = [];

// Read existing submissions
if (file_exists($dataFile)) {
    $existingData = file_get_contents($dataFile);
    if ($existingData) {
        $submissions = json_decode($existingData, true) ?: [];
    }
}

// Add new submission
$submissions[] = $contactData;

// Save back to file
if (file_put_contents($dataFile, json_encode($submissions, JSON_PRETTY_PRINT))) {
    $fileSaved = true;
} else {
    $fileSaved = false;
}

// Send email notification
$to = 'rajshreemineral@gmail.com'; // Your email address
$emailSubject = "New Contact Form Submission: $subject";
$emailBody = "
New contact form submission received:

Name: $name
Mobile: $mobile
Email: $email
Subject: $subject
Message: $message

Submitted on: " . date('Y-m-d H:i:s') . "
IP Address: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "

---
This email was sent from your website contact form.
";

$headers = [
    'From: noreply@rajshreemineral.com',
    'Reply-To: ' . $email,
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: PHP/' . phpversion()
];

$emailSent = mail($to, $emailSubject, $emailBody, implode("\r\n", $headers));

// Send auto-reply to customer
$customerSubject = "Thank you for contacting Raj Shree Minerals";
$customerBody = "
Dear $name,

Thank you for contacting Raj Shree Minerals. We have received your message and will get back to you within 24 hours.

Your message details:
Subject: $subject
Message: $message

If you have any urgent queries, please call us at +91 94271 01391.

Best regards,
Raj Shree Minerals Team
+91 94271 01391
rajshreemineral@gmail.com
";

$customerHeaders = [
    'From: Raj Shree Minerals <noreply@rajshreemineral.com>',
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: PHP/' . phpversion()
];

$autoReplySent = mail($email, $customerSubject, $customerBody, implode("\r\n", $customerHeaders));

// Return response
if ($fileSaved) {
    echo json_encode([
        'success' => true, 
        'message' => 'Thank you for contacting us! We will get back to you within 24 hours.',
        'email_sent' => $emailSent,
        'auto_reply_sent' => $autoReplySent
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Sorry, there was an error saving your message. Please try again or call us directly.'
    ]);
}
?> 