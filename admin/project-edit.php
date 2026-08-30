<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/projects.php';

$id = (int) ($_GET['id'] ?? 0);
$project = get_project_by_id($id);
if (!$project) {
    header('Location: projects.php');
    exit;
}

$pageTitle = 'Edit: ' . $project['title'];
$errors = [];

require __DIR__ . '/includes/header.php';
?>

<a href="projects.php" style="font-size:13px;color:rgba(255,255,255,0.6);">&larr; Back to projects</a>
<h1 style="font-family:'Canela Trial',Georgia,serif;font-weight:300;font-size:32px;margin:16px 0 32px;">
  Edit: <?= h($project['title']) ?>
</h1>

<?php require __DIR__ . '/project-form.php'; ?>

<hr style="margin:48px 0;border:none;border-top:1px solid rgba(255,255,255,0.15);">

<h2 style="font-family:'Canela Trial',Georgia,serif;font-weight:300;font-size:24px;margin-bottom:20px;">Gallery images</h2>

<?php if (isset($_GET['img_error'])): ?>
  <div class="flash flash-error"><?= h($_GET['img_error']) ?></div>
<?php endif; ?>

Max video upload size is 50MB — for anything longer, use "Embed video URL" instead (YouTube/Vimeo, no size limit, doesn't use your hosting's bandwidth). See the README for why.
<form class="image-upload-row" method="post" action="image-upload.php" enctype="multipart/form-data" style="flex-wrap:wrap;align-items:flex-start;">
  <input type="hidden" name="project_id" value="<?= (int) $project['id'] ?>">
  <select name="section">
    <option value="gallery">gallery</option>
    <option value="sneak-peek">sneak-peek</option>
    <option value="social">social</option>
    <option value="kidsfilm">kidsfilm</option>
  </select>
  <select name="media_type" id="media-type-select" onchange="onMediaTypeChange()">
    <option value="image">Image</option>
    <option value="video_file">Video file (max 50MB)</option>
    <option value="video_embed">Embed video URL</option>
  </select>
  <input type="file" name="file" id="media-file-input" accept="image/*" required>
  <input type="text" name="embed_url" id="media-embed-input" placeholder="https://youtube.com/watch?v=... or https://vimeo.com/..." style="display:none;flex:1;min-width:260px;">
  <button type="submit" class="btn" style="height:36px;padding:0 16px;font-size:13px;">+ Add media</button>
</form>
<script>
function onMediaTypeChange() {
  const type = document.getElementById('media-type-select').value;
  const fileInput = document.getElementById('media-file-input');
  const embedInput = document.getElementById('media-embed-input');
  if (type === 'video_embed') {
    fileInput.style.display = 'none';
    fileInput.required = false;
    embedInput.style.display = '';
    embedInput.required = true;
  } else {
    fileInput.style.display = '';
    fileInput.required = true;
    fileInput.accept = type === 'video_file' ? 'video/mp4,video/webm,video/quicktime,video/ogg' : 'image/*';
    embedInput.style.display = 'none';
    embedInput.required = false;
  }
}
</script>

<?php if (empty($project['images'])): ?>
  <p style="color:rgba(255,255,255,0.6);font-size:14px;">No images yet.</p>
<?php else: ?>
  <div class="image-grid">
    <?php foreach ($project['images'] as $i => $img): ?>
      <div class="image-card">
        <div class="thumb">
          <?php if ($img['media_type'] === 'video_file'): ?>
            <video src="<?= h($img['url']) ?>" muted preload="metadata" style="width:100%;height:100%;object-fit:cover;"></video>
          <?php elseif ($img['media_type'] === 'video_embed'): ?>
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#111;color:rgba(255,255,255,0.6);font-size:12px;padding:8px;text-align:center;overflow:hidden;">
              &#9654; <?= h($img['url']) ?>
            </div>
          <?php else: ?>
            <img src="<?= h($img['url']) ?>" alt="<?= h($img['caption']) ?>">
          <?php endif; ?>
        </div>
        <div class="body">
          <span class="badge" style="align-self:flex-start;">
            <?= $img['media_type'] === 'video_file' ? 'Video file' : ($img['media_type'] === 'video_embed' ? 'Video embed' : 'Image') ?>
          </span>
          <form method="post" action="image-update.php">
            <input type="hidden" name="id" value="<?= (int) $img['id'] ?>">
            <input type="hidden" name="project_id" value="<?= (int) $project['id'] ?>">
            <select name="section" onchange="this.form.submit()">
              <?php foreach (['gallery', 'sneak-peek', 'social', 'kidsfilm'] as $opt): ?>
                <option value="<?= $opt ?>" <?= $img['section'] === $opt ? 'selected' : '' ?>><?= $opt ?></option>
              <?php endforeach; ?>
            </select>
            <div style="display:flex;gap:6px;margin-top:8px;">
              <input type="text" name="caption" placeholder="Caption (optional)" value="<?= h($img['caption']) ?>" style="flex:1">
              <button type="submit" class="link-btn">Save</button>
            </div>
          </form>
          <div class="row">
            <div class="arrows">
              <form method="post" action="image-reorder.php" style="display:inline">
                <input type="hidden" name="id" value="<?= (int) $img['id'] ?>">
                <input type="hidden" name="project_id" value="<?= (int) $project['id'] ?>">
                <input type="hidden" name="direction" value="up">
                <button type="submit" class="arrow" <?= $i === 0 ? 'disabled' : '' ?>>&#9664;</button>
              </form>
              <form method="post" action="image-reorder.php" style="display:inline">
                <input type="hidden" name="id" value="<?= (int) $img['id'] ?>">
                <input type="hidden" name="project_id" value="<?= (int) $project['id'] ?>">
                <input type="hidden" name="direction" value="down">
                <button type="submit" class="arrow" <?= $i === count($project['images']) - 1 ? 'disabled' : '' ?>>&#9654;</button>
              </form>
            </div>
            <form method="post" action="image-delete.php" onsubmit="return confirm('Remove this image?');">
              <input type="hidden" name="id" value="<?= (int) $img['id'] ?>">
              <input type="hidden" name="project_id" value="<?= (int) $project['id'] ?>">
              <button type="submit" class="link-btn danger">Delete</button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
