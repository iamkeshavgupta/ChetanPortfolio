<?php
require_once __DIR__ . '/includes/projects.php';
require_once __DIR__ . '/includes/settings.php';

$type = $_GET['type'] ?? '';
if (!array_key_exists($type, PROJECT_CATEGORIES)) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$projects = list_projects(true, $type);
$siteTitle = get_setting('site_title', 'CHETAN GUPTA');
$footerText = get_setting('footer_text', '© ' . date('Y') . ' Chetan Gupta');
$label = category_label($type);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($label) ?> — <?= h($siteTitle) ?></title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>
<main class="wrap">
  <?php require __DIR__ . '/includes/nav.php'; ?>

  <div class="hero-title">
    <h1><?= h($label) ?></h1>
  </div>

  <section class="container" style="padding-top:120px;">
    <?php $emptyMessage = "No {$label} projects published yet."; ?>
    <?php require __DIR__ . '/includes/project-list.php'; ?>
  </section>

  <footer class="site-footer">
    <p><?= h($footerText) ?></p>
  </footer>
</main>
</body>
</html>
