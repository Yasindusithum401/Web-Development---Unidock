<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/json');

if (isset($pdo)) {$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

$action =$_REQUEST['action'] ?? 'list';

switch ($action) {

    case 'list':
        try {
            $stmt =$pdo->query(
                "SELECT q.id, q.title, q.body, q.created_at, q.user_id, COALESCE(u.full_name, 'User') AS author,
                        (SELECT COUNT(*) FROM question_replies r WHERE r.question_id = q.id) AS reply_count
                 FROM questions q
                 JOIN users u ON u.id = q.user_id
                 ORDER BY q.created_at DESC"
            );
            json_response(['success' => true, 'questions' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
        }
        break;

    case 'ask':
        $userId =$_SESSION['user_id'] ?? (function_exists('current_user_id') ? current_user_id() : null);

        if (!$userId) {
            json_response(['success' => false, 'message' => 'Oya log in wela na! Karunakarala log in wenna.'], 401);
        }

        
        $userCheck =$pdo->prepare("SELECT id FROM users WHERE id = ?");
        $userCheck->execute([$userId]);
        if (!$userCheck->fetch()) {
            json_response(['success' => false, 'message' => 'User ID (' . $userId . ') eka users table eke soyaganna ba. Log out wela aith log wenna.'], 400);
        }

        $body = trim($_POST['body'] ?? '');
        if ($body === '') {
            json_response(['success' => false, 'message' => 'Message eka type karala yawanna.'], 422);
        }

        $title = mb_substr($body, 0, 100) . (mb_strlen($body) > 100 ? '...' : '');

        try {
            $stmt =$pdo->prepare('INSERT INTO questions (user_id, title, body) VALUES (?, ?, ?)');
            $stmt->execute([$userId, $title,$body]);

            json_response([
                'success' => true, 
                'id' => $pdo->lastInsertId(),
                'message' => 'Message saved successfully!'
            ]);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'SQL Error: ' . $e->getMessage()], 500);
        }
        break;

    case 'reply':
        $userId =$_SESSION['user_id'] ?? (function_exists('current_user_id') ? current_user_id() : null);
        if (!$userId) {
            json_response(['success' => false, 'message' => 'Reply karanna kalin log in wenna.'], 401);
        }

        $questionId = (int)($_POST['question_id'] ?? 0);
        $body = trim($_POST['body'] ?? '');

        if ($questionId <= 0 || $body === '') {
            json_response(['success' => false, 'message' => 'Message content missing.'], 422);
        }

        $userCheck =$pdo->prepare("SELECT id FROM users WHERE id = ?");
        $userCheck->execute([$userId]);
        if (!$userCheck->fetch()) {
            json_response(['success' => false, 'message' => 'User ID eka DB eke na.'], 400);
        }

        try {
            $stmt =$pdo->prepare('INSERT INTO question_replies (question_id, user_id, body) VALUES (?, ?, ?)');
            $stmt->execute([$questionId, $userId,$body]);

            json_response(['success' => true, 'id' => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'SQL Error: ' . $e->getMessage()], 500);
        }
        break;

    case 'view':
        $id = (int)($_GET['id'] ?? 0);
        try {
            $qStmt =$pdo->prepare(
                "SELECT q.id, q.title, q.body, q.created_at, q.user_id, u.full_name AS author
                 FROM questions q JOIN users u ON u.id = q.user_id
                 WHERE q.id = ?"
            );
            $qStmt->execute([$id]);
            $question =$qStmt->fetch(PDO::FETCH_ASSOC);

            $rStmt =$pdo->prepare(
                "SELECT r.id, r.body, r.created_at, r.user_id, u.full_name AS author
                 FROM question_replies r JOIN users u ON u.id = r.user_id
                 WHERE r.question_id = ? ORDER BY r.created_at ASC"
            );
            $rStmt->execute([$id]);

            json_response(['success' => true, 'question' => $question, 'replies' =>$rStmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'delete_question':
        $userId =$_SESSION['user_id'] ?? (function_exists('current_user_id') ? current_user_id() : null);
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt =$pdo->prepare('DELETE FROM questions WHERE id = ? AND user_id = ?');
            $stmt->execute([$id,$userId]);
            json_response(['success' => true]);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'delete_reply':
        $userId =$_SESSION['user_id'] ?? (function_exists('current_user_id') ? current_user_id() : null);
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt =$pdo->prepare('DELETE FROM question_replies WHERE id = ? AND user_id = ?');
            $stmt->execute([$id,$userId]);
            json_response(['success' => true]);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    default:
        json_response(['success' => false, 'message' => 'Invalid action'], 400);
}