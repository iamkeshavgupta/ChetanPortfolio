<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/projects.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $project = get_project_by_id($id);
    if ($project) {
        update_project($id, ['published' => !$project['published']]);
    }
}

header('Location: projects.php');
exit;
