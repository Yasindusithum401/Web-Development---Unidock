<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if (!is_logged_in()) {
    header('Location: auth/login.php?redirect=' . urlencode('features.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: features.php');
    exit;
}

$title        = sanitize($_POST['title'] ?? '');
$subject      = sanitize($_POST['subject'] ?? '');
$subjectCode  = sanitize($_POST['subjectCode'] ?? '');

if ($title === '' || $subject === '' || empty($_FILES['resource']['name'])) {
    header('Location: features.php?upload_error=' . urlencode('Please fill in all fields and choose a PDF.'));
    exit;
}

$file = $_FILES['resource'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    header('Location: features.php?upload_error=' . urlencode('File upload failed. Please try again.'));
    exit;
}

$finfo    = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if ($mimeType !== 'application/pdf') {
    header('Location: features.php?upload_error=' . urlencode('Only PDF files are allowed.'));
    exit;
}

$maxBytes = 10 * 1024 * 1024; // 10 mb only section eka 
if ($file['size'] > $maxBytes) {
    header('Location: features.php?upload_error=' . urlencode('File is too large (10MB max).'));
    exit;
}

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$safeName = preg_replace('/[^A-Za-z0-9_\-]/', '-', pathinfo($file['name'], PATHINFO_FILENAME));
$fileName = $safeName . '-' . uniqid() . '.pdf';
$destPath = $uploadDir . $fileName;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    header('Location: features.php?upload_error=' . urlencode('Could not save the file on the server.'));
    exit;
}

$stmt = $pdo->prepare('INSERT INTO resources (title, subject, subject_code, file_path, uploaded_by) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([$title, $subject, $subjectCode, 'uploads/' . $fileName, current_user_id()]);

header('Location: features.php?upload_success=1');
exit;
