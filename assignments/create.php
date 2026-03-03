<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') error('POST required', 405);

$db   = getDB();
$user = requireAuth($db);
$body = getBody();

$subjectId   = (int)    ($body['subject_id'] ?? 0);
$title       = trim($body['title']       ?? '');
$dueDate     = trim($body['due_date']    ?? '') ?: null;
$priority    = trim($body['priority']    ?? 'Medium');
$description = trim($body['description'] ?? '') ?: null;
$filePath    = trim($body['file_path']   ?? '') ?: null;

if ($subjectId <= 0) error('subject_id required');
if (empty($title))   error('title required');

$chk = $db->prepare('SELECT id FROM subjects WHERE id = ? AND user_id = ?');
$chk->execute([$subjectId, $user['id']]);
if (!$chk->fetch()) error('Subject not found', 404);

$stmt = $db->prepare(
    'INSERT INTO assignments (subject_id, user_id, title, due_date, priority, description, file_path)
     VALUES (?, ?, ?, ?, ?, ?, ?)'
);
$stmt->execute([$subjectId, $user['id'], $title, $dueDate, $priority, $description, $filePath]);
$id = $db->lastInsertId();

success([
    'id'          => (int) $id,
    'subject_id'  => $subjectId,
    'title'       => $title,
    'due_date'    => $dueDate,
    'priority'    => $priority,
    'description' => $description,
    'file_path'   => $filePath,
    'is_done'     => 0,
], 'Assignment created');
