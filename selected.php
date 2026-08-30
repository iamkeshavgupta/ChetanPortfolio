<?php
require_once __DIR__ . '/includes/projects.php';
require_once __DIR__ . '/includes/settings.php';

$projects = list_projects(true, null, true);
$siteTitle = get_setting('site_title', 'CHETAN GUPTA');
$footerText = get_setting('footer_text', '© ' . date('Y') . ' Chetan Gupta');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Selected Work — <?= h($siteTitle) ?></title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>
<main class="wrap">
  <?php require __DIR__ . '/includes/nav.php'; ?>

  <div class="hero-title">
    <h1>Selected Work</h1>
  </div>

  <section class="container" style="padding-top:120px;">
    <?php $emptyMessage = 'No projects marked as featured yet — mark some as "Featured" in the admin panel.'; ?>
    <?php require __DIR__ . '/includes/project-list.php'; ?>
  </section>

  <footer class="site-footer">
    <p><?= h($footerText) ?></p>
  </footer>
</main>
</body>
</html>
