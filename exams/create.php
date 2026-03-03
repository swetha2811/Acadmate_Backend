<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') error('POST required', 405);

$db   = getDB();
$user = requireAuth($db);
$body = getBody();

$subjectId = (int) ($body['subject_id'] ?? 0);
$type      = $body['type']              ?? 'Mid';
$examDate  = $body['exam_date']         ?? null;
$examTime  = $body['exam_time']         ?? null;
$location  = trim($body['location']     ?? '');
$syllabus  = trim($body['syllabus']     ?? '');

if ($subjectId <= 0) error('subject_id required');

$stmt = $db->prepare(
    'INSERT INTO exams (subject_id, user_id, type, exam_date, exam_time, location, syllabus)
     VALUES (?, ?, ?, ?, ?, ?, ?)'
);
$stmt->execute([$subjectId, $user['id'], $type, $examDate ?: null, $examTime ?: null, $location, $syllabus]);
$id = (int) $db->lastInsertId();

success(['id' => $id, 'subject_id' => $subjectId, 'type' => $type,
         'exam_date' => $examDate, 'exam_time' => $examTime,
         'location' => $location, 'syllabus' => $syllabus], 'Exam created');
