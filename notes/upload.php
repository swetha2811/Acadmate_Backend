<?php
require_once __DIR__ . '/../config.php';

$db   = getDB();
$user = requireAuth($db);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') error('POST required', 405);
if (empty($_FILES['file'])) error('No file uploaded');

$file     = $_FILES['file'];
$userId   = $user['id'];
$uploadDir = __DIR__ . '/../uploads/notes/' . $userId . '/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$originalName = basename($file['name']);
$safeName     = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
$timestamp    = time();
$targetPath   = $uploadDir . $timestamp . '_' . $safeName;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) error('Failed to save file', 500);

$relativePath = 'uploads/notes/' . $userId . '/' . $timestamp . '_' . $safeName;
// Return file_path at top level for Android FileUploadResponse
echo json_encode(['success' => true, 'message' => 'File uploaded', 'file_path' => $relativePath]);
