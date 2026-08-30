<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/projects.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: projects.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$direction = $_POST['direction'] ?? '';

$projects = list_projects(false);
$ids = array_column($projects, 'id');
$index = array_search($id, $ids, true);

if ($index !== false) {
    $target = $direction === 'up' ? $index - 1 : $index + 1;
    if ($target >= 0 && $target < count($ids)) {
        [$ids[$index], $ids[$target]] = [$ids[$target], $ids[$index]];
        reorder_projects($ids);
    }
}

header('Location: projects.php');
exit;
