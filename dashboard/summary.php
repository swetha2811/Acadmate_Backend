<?php
require_once __DIR__ . '/../config.php';
$db   = getDB();
$user = requireAuth($db);

$today      = date('Y-m-d');
$oneWeek    = date('Y-m-d', strtotime('+7 days'));
$dayName    = date('l');
$semesterId = (int) ($_GET['semester_id'] ?? 0);
$tomorrow   = date('Y-m-d', strtotime('+1 day'));
$deviceHHMM = preg_replace('/[^0-9:]/', '', $_GET['device_time'] ?? '');

$expectedMap = getExpectedClasses($db, $user['id'], $semesterId, $deviceHHMM);

$semFilter  = $semesterId > 0 ? 'AND sub.semester_id = :semId'  : '';
$semFilterS = $semesterId > 0 ? 'AND s.semester_id = :semId2'   : '';

// ── Today's classes ────────────────────────────────
$s = $db->prepare("
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
$sparams = [':uid1' => $user['id'], ':today' => $today, ':uid2' => $user['id'], ':uid3' => $user['id'], ':day' => $dayName];
if ($semesterId > 0) $sparams[':semId'] = $semesterId;
$s->execute($sparams);
$rawClasses = $s->fetchAll();

$classes = [];
foreach ($rawClasses as $row) {
    $subId    = (int) $row['subject_id'];
    $expected = $expectedMap[$subId] ?? 0;
    $present  = (int) $row['present_count'];
    $marked   = (int) $row['total_marked'];
    // UI percentage: present ÷ marked (only classes the user has recorded)
    $pct      = ($marked > 0) ? round($present * 100.0 / $marked, 1) : 0.0;
    // class_state is NOT computed server-side — the Android client derives it
    // from start_time/end_time using device-local time, which is always correct
    // regardless of the server's timezone (server runs UTC, device may be IST etc.)
    $classes[] = [
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

// ── Deadlines (next 7 days, filtered by semester) ──
$semJoin   = $semesterId > 0 ? '' : '';
$semWhere  = $semesterId > 0 ? 'AND s.semester_id = ?' : '';
$semParams = $semesterId > 0 ? [$semesterId] : [];

$stmtA = $db->prepare("
    SELECT 'assignment' AS type, a.id, a.title, a.due_date, a.priority, s.name AS subject_name, s.id AS subject_id
      FROM assignments a JOIN subjects s ON s.id=a.subject_id
     WHERE a.user_id=? AND a.due_date BETWEEN ? AND ? AND a.is_done=0 $semWhere
");
$stmtA->execute(array_merge([$user['id'], $today, $oneWeek], $semParams));
$deadlines = $stmtA->fetchAll();

$stmtP = $db->prepare("
    SELECT 'practical' AS type, p.id, p.title, p.submission_date AS due_date, 'Medium' AS priority, s.name AS subject_name, s.id AS subject_id
      FROM practicals p JOIN subjects s ON s.id=p.subject_id
     WHERE p.user_id=? AND p.submission_date BETWEEN ? AND ? AND p.is_done=0 $semWhere
");
$stmtP->execute(array_merge([$user['id'], $today, $oneWeek], $semParams));
$deadlines = array_merge($deadlines, $stmtP->fetchAll());

$stmtE = $db->prepare("
    SELECT 'exam' AS type, e.id, CONCAT(e.type,' Exam') AS title, e.exam_date AS due_date, 'High' AS priority, s.name AS subject_name, s.id AS subject_id
      FROM exams e JOIN subjects s ON s.id=e.subject_id
     WHERE e.user_id=? AND e.exam_date BETWEEN ? AND ? $semWhere
");
$stmtE->execute(array_merge([$user['id'], $today, $oneWeek], $semParams));
$deadlines = array_merge($deadlines, $stmtE->fetchAll());
usort($deadlines, fn($a,$b) => strcmp($a['due_date'], $b['due_date']));
foreach ($deadlines as &$dl) { $dl['is_tomorrow'] = ($dl['due_date'] === $tomorrow) ? 1 : 0; }
unset($dl);

// ── Subjects & Attendance alerts ──
$stmtSub = $db->prepare("
    SELECT s.id AS subject_id, s.name AS subject_name, s.code AS subject_code,
           s.min_attendance_pct,
           COALESCE(att.present_count, 0) AS present_count,
           COALESCE(att.total_marked,  0) AS total_marked
      FROM subjects s
      LEFT JOIN (
          SELECT subject_id, SUM(status='present') AS present_count, COUNT(*) AS total_marked
            FROM attendance WHERE user_id = :uid GROUP BY subject_id
      ) att ON att.subject_id = s.id
     WHERE s.user_id = :uid2 $semFilterS
     ORDER BY s.name
");
$sparams2 = [':uid' => $user['id'], ':uid2' => $user['id']];
if ($semesterId > 0) $sparams2[':semId2'] = $semesterId;
$stmtSub->execute($sparams2);
$rawSubs = $stmtSub->fetchAll();

$subjects = [];
$alerts   = [];
foreach ($rawSubs as $sub) {
    $subId    = (int) $sub['subject_id'];
    $expected = $expectedMap[$subId] ?? 0;
    $present  = (int) $sub['present_count'];
    $marked   = (int) $sub['total_marked'];
    // UI percentage: present ÷ marked (only classes the user has recorded)
    $uiPct    = ($marked > 0) ? round($present * 100.0 / $marked, 1) : 0.0;
    // Alert percentage: present ÷ expected-so-far (catches non-marked skips)
    $alertPct = ($expected > 0) ? round($present * 100.0 / $expected, 1) : 0.0;
    $missed   = max(0, $expected - $present);
    $min      = (int) $sub['min_attendance_pct'];
    $subjects[] = [
        'subject_id'         => $subId,
        'subject_name'       => $sub['subject_name'],
        'subject_code'       => $sub['subject_code'] ?? '',
        'min_attendance_pct' => $min,
        'present_count'      => $present,
        'total_classes'      => $expected,
        'total_marked'       => $marked,
        'total_missed'       => $missed,
        'attendance_pct'     => $uiPct,
    ];
    if ($marked > 0 && $alertPct < $min) {
        $needed = 0;
        for ($i = 1; $i <= 300; $i++) {
            if ($expected > 0 && (($present + $i) * 100.0 / ($expected + $i)) >= $min) { $needed = $i; break; }
        }
        $alerts[] = ['subject_id' => $subId, 'subject_name' => $sub['subject_name'], 'attendance_pct' => $alertPct, 'classes_needed' => $needed];
    }
}

success(['today_classes' => $classes, 'upcoming_deadlines' => $deadlines, 'attendance_alerts' => $alerts, 'subjects' => $subjects]);
