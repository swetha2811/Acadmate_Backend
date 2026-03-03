<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') error('POST required', 405);

$db   = getDB();
$user = requireAuth($db);

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    error('No file uploaded or upload error');
}

$uploadDir = __DIR__ . '/../uploads/assignments/' . $user['id'] . '/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$originalName = basename($_FILES['file']['name']);
$safeName     = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
$uniqueName   = time() . '_' . $safeName;
$destPath     = $uploadDir . $uniqueName;

if (!move_uploaded_file($_FILES['file']['tmp_name'], $destPath)) {
    error('Failed to save file');
}

$relativePath = 'uploads/assignments/' . $user['id'] . '/' . $uniqueName;
success(['file_path' => $relativePath], 'File uploaded');
