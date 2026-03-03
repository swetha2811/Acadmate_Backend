<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') error('POST required', 405);

$db   = getDB();
$user = requireAuth($db);
$body = getBody();

$subjectId = (int) ($body['subject_id'] ?? 0);
$title     = trim($body['title']        ?? '');
$tag       = $body['tag']               ?? 'Unit';
$content   = trim($body['content']      ?? '');
$filePath  = trim($body['file_path']    ?? '');

if ($subjectId <= 0) error('subject_id required');
if (empty($title))   error('title required');

$stmt = $db->prepare(
    'INSERT INTO notes (subject_id, user_id, title, tag, content, file_path) VALUES (?, ?, ?, ?, ?, ?)'
);
$stmt->execute([$subjectId, $user['id'], $title, $tag, $content, $filePath ?: null]);
$id = (int) $db->lastInsertId();

$createdAt = date('Y-m-d H:i:s');
success(['id' => $id, 'subject_id' => $subjectId, 'title' => $title,
         'tag' => $tag, 'content' => $content, 'file_path' => $filePath ?: null,
         'created_at' => $createdAt], 'Note created');
