<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') error('POST required', 405);

$db   = getDB();
$user = requireAuth($db);
$body = getBody();

$subjectId = (int)   ($body['subject_id'] ?? 0);
$day       = trim($body['day']            ?? '');
$mode      = $body['mode']                ?? 'Theory';
$startTime = trim($body['start_time']     ?? '');
$endTime   = trim($body['end_time']       ?? '');
$room      = trim($body['room']           ?? 'TBD');

if ($subjectId <= 0) error('subject_id required');
if (empty($day))     error('day required');
if (empty($startTime) || empty($endTime)) error('start_time and end_time required');

// Verify subject belongs to user
$s = $db->prepare('SELECT id FROM subjects WHERE id = ? AND user_id = ?');
$s->execute([$subjectId, $user['id']]);
if (!$s->fetch()) error('Subject not found', 404);

$stmt = $db->prepare(
    'INSERT INTO class_slots (subject_id, user_id, day, mode, start_time, end_time, room)
     VALUES (?, ?, ?, ?, ?, ?, ?)'
);
$stmt->execute([$subjectId, $user['id'], $day, $mode, $startTime, $endTime, $room]);
$id = (int) $db->lastInsertId();

// Return with subject name for convenience
$sub = $db->prepare('SELECT name, code, type, credits, min_attendance_pct, total_classes FROM subjects WHERE id = ?');
$sub->execute([$subjectId]);
$subject = $sub->fetch();

success([
    'id'              => $id,
    'subject_id'      => $subjectId,
    'subject_name'    => $subject['name'],
    'subject_code'    => $subject['code'],
    'subject_type'    => $subject['type'],
    'credits'         => $subject['credits'],
    'min_att'         => $subject['min_attendance_pct'],
    'total_classes'   => $subject['total_classes'],
    'day'             => $day,
    'mode'            => $mode,
    'start_time'      => $startTime,
    'end_time'        => $endTime,
    'room'            => $room
], 'Class slot created');
