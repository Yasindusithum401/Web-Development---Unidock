<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/json');

$q       = trim($_GET['q'] ?? '');
$subject = trim($_GET['subject'] ?? '');

if ($q === '' && ($subject === '' || $subject === 'all')) {
    echo json_encode(['success' => true, 'results' => [], 'message' => 'Type something or pick a subject to search.']);
    exit;
}

$sql    = 'SELECT id, title, subject, subject_code, description, file_path FROM resources WHERE 1=1';
$params = [];

if ($q !== '') {
    $sql .= ' AND (title LIKE ? OR subject LIKE ? OR subject_code LIKE ? OR description LIKE ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like, $like);
}
if ($subject !== '' && $subject !== 'all') {
    $sql .= ' AND subject = ?';
    $params[] = $subject;
}

$sql .= ' ORDER BY created_at DESC LIMIT 50';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();

$bookmarked = [];
if (is_logged_in()) {
    $b = $pdo->prepare('SELECT resource_id FROM bookmarks WHERE user_id = ?');
    $b->execute([current_user_id()]);
    $bookmarked = array_column($b->fetchAll(), 'resource_id');
}

foreach ($results as &$r) {
    $r['bookmarked'] = in_array($r['id'], $bookmarked);
}

echo json_encode(['success' => true, 'results' => $results]);