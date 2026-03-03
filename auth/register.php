<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') error('POST required', 405);

$body = getBody();
$fullName = trim($body['full_name'] ?? '');
$email    = trim($body['email']     ?? '');
$course   = trim($body['course']    ?? '');
$password = $body['password']       ?? '';

if (empty($fullName)) error('Full name is required');
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) error('Valid email is required');
if (empty($course)) error('Course is required');
if (strlen($password) < 6) error('Password must be at least 6 characters');

$db = getDB();

// Check duplicate email
$stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) error('An account with this email already exists');

$hash  = password_hash($password, PASSWORD_BCRYPT);
$token = bin2hex(random_bytes(32));

$stmt = $db->prepare(
    'INSERT INTO users (full_name, email, course, password_hash, auth_token) VALUES (?, ?, ?, ?, ?)'
);
$stmt->execute([$fullName, $email, $course, $hash, $token]);
$userId = (int) $db->lastInsertId();

success([
    'user_id'    => $userId,
    'full_name'  => $fullName,
    'email'      => $email,
    'course'     => $course,
    'auth_token' => $token
], 'Account created successfully');
