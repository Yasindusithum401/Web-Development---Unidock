<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    json_response(['success' => false, 'message' => 'Please log in to manage bookmarks.'], 401);
}

$userId = current_user_id();
$action = $_REQUEST['action'] ?? 'toggle';

switch ($action) {
    case 'toggle':
        $resourceId = (int)($_POST['resource_id'] ?? 0);
        if ($resourceId <= 0) {
            json_response(['success' => false, 'message' => 'Invalid Resource ID.'], 422);
        }
        $check = $pdo->prepare("SELECT id FROM bookmarks WHERE user_id = ? AND resource_id = ?");
        $check->execute([$userId, $resourceId]);
        $exists = $check->fetch();

        if ($exists) {
            $delete = $pdo->prepare("DELETE FROM bookmarks WHERE user_id = ? AND resource_id = ?");
            $delete->execute([$userId, $resourceId]);
            json_response(['success' => true, 'bookmarked' => false, 'message' => 'Bookmark removed.']);
        } else {
            $insert = $pdo->prepare("INSERT INTO bookmarks (user_id, resource_id) VALUES (?, ?)");
            $insert->execute([$userId, $resourceId]);
            json_response(['success' => true, 'bookmarked' => true, 'message' => 'Resource bookmarked.']);
        }
        break;

    case 'list':
        $sql = "SELECT r.id, r.title, r.subject, r.subject_code, r.description, r.file_path 
                FROM bookmarks b 
                JOIN resources r ON b.resource_id = r.id 
                WHERE b.user_id = ? 
                ORDER BY b.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $bookmarks = $stmt->fetchAll();

        json_response(['success' => true, 'bookmarks' => $bookmarks]);
        break;

    default:
        json_response(['success' => false, 'message' => 'Invalid action.'], 400);
}