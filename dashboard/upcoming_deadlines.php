<?php
require_once __DIR__ . '/../config.php';
$db   = getDB();
$user = requireAuth($db);

$today      = date('Y-m-d');
$oneWeek    = date('Y-m-d', strtotime('+7 days'));
$semesterId = (int) ($_GET['semester_id'] ?? 0);

$semJoin   = '';
$semWhere  = '';
$semParams = [];
if ($semesterId > 0) {
    $semJoin  = 'JOIN semesters sem ON sem.id = s.semester_id';
    $semWhere = 'AND s.semester_id = ?';
    $semParams = [$semesterId];
}

// Assignments
$stmtA = $db->prepare("
    SELECT a.id, 'assignment' AS type, a.title, a.due_date,
           a.priority, s.name AS subject_name, s.id AS subject_id
      FROM assignments a
      JOIN subjects s ON s.id = a.subject_id $semJoin
     WHERE a.user_id = ? AND a.due_date BETWEEN ? AND ? AND a.is_done = 0 $semWhere
     ORDER BY a.due_date ASC
");
$stmtA->execute(array_merge([$user['id'], $today, $oneWeek], $semParams));
$assignments = $stmtA->fetchAll();

// Practicals
$stmtP = $db->prepare("
    SELECT p.id, 'practical' AS type, p.title, p.submission_date AS due_date,
           'Medium' AS priority, s.name AS subject_name, s.id AS subject_id
      FROM practicals p
      JOIN subjects s ON s.id = p.subject_id $semJoin
     WHERE p.user_id = ? AND p.submission_date BETWEEN ? AND ? AND p.is_done = 0 $semWhere
     ORDER BY p.submission_date ASC
");
$stmtP->execute(array_merge([$user['id'], $today, $oneWeek], $semParams));
$practicals = $stmtP->fetchAll();

// Exams
$stmtE = $db->prepare("
    SELECT e.id, 'exam' AS type, CONCAT(e.type, ' Exam') AS title, e.exam_date AS due_date,
           'High' AS priority, s.name AS subject_name, s.id AS subject_id
      FROM exams e
      JOIN subjects s ON s.id = e.subject_id $semJoin
     WHERE e.user_id = ? AND e.exam_date BETWEEN ? AND ? $semWhere
     ORDER BY e.exam_date ASC
");
$stmtE->execute(array_merge([$user['id'], $today, $oneWeek], $semParams));
$exams = $stmtE->fetchAll();

$all      = array_merge($assignments, $practicals, $exams);
$tomorrow = date('Y-m-d', strtotime('+1 day'));
usort($all, fn($a, $b) => strcmp($a['due_date'], $b['due_date']));
foreach ($all as &$dl) {
    $dl['is_tomorrow'] = ($dl['due_date'] === $tomorrow) ? 1 : 0;
}
unset($dl);

success($all);
