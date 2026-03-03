<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') error('POST required', 405);

$db   = getDB();
$user = requireAuth($db);

$db->prepare('UPDATE users SET auth_token = NULL WHERE id = ?')->execute([$user['id']]);

success([], 'Logged out successfully');
