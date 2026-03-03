<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') error('POST required', 405);

$db   = getDB();
$user = requireAuth($db);
$body = getBody();

$subjectId   = (int) ($body['subject_id']    ?? 0);
$classSlotId = (int) ($body['class_slot_id'] ?? 0);
$date        = trim($body['date']            ?? '');
$status      = trim($body['status']          ?? 'present');  // 'present' | 'absent'

if ($subjectId <= 0 || $classSlotId <= 0) error('subject_id and class_slot_id required');
if (empty($date))  error('date required');

// INSERT or UPDATE using UNIQUE KEY (subject_id, class_slot_id, date)
$stmt = $db->prepare(
    'INSERT INTO attendance (subject_id, user_id, class_slot_id, date, status)
     VALUES (?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE status = VALUES(status)'
);
$stmt->execute([$subjectId, $user['id'], $classSlotId, $date, $status]);

// Return updated summary for this subject
$deviceHHMM  = preg_replace('/[^0-9:]/', '', $body['device_time'] ?? '');
$expectedMap = getExpectedClasses($db, $user['id'], 0, $deviceHHMM);
$expected    = $expectedMap[$subjectId] ?? 0;

$sum = $db->prepare(
    "SELECT SUM(status='present') AS present_count,
            COUNT(*)               AS total_marked
     FROM attendance WHERE subject_id = ? AND user_id = ?"
);
$sum->execute([$subjectId, $user['id']]);
$summary = $sum->fetch();

$present = (int) $summary['present_count'];
$marked  = (int) $summary['total_marked'];
// UI percentage: present ÷ marked (only what the student has recorded)
$missed  = max(0, $expected - $present);
$pct     = ($marked > 0) ? round($present * 100.0 / $marked, 1) : 0.0;

success([
    'subject_id'       => $subjectId,
    'date'             => $date,
    'status'           => $status,
    'present_count'    => $present,
    'total_marked'     => $marked,
    'total_missed'     => $missed,
    'expected_classes' => $expected,
    'attendance_pct'   => $pct,
], 'Attendance marked');
