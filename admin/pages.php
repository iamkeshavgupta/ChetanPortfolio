<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/settings.php';
require_once __DIR__ . '/../includes/helpers.php';

$pageTitle = 'Pages';
$aboutContent = get_setting('about_content', '');
$contactContent = get_setting('contact_content', '');
$success = isset($_GET['saved']);

require __DIR__ . '/includes/header.php';
?>

<div class="page-header"><h1>Pages</h1></div>

<?php if ($success): ?>
  <div class="flash flash-success">Pages saved.</div>
<?php endif; ?>

<p style="color:rgba(255,255,255,0.6);font-size:14px;margin-bottom:32px;">
  Content for the static <a href="/about" target="_blank" style="color:#fff;">/about</a> and
  <a href="/contact" target="_blank" style="color:#fff;">/contact</a> pages. Leave a blank line
  between paragraphs.
</p>

<form class="project-form" method="post" action="pages-save.php">
  <div class="field">
    <label for="about_content">About page</label>
    <textarea id="about_content" name="about_content" style="min-height:220px"><?= h($aboutContent) ?></textarea>
  </div>

  <div class="field">
    <label for="contact_content">Contact page</label>
    <textarea id="contact_content" name="contact_content" style="min-height:160px"><?= h($contactContent) ?></textarea>
  </div>

  <button type="submit" class="btn">Save pages</button>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
