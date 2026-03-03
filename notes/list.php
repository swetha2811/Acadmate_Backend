<?php
require_once __DIR__ . '/../config.php';

$db   = getDB();
$user = requireAuth($db);
$subjectId = (int) ($_GET['subject_id'] ?? 0);
if ($subjectId <= 0) error('subject_id required');

$stmt = $db->prepare(
    'SELECT * FROM notes WHERE subject_id = ? AND user_id = ? ORDER BY created_at DESC'
);
$stmt->execute([$subjectId, $user['id']]);
success($stmt->fetchAll());
