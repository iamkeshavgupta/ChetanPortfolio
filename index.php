<?php
require_once __DIR__ . '/includes/projects.php';
require_once __DIR__ . '/includes/settings.php';

$projects = list_projects(true);
$siteTitle = get_setting('site_title', 'CHETAN GUPTA');
$heroImage = get_setting('hero_image', '/seed/hero-home-frame.jpg');
$footerText = get_setting('footer_text', '© ' . date('Y') . ' Chetan Gupta');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($siteTitle) ?> — Portfolio</title>
<meta name="description" content="Photography & videography portfolio of Chetan Gupta">
<link rel="stylesheet" href="/css/style.css">
</head>
<body>
<main class="wrap">
  <?php require __DIR__ . '/includes/nav.php'; ?>

  <div class="hero-title">
    <h1><?= h($siteTitle) ?></h1>
  </div>

  <?php if ($heroImage): ?>
  <div class="hero-image">
    <img src="<?= h($heroImage) ?>" alt="<?= h($siteTitle) ?>">
  </div>
  <?php endif; ?>

  <section class="container" style="padding-top:120px;">
    <?php require __DIR__ . '/includes/project-list.php'; ?>
  </section>

  <footer class="site-footer">
    <p><?= h($footerText) ?></p>
  </footer>
</main>
</body>
</html>
