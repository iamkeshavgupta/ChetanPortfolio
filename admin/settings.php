<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/settings.php';
require_once __DIR__ . '/../includes/helpers.php';

$pageTitle = 'Settings';
$siteTitle = get_setting('site_title', 'CHETAN GUPTA');
$heroImage = get_setting('hero_image', '/seed/hero-home-frame.jpg');
$footerText = get_setting('footer_text', '© ' . date('Y') . ' Chetan Gupta');
$navLeft = get_json_setting('nav_left', []);
$navRight = get_json_setting('nav_right', []);
$success = isset($_GET['saved']);

require __DIR__ . '/includes/header.php';
?>

<div class="page-header"><h1>Settings</h1></div>

<?php if ($success): ?>
  <div class="flash flash-success">Settings saved.</div>
<?php endif; ?>

<p style="color:rgba(255,255,255,0.6);font-size:14px;margin-bottom:32px;">
  Looking for About/Contact page content? That's under <a href="pages.php" style="color:#fff;">Pages</a> now.
</p>

<form class="project-form" method="post" action="settings-save.php" enctype="multipart/form-data">
  <div class="field">
    <label for="site_title">Homepage title</label>
    <input id="site_title" name="site_title" type="text" value="<?= h($siteTitle) ?>" required>
  </div>

  <div class="field">
    <label>Hero banner image (homepage top)</label>
    <div class="thumbnail-picker">
      <?php if ($heroImage): ?>
        <div class="thumb"><img src="<?= h($heroImage) ?>" alt=""></div>
      <?php endif; ?>
      <input type="file" name="hero_image_file" accept="image/*">
    </div>
    <input type="hidden" name="hero_image_existing" value="<?= h($heroImage) ?>">
  </div>

  <div class="field">
    <label for="footer_text">Footer text</label>
    <input id="footer_text" name="footer_text" type="text" value="<?= h($footerText) ?>">
  </div>

  <div class="field">
    <label>Nav menu — left column</label>
    <div id="nav-left-list">
      <?php foreach ($navLeft as $link): ?>
        <div class="credit-row">
          <input type="text" name="nav_left_label[]" placeholder="Label" value="<?= h($link['label'] ?? '') ?>">
          <input type="text" name="nav_left_href[]" placeholder="URL" value="<?= h($link['href'] ?? '') ?>">
          <button type="button" onclick="this.closest('.credit-row').remove()">&times;</button>
        </div>
      <?php endforeach; ?>
    </div>
    <button type="button" class="btn" style="height:32px;padding:0 14px;font-size:13px;" onclick="addNavRow('nav-left-list')">+ Add link</button>
  </div>

  <div class="field">
    <label>Nav menu — right column</label>
    <div id="nav-right-list">
      <?php foreach ($navRight as $link): ?>
        <div class="credit-row">
          <input type="text" name="nav_right_label[]" placeholder="Label" value="<?= h($link['label'] ?? '') ?>">
          <input type="text" name="nav_right_href[]" placeholder="URL" value="<?= h($link['href'] ?? '') ?>">
          <button type="button" onclick="this.closest('.credit-row').remove()">&times;</button>
        </div>
      <?php endforeach; ?>
    </div>
    <button type="button" class="btn" style="height:32px;padding:0 14px;font-size:13px;" onclick="addNavRow('nav-right-list')">+ Add link</button>
    <p style="color:rgba(255,255,255,0.5);font-size:12px;margin-top:8px;">
      This is also where the Instagram link lives — use a full URL (e.g. https://instagram.com/yourhandle) and it'll open in a new tab automatically.
    </p>
  </div>

  <button type="submit" class="btn">Save settings</button>
</form>

<script>
function addNavRow(listId) {
  const prefix = listId === 'nav-left-list' ? 'nav_left' : 'nav_right';
  const wrap = document.createElement('div');
  wrap.className = 'credit-row';
  wrap.innerHTML = `<input type="text" name="${prefix}_label[]" placeholder="Label">` +
    `<input type="text" name="${prefix}_href[]" placeholder="URL">` +
    `<button type="button" onclick="this.closest('.credit-row').remove()">&times;</button>`;
  document.getElementById(listId).appendChild(wrap);
}
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
