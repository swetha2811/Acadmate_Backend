<?php
require_once __DIR__ . '/../config.php';

$db   = getDB();
$user = requireAuth($db);

$day        = $_GET['day']         ?? null; // optional ?day=Monday
$semesterId = (int) ($_GET['semester_id'] ?? 0); // optional semester filter

$sql = "
    SELECT cs.id AS slot_id, cs.day, cs.mode, cs.start_time, cs.end_time, cs.room,
           sub.id AS subject_id, sub.name AS subject_name, sub.code AS subject_code,
           sub.type AS subject_type, sub.credits, sub.min_attendance_pct AS min_att,
           sub.semester_id,
           COALESCE(att.present_count, 0) AS present_count
      FROM class_slots cs
      JOIN subjects sub ON sub.id = cs.subject_id
      LEFT JOIN (
          SELECT subject_id, SUM(status='present') AS present_count
            FROM attendance WHERE user_id = :uid GROUP BY subject_id
      ) att ON att.subject_id = cs.subject_id
     WHERE cs.user_id = :uid2
";

$params = [':uid' => $user['id'], ':uid2' => $user['id']];

if ($day) {
    $sql .= ' AND cs.day = :day';
    $params[':day'] = $day;
}

if ($semesterId > 0) {
    $sql .= ' AND sub.semester_id = :semId';
    $params[':semId'] = $semesterId;
}

$sql .= ' ORDER BY cs.start_time ASC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
success($stmt->fetchAll());
