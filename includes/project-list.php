<?php
// Included by index.php, category.php, and selected.php. Expects $projects
// (array of project rows) and optional $emptyMessage.
$emptyMessage = $emptyMessage ?? 'No projects published yet.';
?>
<?php if (empty($projects)): ?>
  <p style="color:var(--fg-muted);text-align:center;padding:80px 0;"><?= h($emptyMessage) ?></p>
<?php endif; ?>
<?php foreach ($projects as $i => $p): ?>
  <div class="project-card-row <?= $i % 2 === 1 ? 'reverse' : '' ?>">
    <div class="card-image">
      <?php if ($p['card_image']): ?><img src="<?= h($p['card_image']) ?>" alt="<?= h($p['title']) ?>"><?php endif; ?>
    </div>
    <div class="card-text">
      <h2><?= h($p['title']) ?></h2>
      <p><?= h($p['tagline']) ?></p>
      <a href="<?= h(project_url($p['slug'])) ?>" class="pill-btn">View project</a>
    </div>
  </div>
<?php endforeach; ?>
