<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$projectCount = 0;
$publishedCount = 0;
$featuredCount = 0;
$mediaCount = 0;
$videoCount = 0;
try {
    $pdo = get_pdo();
    $projectCount = (int) $pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn();
    $publishedCount = (int) $pdo->query('SELECT COUNT(*) FROM projects WHERE published = 1')->fetchColumn();
    $featuredCount = (int) $pdo->query('SELECT COUNT(*) FROM projects WHERE featured = 1')->fetchColumn();
    $mediaCount = (int) $pdo->query('SELECT COUNT(*) FROM project_images')->fetchColumn();
    $videoCount = (int) $pdo->query("SELECT COUNT(*) FROM project_images WHERE media_type != 'image'")->fetchColumn();
} catch (Throwable $e) {
    // Counts are just a convenience; ignore failures here.
}

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>

<div class="page-header"><h1>Dashboard</h1></div>
<p style="color:rgba(255,255,255,0.6);font-size:14px;margin-top:-20px;margin-bottom:32px;">
  Manage everything shown on the live site from here.
</p>

<div class="dashboard-grid">
  <a href="projects.php" class="dashboard-card">
    <h3>Projects (<?= $projectCount ?>)</h3>
    <p><?= $publishedCount ?> published, <?= $projectCount - $publishedCount ?> draft, <?= $featuredCount ?> featured. Add, edit, delete, and reorder.</p>
  </a>
  <a href="projects.php" class="dashboard-card">
    <h3>Gallery media (<?= $mediaCount ?>)</h3>
    <p><?= $videoCount ?> video<?= $videoCount === 1 ? '' : 's' ?> (uploaded or embedded), rest images — managed per-project on each project's edit page.</p>
  </a>
  <a href="pages.php" class="dashboard-card">
    <h3>Pages</h3>
    <p>Edit the content of the About and Contact pages.</p>
  </a>
  <a href="settings.php" class="dashboard-card">
    <h3>Settings</h3>
    <p>Homepage title, hero banner, footer text, and nav menu links.</p>
  </a>
  <a href="account.php" class="dashboard-card">
    <h3>Account</h3>
    <p>Change the admin username and password.</p>
  </a>
  <a href="/" target="_blank" class="dashboard-card">
    <h3>View site &#8599;</h3>
    <p>Open the live site in a new tab.</p>
  </a>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
