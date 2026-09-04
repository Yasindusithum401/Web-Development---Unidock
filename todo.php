<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if (!is_logged_in()) {
    json_response(['success' => false, 'message' => 'Please log in first.'], 401);
}

$userId = current_user_id();
$action = $_REQUEST['action'] ?? 'list';

switch ($action) {
    case 'list':
        $stmt = $pdo->prepare('SELECT * FROM todos WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$userId]);
        json_response(['success' => true, 'todos' => $stmt->fetchAll()]);
        break;

    case 'add':
        $title = sanitize($_POST['title'] ?? '');
        $note  = sanitize($_POST['note'] ?? '');
        if ($title === '') {
            json_response(['success' => false, 'message' => 'Title is required.'], 422);
        }
        $stmt = $pdo->prepare('INSERT INTO todos (user_id, title, note) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $title, $note]);
        json_response(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;

    case 'toggle':
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('UPDATE todos SET completed = NOT completed WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        json_response(['success' => true]);
        break;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM todos WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        json_response(['success' => true]);
        break;

    case 'clear':
        $stmt = $pdo->prepare('DELETE FROM todos WHERE user_id = ?');
        $stmt->execute([$userId]);
        json_response(['success' => true]);
        break;

    default:
        json_response(['success' => false, 'message' => 'Unknown action.'], 400);
}
