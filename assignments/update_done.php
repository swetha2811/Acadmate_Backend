<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') error('POST required', 405);

$db   = getDB();
$user = requireAuth($db);
$body = getBody();

$id     = (int) ($body['id']      ?? 0);
$isDone = (int) ($body['is_done'] ?? 0);
if ($id <= 0) error('id required');

$stmt = $db->prepare('UPDATE assignments SET is_done = ? WHERE id = ? AND user_id = ?');
$stmt->execute([$isDone, $id, $user['id']]);
success(['id' => $id, 'is_done' => $isDone]);
