<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? 'list';

switch ($action) {

    case 'list':
        $stmt = $pdo->query(
            "SELECT q.id, q.title, q.body, q.created_at, u.full_name AS author,
                    (SELECT COUNT(*) FROM question_replies r WHERE r.question_id = q.id) AS reply_count
             FROM questions q
             JOIN users u ON u.id = q.user_id
             ORDER BY q.created_at DESC"
        );
        json_response(['success' => true, 'questions' => $stmt->fetchAll()]);
        break;

    case 'view':
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            json_response(['success' => false, 'message' => 'Invalid question.'], 422);
        }

        $qStmt = $pdo->prepare(
            "SELECT q.id, q.title, q.body, q.created_at, q.user_id, u.full_name AS author
             FROM questions q JOIN users u ON u.id = q.user_id
             WHERE q.id = ?"
        );
        $qStmt->execute([$id]);
        $question = $qStmt->fetch();
        if (!$question) {
            json_response(['success' => false, 'message' => 'Question not found.'], 404);
        }

        $rStmt = $pdo->prepare(
            "SELECT r.id, r.body, r.created_at, r.user_id, u.full_name AS author
             FROM question_replies r JOIN users u ON u.id = r.user_id
             WHERE r.question_id = ? ORDER BY r.created_at ASC"
        );
        $rStmt->execute([$id]);

        json_response(['success' => true, 'question' => $question, 'replies' => $rStmt->fetchAll()]);
        break;

    case 'ask':
        if (!is_logged_in()) {
            json_response(['success' => false, 'message' => 'Please log in to ask a question.'], 401);
        }
        $title = sanitize($_POST['title'] ?? '');
        $body  = sanitize($_POST['body'] ?? '');
        if ($title === '' || $body === '') {
            json_response(['success' => false, 'message' => 'Please fill in both the title and your question.'], 422);
        }
        $stmt = $pdo->prepare('INSERT INTO questions (user_id, title, body) VALUES (?, ?, ?)');
        $stmt->execute([current_user_id(), $title, $body]);
        json_response(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;

    case 'reply':
        if (!is_logged_in()) {
            json_response(['success' => false, 'message' => 'Please log in to reply.'], 401);
        }
        $questionId = (int)($_POST['question_id'] ?? 0);
        $body       = sanitize($_POST['body'] ?? '');
        if ($questionId <= 0 || $body === '') {
            json_response(['success' => false, 'message' => 'Write a reply first.'], 422);
        }
        $check = $pdo->prepare('SELECT id FROM questions WHERE id = ?');
        $check->execute([$questionId]);
        if (!$check->fetch()) {
            json_response(['success' => false, 'message' => 'Question not found.'], 404);
        }
        $stmt = $pdo->prepare('INSERT INTO question_replies (question_id, user_id, body) VALUES (?, ?, ?)');
        $stmt->execute([$questionId, current_user_id(), $body]);
        json_response(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;

    case 'delete_question':
        if (!is_logged_in()) {
            json_response(['success' => false, 'message' => 'Please log in first.'], 401);
        }
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM questions WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, current_user_id()]);
        json_response(['success' => true]);
        break;

    case 'delete_reply':
        if (!is_logged_in()) {
            json_response(['success' => false, 'message' => 'Please log in first.'], 401);
        }
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM question_replies WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, current_user_id()]);
        json_response(['success' => true]);
        break;

    default:
        json_response(['success' => false, 'message' => 'Unknown action.'], 400);
}