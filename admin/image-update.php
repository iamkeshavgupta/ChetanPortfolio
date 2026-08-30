<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/projects.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    update_project_image($id, [
        'caption' => (string) ($_POST['caption'] ?? ''),
        'section' => (string) ($_POST['section'] ?? 'gallery'),
    ]);
}

$projectId = (int) ($_POST['project_id'] ?? 0);
header('Location: project-edit.php?id=' . $projectId);
exit;
