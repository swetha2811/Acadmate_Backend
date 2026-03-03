<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') error('POST required', 405);

$db   = getDB();
$user = requireAuth($db);
$body = getBody();

$fullName = trim($body['full_name'] ?? '');
$email    = trim($body['email']    ?? '');
$course   = trim($body['course']   ?? '');
$password = trim($body['password'] ?? '');

if (empty($fullName)) error('full_name required');
if (empty($email))    error('email required');

// Check email not taken by another user
$chk = $db->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
$chk->execute([$email, $user['id']]);
if ($chk->fetch()) error('Email already in use by another account');

if (!empty($password)) {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $db->prepare('UPDATE users SET full_name=?, email=?, course=?, password_hash=? WHERE id=?');
    $stmt->execute([$fullName, $email, $course, $hash, $user['id']]);
} else {
    $stmt = $db->prepare('UPDATE users SET full_name=?, email=?, course=? WHERE id=?');
    $stmt->execute([$fullName, $email, $course, $user['id']]);
}

success([
    'id'        => $user['id'],
    'full_name' => $fullName,
    'email'     => $email,
    'course'    => $course,
], 'Profile updated');
