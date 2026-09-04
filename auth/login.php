<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
$redirect = $_POST['redirect'] ?? ($_GET['redirect'] ?? 'index.php');
if ($redirect === '' || strpos($redirect, 'auth/') !== false) {
    $redirect = 'index.php';
}
if ($redirect[0] !== '/' && strpos($redirect, 'http') !== 0) {
    $redirect = '../' . $redirect;
}

function back_with(string $url, string $key, string $value): void {
    $sep = (strpos($url, '?') !== false) ? '&' : '?';
    header('Location: ' . $url . $sep . $key . '=' . urlencode($value));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        back_with($redirect, 'login_error', 'Please fill in both fields.');
    }

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        header('Location: ' . $redirect);
        exit;
    }

    back_with($redirect, 'login_error', 'Invalid email or password.');
} else {
    header('Location: ../index.php');
    exit;
}
