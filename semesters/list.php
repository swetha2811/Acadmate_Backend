<?php
require_once __DIR__ . '/../config.php';

$db   = getDB();
$user = requireAuth($db);

$stmt = $db->prepare(
    'SELECT * FROM semesters WHERE user_id = ? ORDER BY created_at DESC'
);
$stmt->execute([$user['id']]);
$rows = $stmt->fetchAll();

success($rows);
