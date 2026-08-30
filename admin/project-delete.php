<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/projects.php';
require_once __DIR__ . '/../includes/upload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $project = get_project_by_id($id);
    if ($project) {
        foreach ($project['images'] as $img) {
            delete_uploaded_file($img['url']);
        }
        delete_uploaded_file($project['card_image']);
        if ($project['hero_image'] !== $project['card_image']) {
            delete_uploaded_file($project['hero_image']);
        }
        delete_project($id);
    }
}

header('Location: projects.php?deleted=1');
exit;
