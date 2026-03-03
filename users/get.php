<?php
require_once __DIR__ . '/../config.php';

$db   = getDB();
$user = requireAuth($db);

$stmt = $db->prepare('SELECT id, full_name, email, course FROM users WHERE id = ?');
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();

success($profile);
