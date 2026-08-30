<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/projects.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $projectId = (int) ($_POST['project_id'] ?? 0);
    $direction = $_POST['direction'] ?? '';

    $project = get_project_by_id($projectId);
    if ($project) {
        $images = $project['images'];
        $ids = array_column($images, 'id');
        $index = array_search($id, $ids, true);
        if ($index !== false) {
            $target = $direction === 'up' ? $index - 1 : $index + 1;
            if ($target >= 0 && $target < count($ids)) {
                [$ids[$index], $ids[$target]] = [$ids[$target], $ids[$index]];
                foreach ($ids as $i => $imgId) {
                    update_project_image($imgId, ['sort_order' => $i]);
                }
            }
        }
    }
}

$projectId = (int) ($_POST['project_id'] ?? 0);
header('Location: project-edit.php?id=' . $projectId);
exit;
