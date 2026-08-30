<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/helpers.php';

$pageTitle = 'New project';
$project = null;
$errors = [];

require __DIR__ . '/includes/header.php';
?>
<div class="page-header"><h1>New project</h1></div>
<?php require __DIR__ . '/project-form.php'; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
