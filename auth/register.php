<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$redirect = $_POST['redirect'] ?? 'index.php';
if ($redirect === '' || strpos($redirect, 'auth/') !== false) {
    $redirect = 'index.php';
}
if ($redirect[0] !== '/' && strpos($redirect, 'http') !== 0) {
    $redirect = '../' . $redirect;
}

function back_with_signup(string $url, string $key, string $value): void {
    $sep = (strpos($url, '?') !== false) ? '&' : '?';
    header('Location: ' . $url . $sep . $key . '=' . urlencode($value));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = sanitize($_POST['signupname'] ?? '');
    $email    = sanitize($_POST['signupEmail'] ?? '');
    $password = $_POST['signupPassword'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        back_with_signup($redirect, 'signup_error', 'Please fill in all fields.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        back_with_signup($redirect, 'signup_error', 'Please enter a valid email.');
    }
    if (strlen($password) < 6) {
        back_with_signup($redirect, 'signup_error', 'Password must be at least 6 characters.');
    }

    $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $check->execute([$email]);
    if ($check->fetch()) {
        back_with_signup($redirect, 'signup_error', 'An account with this email already exists.');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $insert = $pdo->prepare('INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)');
    $insert->execute([$name, $email, $hash]);

    // Auto login
    session_regenerate_id(true);
    $_SESSION['user_id']    = $pdo->lastInsertId();
    $_SESSION['user_name']  = $name;
    $_SESSION['user_email'] = $email;

    header('Location: ' . $redirect);
    exit;
} else {
    header('Location: ../index.php');
    exit;
}
