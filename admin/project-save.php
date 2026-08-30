<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/projects.php';
require_once __DIR__ . '/../includes/upload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
$title = trim((string) ($_POST['title'] ?? ''));
$slugInput = trim((string) ($_POST['slug'] ?? ''));
$category = (string) ($_POST['category'] ?? 'other');
if (!array_key_exists($category, PROJECT_CATEGORIES)) {
    $category = 'other';
}

$credits = [];
$roles = $_POST['credit_role'] ?? [];
$names = $_POST['credit_names'] ?? [];
foreach ($roles as $i => $role) {
    $role = trim((string) $role);
    $name = trim((string) ($names[$i] ?? ''));
    if ($role !== '' || $name !== '') {
        $credits[] = ['role' => $role, 'names' => $name];
    }
}

$errors = [];
if ($title === '') {
    $errors[] = 'Title is required.';
}

$slug = $slugInput !== '' ? slugify($slugInput) : slugify($title);
if ($slug === '') {
    $slug = 'project-' . time();
}
if (slug_exists($slug, $id)) {
    $errors[] = 'A project with this slug already exists.';
}

if (!empty($errors)) {
    $project = $id ? get_project_by_id($id) : null;
    $pageTitle = $id ? 'Edit: ' . ($project['title'] ?? '') : 'New project';
    require __DIR__ . '/includes/header.php';
    echo '<div class="page-header"><h1>' . h($pageTitle) . '</h1></div>';
    require __DIR__ . '/project-form.php';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$cardImage = (string) ($_POST['card_image_existing'] ?? '');
if (!empty($_FILES['card_image_file']['name'])) {
    try {
        $cardImage = save_uploaded_image($_FILES['card_image_file']);
    } catch (RuntimeException $e) {
        $errors[] = 'Card image: ' . $e->getMessage();
    }
}

$heroImage = (string) ($_POST['hero_image_existing'] ?? '');
if (!empty($_FILES['hero_image_file']['name'])) {
    try {
        $heroImage = save_uploaded_image($_FILES['hero_image_file']);
    } catch (RuntimeException $e) {
        $errors[] = 'Hero image: ' . $e->getMessage();
    }
}

if (!empty($errors)) {
    $project = $id ? get_project_by_id($id) : null;
    $pageTitle = $id ? 'Edit: ' . ($project['title'] ?? '') : 'New project';
    require __DIR__ . '/includes/header.php';
    echo '<div class="page-header"><h1>' . h($pageTitle) . '</h1></div>';
    require __DIR__ . '/project-form.php';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$data = [
    'slug' => $slug,
    'title' => $title,
    'category' => $category,
    'tagline' => (string) ($_POST['tagline'] ?? ''),
    'card_image' => $cardImage,
    'hero_image' => $heroImage,
    'credits' => $credits,
    'concept' => (string) ($_POST['concept'] ?? ''),
    'more_info' => (string) ($_POST['more_info'] ?? ''),
    'published' => isset($_POST['published']),
    'featured' => isset($_POST['featured']),
];

if ($id) {
    update_project($id, $data);
    header('Location: project-edit.php?id=' . $id);
} else {
    $data['published'] = isset($_POST['published']);
    $newId = create_project($data);
    header('Location: project-edit.php?id=' . $newId);
}
exit;
