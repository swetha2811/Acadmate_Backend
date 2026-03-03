<?php
require_once __DIR__ . '/../config.php';

$db   = getDB();
$user = requireAuth($db);

$subjectId = (int) ($_GET['subject_id'] ?? 0);
if ($subjectId <= 0) error('subject_id query param required');

$stmt = $db->prepare(
    'SELECT id, subject_id, title, lab_number, description, submission_date, is_done, file_path, reference_link, created_at
       FROM practicals
      WHERE subject_id = ? AND user_id = ?
      ORDER BY created_at ASC'
);
$stmt->execute([$subjectId, $user['id']]);
success($stmt->fetchAll());
