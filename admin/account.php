<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/settings.php';
require_once __DIR__ . '/../includes/helpers.php';

$pageTitle = 'Account';
$currentUsername = get_setting('admin_username', 'admin');
$errors = [];
$success = isset($_GET['saved']);

require __DIR__ . '/includes/header.php';
?>

<div class="page-header"><h1>Account</h1></div>

<?php if ($success): ?>
  <div class="flash flash-success">Account updated.</div>
<?php endif; ?>
<?php if (!empty($errors)): ?>
  <div class="flash flash-error"><?= h(implode(' ', $errors)) ?></div>
<?php endif; ?>

<?php require __DIR__ . '/account-form.php'; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
