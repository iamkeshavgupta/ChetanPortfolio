<?php
$__currentScript = basename($_SERVER['SCRIPT_NAME']);
function nav_active(string ...$scripts): string {
    global $__currentScript;
    return in_array($__currentScript, $scripts, true) ? ' class="active"' : '';
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($pageTitle) ? h($pageTitle) . ' — ' : '' ?>Admin — Chetan Gupta</title>
<meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="topbar">
  <a href="index.php" class="brand">CHETAN GUPTA — Admin</a>
  <nav class="topbar-nav">
    <a href="index.php"<?= nav_active('index.php') ?>>Dashboard</a>
    <a href="projects.php"<?= nav_active('projects.php', 'project-new.php', 'project-edit.php') ?>>Projects</a>
    <a href="pages.php"<?= nav_active('pages.php') ?>>Pages</a>
    <a href="settings.php"<?= nav_active('settings.php') ?>>Settings</a>
    <a href="account.php"<?= nav_active('account.php') ?>>Account</a>
  </nav>
  <div class="actions">
    <a href="/" target="_blank">View site &#8599;</a>
    <a href="logout.php" class="btn" style="height:32px;padding:0 16px;">Log out</a>
  </div>
</div>
<div class="page">
