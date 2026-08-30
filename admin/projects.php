<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/projects.php';

$pageTitle = 'Projects';
$projects = list_projects(false);

require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <h1>Projects</h1>
  <a href="project-new.php" class="btn">+ New project</a>
</div>

<?php if (isset($_GET['deleted'])): ?>
  <div class="flash flash-success">Project deleted.</div>
<?php endif; ?>

<?php if (empty($projects)): ?>
  <p style="color:rgba(255,255,255,0.6)">No projects yet. Create your first one.</p>
<?php else: ?>
  <?php foreach ($projects as $i => $p): ?>
    <div class="project-row">
      <div class="order-arrows">
        <form method="post" action="reorder.php">
          <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
          <input type="hidden" name="direction" value="up">
          <button type="submit" <?= $i === 0 ? 'disabled' : '' ?>>&#9650;</button>
        </form>
        <form method="post" action="reorder.php">
          <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
          <input type="hidden" name="direction" value="down">
          <button type="submit" <?= $i === count($projects) - 1 ? 'disabled' : '' ?>>&#9660;</button>
        </form>
      </div>

      <div class="thumb">
        <?php if ($p['card_image']): ?><img src="<?= h($p['card_image']) ?>" alt=""><?php endif; ?>
      </div>

      <div class="meta">
        <p><?= h($p['title']) ?></p>
        <p class="slug">/projects/<?= h($p['slug']) ?></p>
      </div>

      <span class="badge <?= $p['published'] ? 'published' : 'draft' ?>">
        <?= $p['published'] ? 'Published' : 'Draft' ?>
      </span>

      <form method="post" action="toggle-publish.php" style="display:inline">
        <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
        <button type="submit" class="link-btn"><?= $p['published'] ? 'Unpublish' : 'Publish' ?></button>
      </form>

      <a href="project-edit.php?id=<?= (int) $p['id'] ?>" class="link-btn">Edit</a>

      <form method="post" action="project-delete.php" style="display:inline" onsubmit="return confirm('Delete &quot;<?= h(addslashes($p['title'])) ?>&quot;? This cannot be undone.');">
        <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
        <button type="submit" class="link-btn danger">Delete</button>
      </form>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
