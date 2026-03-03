<?php
require_once __DIR__ . '/../config.php';
$db   = getDB();
$user = requireAuth($db);

$semesterId  = (int) ($_GET['semester_id'] ?? 0);
$deviceHHMM  = preg_replace('/[^0-9:]/', '', $_GET['device_time'] ?? '');
$expectedMap = getExpectedClasses($db, $user['id'], $semesterId, $deviceHHMM);
$semFilter   = $semesterId > 0 ? 'AND s.semester_id = :semId' : '';

$stmt = $db->prepare("
    SELECT s.id AS subject_id, s.name AS subject_name, s.min_attendance_pct,
           COALESCE(att.present_count, 0) AS present_count,
           COALESCE(att.total_marked,  0) AS total_marked
      FROM subjects s
      LEFT JOIN (
          SELECT subject_id, SUM(status='present') AS present_count, COUNT(*) AS total_marked
            FROM attendance WHERE user_id = :uid GROUP BY subject_id
      ) att ON att.subject_id = s.id
     WHERE s.user_id = :uid2 $semFilter
     ORDER BY s.name
");
$params = [':uid' => $user['id'], ':uid2' => $user['id']];
if ($semesterId > 0) $params[':semId'] = $semesterId;
$stmt->execute($params);
$rows = $stmt->fetchAll();

$result = [];
foreach ($rows as $row) {
    $subId    = (int) $row['subject_id'];
    $expected = $expectedMap[$subId] ?? 0;
    $present  = (int) $row['present_count'];
    $marked   = (int) $row['total_marked'];
    $pct      = ($expected > 0) ? round($present * 100.0 / $expected, 1) : 0.0;
    $min      = (int) $row['min_attendance_pct'];
    if ($marked > 0 && $pct < $min) {
        $needed = 0;
        for ($i = 1; $i <= 300; $i++) {
            if ($expected > 0 && (($present + $i) * 100.0 / ($expected + $i)) >= $min) {
                $needed = $i; break;
            }
        }
        $result[] = [
            'subject_id'     => $subId,
            'subject_name'   => $row['subject_name'],
            'attendance_pct' => $pct,
            'classes_needed' => $needed,
        ];
    }
}
success($result);
