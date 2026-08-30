<?php
require_once __DIR__ . '/includes/settings.php';
require_once __DIR__ . '/includes/helpers.php';

$siteTitle = get_setting('site_title', 'CHETAN GUPTA');
$footerText = get_setting('footer_text', '© ' . date('Y') . ' Chetan Gupta');
$aboutContent = get_setting('about_content', "Write something about yourself here — edit this from the admin panel's Pages section.");
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>About — <?= h($siteTitle) ?></title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>
<main class="wrap">
  <?php require __DIR__ . '/includes/nav.php'; ?>

  <div class="hero-title">
    <h1>About</h1>
  </div>

  <section class="container" style="padding-top:80px;padding-bottom:100px;max-width:800px;">
    <p style="font-size:16px;font-weight:300;line-height:28px;white-space:pre-line;"><?= h($aboutContent) ?></p>
  </section>

  <footer class="site-footer">
    <p><?= h($footerText) ?></p>
  </footer>
</main>
</body>
</html>
