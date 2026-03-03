<?php
require_once __DIR__ . '/../config.php';

$db   = getDB();
$user = requireAuth($db);

$subjectId = (int) ($_GET['subject_id'] ?? 0);
if ($subjectId <= 0) error('subject_id query param required');

$stmt = $db->prepare("
    SELECT s.*,
        COALESCE(att.present_count, 0) AS present_count,
        COALESCE(att.total_marked, 0)  AS total_marked,
        CASE WHEN COALESCE(att.total_marked, 0) > 0
            THEN ROUND(COALESCE(att.present_count, 0) * 100.0 / att.total_marked, 1)
            ELSE 0
        END AS attendance_pct
    FROM subjects s
    LEFT JOIN (
        SELECT subject_id,
               SUM(status = 'present') AS present_count,
               COUNT(*)                AS total_marked
          FROM attendance WHERE user_id = ?
         GROUP BY subject_id
    ) att ON att.subject_id = s.id
    WHERE s.id = ? AND s.user_id = ?
");
$stmt->execute([$user['id'], $subjectId, $user['id']]);
$row = $stmt->fetch();
if (!$row) error('Subject not found', 404);

success($row);
