<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') error('POST required', 405);

$db   = getDB();
$user = requireAuth($db);
$body = getBody();

$semesterId       = (int)   ($body['semester_id']        ?? 0);
$name             = trim($body['name']                   ?? '');
$code             = trim($body['code']                   ?? '');
$credits          = (int)   ($body['credits']            ?? 3);
$type             = $body['type']                        ?? 'Theory';
$classesPerWeek   = (int)   ($body['classes_per_week']   ?? 3);
$minAttendancePct = (int)   ($body['min_attendance_pct'] ?? 75);
$totalClasses     = (int)   ($body['total_classes']      ?? 72);

if ($semesterId <= 0) error('semester_id is required');
if (empty($name))     error('Subject name is required');

// Verify semester belongs to this user
$s = $db->prepare('SELECT id FROM semesters WHERE id = ? AND user_id = ?');
$s->execute([$semesterId, $user['id']]);
if (!$s->fetch()) error('Semester not found', 404);

$stmt = $db->prepare('INSERT INTO subjects
    (semester_id, user_id, name, code, credits, type, classes_per_week, min_attendance_pct, total_classes)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([
    $semesterId, $user['id'], $name, $code,
    $credits, $type, $classesPerWeek, $minAttendancePct, $totalClasses
]);
$id = (int) $db->lastInsertId();

success([
    'id'                  => $id,
    'semester_id'         => $semesterId,
    'user_id'             => $user['id'],
    'name'                => $name,
    'code'                => $code,
    'credits'             => $credits,
    'type'                => $type,
    'classes_per_week'    => $classesPerWeek,
    'min_attendance_pct'  => $minAttendancePct,
    'total_classes'       => $totalClasses
], 'Subject created');
