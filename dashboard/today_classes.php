<?php
require_once __DIR__ . '/../config.php';
$db   = getDB();
$user = requireAuth($db);

$dayName    = date('l');
$today      = date('Y-m-d');
$semesterId = (int) ($_GET['semester_id'] ?? 0);
$deviceHHMM = preg_replace('/[^0-9:]/', '', $_GET['device_time'] ?? '');

$expectedMap = getExpectedClasses($db, $user['id'], $semesterId, $deviceHHMM);

$semFilter = $semesterId > 0 ? 'AND sub.semester_id = :semId' : '';

$stmt = $db->prepare("
    SELECT cs.id AS slot_id, cs.day, cs.mode, cs.start_time, cs.end_time, cs.room,
           sub.id AS subject_id, sub.name AS subject_name, sub.code AS subject_code,
           a.status AS marked_status,
           COALESCE(att.present_count, 0) AS present_count,
           COALESCE(att.total_marked,  0) AS total_marked
      FROM class_slots cs
      JOIN subjects sub ON sub.id = cs.subject_id
      LEFT JOIN attendance a
             ON a.class_slot_id = cs.id AND a.user_id = :uid1 AND a.date = :today
      LEFT JOIN (
          SELECT subject_id, SUM(status = 'present') AS present_count, COUNT(*) AS total_marked
            FROM attendance WHERE user_id = :uid2 GROUP BY subject_id
      ) att ON att.subject_id = cs.subject_id
     WHERE cs.user_id = :uid3 AND cs.day = :day $semFilter
     ORDER BY cs.start_time ASC
");

$params = [':uid1' => $user['id'], ':today' => $today, ':uid2' => $user['id'], ':uid3' => $user['id'], ':day' => $dayName];
if ($semesterId > 0) $params[':semId'] = $semesterId;
$stmt->execute($params);
$rows = $stmt->fetchAll();

$result = [];
foreach ($rows as $row) {
    $subId    = (int) $row['subject_id'];
    $expected = $expectedMap[$subId] ?? 0;
    $present  = (int) $row['present_count'];
    $marked   = (int) $row['total_marked'];
    // UI percentage: present out of classes the user has actually marked
    $pct      = ($marked > 0) ? round($present * 100.0 / $marked, 1) : 0.0;
    // class_state is NOT computed server-side — the Android client derives it
    // from start_time/end_time using device-local time, which is always correct
    // regardless of the server's timezone (server runs UTC, device may be IST etc.)
    $result[] = [
        'slot_id'          => (int) $row['slot_id'],
        'subject_id'       => $subId,
        'subject_name'     => $row['subject_name'],
        'subject_code'     => $row['subject_code'],
        'start_time'       => $row['start_time'],
        'end_time'         => $row['end_time'],
        'room'             => $row['room'] ?? '',
        'day'              => $row['day'],
        'marked_status'    => $row['marked_status'],
        'present_count'    => $present,
        'total_marked'     => (int) $row['total_marked'],
        'expected_classes' => $expected,
        'attendance_pct'   => $pct,
    ];
}
success($result);
