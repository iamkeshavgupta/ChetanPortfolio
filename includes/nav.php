<?php
require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/helpers.php';
$navLeft = get_json_setting('nav_left', []);
$navRight = get_json_setting('nav_right', []);
?>
<nav class="site-nav">
  <button class="nav-toggle" aria-label="Menu" onclick="document.getElementById('nav-menu').classList.toggle('open')">
    <span></span><span></span>
  </button>
  <div class="nav-menu" id="nav-menu">
    <ul>
      <?php foreach ($navLeft as $link): ?>
        <li><a href="<?= h($link['href'] ?? '/') ?>"<?= str_starts_with($link['href'] ?? '', 'http') ? ' target="_blank" rel="noopener"' : '' ?>><?= h($link['label'] ?? '') ?></a></li>
      <?php endforeach; ?>
    </ul>
    <ul>
      <?php foreach ($navRight as $link): ?>
        <li><a href="<?= h($link['href'] ?? '/') ?>"<?= str_starts_with($link['href'] ?? '', 'http') ? ' target="_blank" rel="noopener"' : '' ?>><?= h($link['label'] ?? '') ?></a></li>
      <?php endforeach; ?>
    </ul>
  </div>
</nav>
