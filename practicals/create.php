<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') error('POST required', 405);

$db   = getDB();
$user = requireAuth($db);
$body = getBody();

$subjectId      = (int)    ($body['subject_id']      ?? 0);
$title          = trim($body['title']          ?? '');
$labNumber      = trim($body['lab_number']     ?? '');
$description    = trim($body['description']    ?? '') ?: null;
$submissionDate = trim($body['submission_date']?? '') ?: null;
$filePath       = trim($body['file_path']      ?? '') ?: null;
$referenceLink  = trim($body['reference_link'] ?? '') ?: null;

if ($subjectId <= 0) error('subject_id required');
if (empty($title))   error('title required');

$chk = $db->prepare('SELECT id FROM subjects WHERE id = ? AND user_id = ?');
$chk->execute([$subjectId, $user['id']]);
if (!$chk->fetch()) error('Subject not found', 404);

$stmt = $db->prepare(
    'INSERT INTO practicals (subject_id, user_id, title, lab_number, description, submission_date, file_path, reference_link)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
);
$stmt->execute([$subjectId, $user['id'], $title, $labNumber, $description, $submissionDate, $filePath, $referenceLink]);
$id = $db->lastInsertId();

success([
    'id'             => (int) $id,
    'subject_id'     => $subjectId,
    'title'          => $title,
    'lab_number'     => $labNumber,
    'description'    => $description,
    'submission_date'=> $submissionDate,
    'file_path'      => $filePath,
    'reference_link' => $referenceLink,
    'is_done'        => 0,
], 'Practical created');
