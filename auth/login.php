<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') error('POST required', 405);

$body     = getBody();
$email    = trim($body['email']    ?? '');
$password = $body['password']      ?? '';

if (empty($email) || empty($password)) error('Email and password are required');

$db   = getDB();
$stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    error('Invalid email or password', 401);
}

// Refresh auth token on every login
$token = bin2hex(random_bytes(32));
$db->prepare('UPDATE users SET auth_token = ? WHERE id = ?')->execute([$token, $user['id']]);

success([
    'user_id'    => (int) $user['id'],
    'full_name'  => $user['full_name'],
    'email'      => $user['email'],
    'course'     => $user['course'],
    'auth_token' => $token
], 'Login successful');
