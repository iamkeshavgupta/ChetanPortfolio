<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/projects.php';
require_once __DIR__ . '/../includes/upload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $image = get_project_image($id);
    if ($image) {
        delete_uploaded_file($image['url']);
        delete_project_image($id);
    }
}

$projectId = (int) ($_POST['project_id'] ?? 0);
header('Location: project-edit.php?id=' . $projectId);
exit;
