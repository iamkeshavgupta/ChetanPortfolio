<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/projects.php';
require_once __DIR__ . '/../includes/upload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$projectId = (int) ($_POST['project_id'] ?? 0);
$section = (string) ($_POST['section'] ?? 'gallery');
$mediaType = (string) ($_POST['media_type'] ?? 'image');

if (!get_project_by_id($projectId)) {
    header('Location: index.php');
    exit;
}

try {
    if ($mediaType === 'video_embed') {
        $embedUrl = trim((string) ($_POST['embed_url'] ?? ''));
        if ($embedUrl === '') {
            throw new RuntimeException('Paste a YouTube or Vimeo URL.');
        }
        if (normalize_video_embed_url($embedUrl) === null) {
            throw new RuntimeException('That doesn\'t look like a YouTube or Vimeo URL.');
        }
        add_project_image($projectId, $embedUrl, '', $section ?: 'gallery', 'video_embed');
    } elseif ($mediaType === 'video_file') {
        if (!isset($_FILES['file'])) {
            throw new RuntimeException('No file was uploaded.');
        }
        $url = save_uploaded_video($_FILES['file']);
        add_project_image($projectId, $url, '', $section ?: 'gallery', 'video_file');
    } else {
        if (!isset($_FILES['file'])) {
            throw new RuntimeException('No file was uploaded.');
        }
        $url = save_uploaded_image($_FILES['file']);
        add_project_image($projectId, $url, '', $section ?: 'gallery', 'image');
    }
} catch (RuntimeException $e) {
    header('Location: project-edit.php?id=' . $projectId . '&img_error=' . urlencode($e->getMessage()));
    exit;
}

header('Location: project-edit.php?id=' . $projectId);
exit;
