<?php
require_once __DIR__ . '/includes/projects.php';
require_once __DIR__ . '/includes/settings.php';

$footerText = get_setting('footer_text', '© ' . date('Y') . ' Chetan Gupta');

$slug = $_GET['slug'] ?? '';
$project = $slug !== '' ? get_project_by_slug($slug) : null;

if (!$project || !$project['published']) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$sectionLabels = [
    'kidsfilm' => "Kids' Film",
    'social' => 'Social Adaptation',
    'sneak-peek' => 'Sneak peek from the set',
    'gallery' => 'Gallery',
];

function section_label(array $labels, string $section): string {
    if (isset($labels[$section])) {
        return $labels[$section];
    }
    return ucwords(str_replace('-', ' ', $section));
}

/** Groups images by section, preserving first-appearance order. */
function group_by_section(array $images): array {
    $order = [];
    $groups = [];
    foreach ($images as $img) {
        $section = $img['section'];
        if (!isset($groups[$section])) {
            $groups[$section] = [];
            $order[] = $section;
        }
        $groups[$section][] = $img;
    }
    $out = [];
    foreach ($order as $section) {
        $out[] = ['section' => $section, 'images' => $groups[$section]];
    }
    return $out;
}

$groups = group_by_section($project['images']);

$allProjects = list_projects(true);
$moreWork = array_slice(array_values(array_filter($allProjects, fn($p) => $p['id'] !== $project['id'])), 0, 4);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($project['title']) ?> — Chetan Gupta</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body>
<main class="wrap">
  <?php require __DIR__ . '/includes/nav.php'; ?>

  <div class="hero-title">
    <h1><?= h($project['title']) ?></h1>
  </div>

  <?php if ($project['hero_image']): ?>
    <div class="project-hero-image">
      <img src="<?= h($project['hero_image']) ?>" alt="<?= h($project['title']) ?>">
    </div>
  <?php endif; ?>

  <section class="container detail-body">
    <?php if (!empty($project['credits']) || $project['concept'] || $project['more_info']): ?>
      <div class="two-col">
        <?php if (!empty($project['credits'])): ?>
          <div>
            <h2 class="section-heading">Credits</h2>
            <div class="credits-cols">
              <div>
                <?php foreach ($project['credits'] as $c): ?><p><?= h($c['role']) ?></p><?php endforeach; ?>
              </div>
              <div>
                <?php foreach ($project['credits'] as $c): ?><p><?= h($c['names']) ?></p><?php endforeach; ?>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($project['concept'] || $project['more_info']): ?>
          <div>
            <?php if ($project['concept']): ?>
              <h2 class="section-heading">Concept</h2>
              <p class="concept-text"><?= h($project['concept']) ?></p>
            <?php endif; ?>
            <?php if ($project['more_info']): ?>
              <h2 class="section-heading">More Info</h2>
              <p class="more-info-text"><?= h($project['more_info']) ?></p>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($groups)): ?>
      <div class="gallery-section">
        <?php foreach ($groups as $group): ?>
          <?php $section = $group['section']; $images = $group['images']; ?>
          <section>
            <h2 class="section-heading"><?= h(section_label($sectionLabels, $section)) ?></h2>

            <?php if ($section === 'kidsfilm'): ?>
              <div class="grid-kidsfilm">
                <?= render_gallery_media($images[0], 'media-el') ?>
              </div>

            <?php elseif ($section === 'social'): ?>
              <div class="grid-social" style="grid-template-columns: repeat(<?= min(count($images), 3) ?>, 1fr);">
                <?php foreach ($images as $img): ?>
                  <div class="cell"><?= render_gallery_media($img, 'media-el') ?></div>
                <?php endforeach; ?>
              </div>

            <?php else: ?>
              <div class="grid-masonry" style="column-count: <?= count($images) > 4 ? 3 : 2 ?>;">
                <?php foreach ($images as $img): ?>
                  <div class="cell"><?= render_gallery_media($img, 'media-el') ?></div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </section>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <?php if (!empty($moreWork)): ?>
    <section class="container more-work">
      <h2 class="section-heading" style="text-align:center;">More Work</h2>
      <div class="more-work-grid" style="grid-template-columns: repeat(<?= min(count($moreWork), 4) ?>, 1fr);">
        <?php foreach ($moreWork as $p): ?>
          <a class="item" href="<?= h(project_url($p['slug'])) ?>">
            <div class="thumb">
              <?php if ($p['card_image']): ?><img src="<?= h($p['card_image']) ?>" alt="<?= h($p['title']) ?>"><?php endif; ?>
            </div>
            <p><?= h($p['title']) ?></p>
          </a>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <footer class="site-footer">
    <p><?= h($footerText) ?></p>
  </footer>
</main>
</body>
</html>
