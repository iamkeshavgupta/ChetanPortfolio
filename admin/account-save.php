<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/settings.php';
require_once __DIR__ . '/../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: account.php');
    exit;
}

$username = trim((string) ($_POST['username'] ?? ''));
$currentPassword = (string) ($_POST['current_password'] ?? '');
$newPassword = (string) ($_POST['new_password'] ?? '');
$newPasswordConfirm = (string) ($_POST['new_password_confirm'] ?? '');

$storedHash = get_setting('admin_password_hash', '');
$errors = [];

if (!password_verify($currentPassword, $storedHash)) {
    $errors[] = 'Current password is incorrect.';
}
if ($username === '') {
    $errors[] = 'Username cannot be empty.';
}
if ($newPassword !== '' && $newPassword !== $newPasswordConfirm) {
    $errors[] = 'New password and confirmation do not match.';
}
if ($newPassword !== '' && strlen($newPassword) < 8) {
    $errors[] = 'New password must be at least 8 characters.';
}

if (!empty($errors)) {
    $pageTitle = 'Account';
    $currentUsername = get_setting('admin_username', 'admin');
    $success = false;
    require __DIR__ . '/includes/header.php';
    echo '<div class="page-header"><h1>Account</h1></div>';
    echo '<div class="flash flash-error">' . h(implode(' ', $errors)) . '</div>';
    require __DIR__ . '/account-form.php';
    require __DIR__ . '/includes/footer.php';
    exit;
}

set_setting('admin_username', $username);
if ($newPassword !== '') {
    set_setting('admin_password_hash', password_hash($newPassword, PASSWORD_DEFAULT));
}

// Username may have changed — keep the session in sync so require_admin()
// (which only checks admin_logged_in, not the username) keeps working, and
// regenerate the session id since credentials just changed.
session_regenerate_id(true);

header('Location: account.php?saved=1');
exit;
