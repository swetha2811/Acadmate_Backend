<?php
require_once __DIR__ . '/../config.php';

$db   = getDB();
$user = requireAuth($db);

$subjectId = (int) ($_GET['subject_id'] ?? 0);
if ($subjectId <= 0) error('subject_id query param required');

$stmt = $db->prepare(
    'SELECT id, subject_id, title, due_date, priority, is_done, description, file_path, created_at
       FROM assignments
      WHERE subject_id = ? AND user_id = ?
      ORDER BY due_date ASC, created_at ASC'
);
$stmt->execute([$subjectId, $user['id']]);
success($stmt->fetchAll());
