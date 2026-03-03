<?php
require_once __DIR__ . '/../config.php';

$db   = getDB();
$user = requireAuth($db);

// Optional semester filter
$semesterId = (int) ($_GET['semester_id'] ?? 0);

// Sanitise and forward device-provided current time (avoids server-UTC vs device-IST mismatch)
$deviceHHMM = preg_replace('/[^0-9:]/', '', $_GET['device_time'] ?? '');

// Expected classes per subject (classes held so far, real-time denominator)
$expectedMap = getExpectedClasses($db, $user['id'], $semesterId, $deviceHHMM);

$semFilter = $semesterId > 0 ? 'AND s.semester_id = :semId' : '';

$sql = "
    SELECT s.id AS subject_id, s.name AS subject_name, s.code AS subject_code,
           s.min_attendance_pct,
           COALESCE(att.present_count, 0) AS present_count,
           COALESCE(att.total_marked,  0) AS total_marked
      FROM subjects s
      LEFT JOIN (
          SELECT subject_id, SUM(status='present') AS present_count, COUNT(*) AS total_marked
            FROM attendance WHERE user_id = :uid GROUP BY subject_id
      ) att ON att.subject_id = s.id
     WHERE s.user_id = :uid2 $semFilter
     ORDER BY s.name
";

$params = [':uid' => $user['id'], ':uid2' => $user['id']];
if ($semesterId > 0) $params[':semId'] = $semesterId;

$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$result = [];
foreach ($rows as $row) {
    $subId    = (int) $row['subject_id'];
    $expected = $expectedMap[$subId] ?? 0;
    $present  = (int) $row['present_count'];
    $marked   = (int) $row['total_marked'];
    // UI percentage: present out of classes the user has actually marked (present or absent)
    $pct      = ($marked > 0) ? round($present * 100.0 / $marked, 1) : 0.0;
    $missed   = max(0, $marked - $present);

    $result[] = [
        'subject_id'         => $subId,
        'subject_name'       => $row['subject_name'],
        'subject_code'       => $row['subject_code'] ?? '',
        'min_attendance_pct' => (int) $row['min_attendance_pct'],
        'present_count'      => $present,
        'total_classes'      => $expected,
        'total_marked'       => $marked,
        'total_missed'       => $missed,
        'attendance_pct'     => $pct,
    ];
}

success($result);

