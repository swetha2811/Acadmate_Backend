<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') error('POST required', 405);

$db   = getDB();
$user = requireAuth($db);
$body = getBody();

$name      = trim($body['name']       ?? '');
$startDate = trim($body['start_date'] ?? '');
$endDate   = trim($body['end_date']   ?? '');

if (empty($name))      error('Semester name is required');
if (empty($startDate)) error('Start date is required');
if (empty($endDate))   error('End date is required');

$stmt = $db->prepare(
    'INSERT INTO semesters (user_id, name, start_date, end_date) VALUES (?, ?, ?, ?)'
);
$stmt->execute([$user['id'], $name, $startDate, $endDate]);
$id = (int) $db->lastInsertId();

success([
    'id'         => $id,
    'user_id'    => $user['id'],
    'name'       => $name,
    'start_date' => $startDate,
    'end_date'   => $endDate
], 'Semester created');
