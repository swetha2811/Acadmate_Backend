<?php
require_once __DIR__ . '/../config.php';

$db   = getDB();
$user = requireAuth($db);

$semesterId = (int) ($_GET['semester_id'] ?? 0);
if ($semesterId <= 0) error('semester_id query param required');

// Expected classes per subject (new denominator)
$expectedMap = getExpectedClasses($db, $user['id']);

$stmt = $db->prepare("
    SELECT s.*,
        COALESCE(att.present_count, 0) AS present_count,
        COALESCE(att.total_marked,  0) AS total_marked
    FROM subjects s
    LEFT JOIN (
        SELECT subject_id,
               SUM(status = 'present') AS present_count,
               COUNT(*)                AS total_marked
          FROM attendance
         WHERE user_id = ?
         GROUP BY subject_id
    ) att ON att.subject_id = s.id
    WHERE s.semester_id = ? AND s.user_id = ?
    ORDER BY s.created_at ASC
");
$stmt->execute([$user['id'], $semesterId, $user['id']]);
$rows = $stmt->fetchAll();

$result = [];
foreach ($rows as $row) {
    $subId    = (int) $row['id'];
    $expected = $expectedMap[$subId] ?? 0;
    $present  = (int) $row['present_count'];
    $marked   = (int) $row['total_marked'];
    $pct      = ($expected > 0) ? round($present * 100.0 / $expected, 1) : 0.0;
    $missed   = max(0, $expected - $present);
    $row['total_classes']  = $expected;
    $row['expected_classes'] = $expected;
    $row['total_missed']   = $missed;
    $row['attendance_pct'] = $pct;
    $result[] = $row;
}

success($result);
