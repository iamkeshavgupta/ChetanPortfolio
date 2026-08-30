<?php
// Included by project-new.php and project-edit.php.
// Expects: $project (array|null), $errors (array).
$isEdit = $project !== null;
$v = fn(string $key, $default = '') => $isEdit ? ($project[$key] ?? $default) : $default;
$credits = $isEdit ? $project['credits'] : [];
?>

<?php if (!empty($errors)): ?>
  <div class="flash flash-error"><?= h(implode(' ', $errors)) ?></div>
<?php endif; ?>

<form class="project-form" method="post" action="project-save.php" enctype="multipart/form-data">
  <?php if ($isEdit): ?>
    <input type="hidden" name="id" value="<?= (int) $project['id'] ?>">
  <?php endif; ?>

  <div class="field">
    <label for="title">Title</label>
    <input id="title" name="title" type="text" value="<?= h($v('title')) ?>" required>
  </div>

  <div class="field">
    <label for="slug">Slug (URL path)</label>
    <input id="slug" name="slug" type="text" value="<?= h($v('slug')) ?>" placeholder="auto-generated from title if left blank">
  </div>

  <div class="field">
    <label for="category">Category <span style="color:rgba(255,255,255,0.5)">(drives the Photography/Videography/Design nav links)</span></label>
    <select id="category" name="category">
      <?php foreach (PROJECT_CATEGORIES as $value => $label): ?>
        <option value="<?= h($value) ?>" <?= $v('category', 'other') === $value ? 'selected' : '' ?>><?= h($label) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="field">
    <label for="tagline">Tagline / homepage description</label>
    <textarea id="tagline" name="tagline"><?= h($v('tagline')) ?></textarea>
  </div>

  <div class="field">
    <label>Card image (homepage)</label>
    <div class="thumbnail-picker">
      <?php if ($v('card_image')): ?>
        <div class="thumb"><img src="<?= h($v('card_image')) ?>" alt=""></div>
      <?php endif; ?>
      <input type="file" name="card_image_file" accept="image/*">
    </div>
    <input type="hidden" name="card_image_existing" value="<?= h($v('card_image')) ?>">
  </div>

  <div class="field">
    <label>Hero image (project page top)</label>
    <div class="thumbnail-picker">
      <?php if ($v('hero_image')): ?>
        <div class="thumb"><img src="<?= h($v('hero_image')) ?>" alt=""></div>
      <?php endif; ?>
      <input type="file" name="hero_image_file" accept="image/*">
    </div>
    <input type="hidden" name="hero_image_existing" value="<?= h($v('hero_image')) ?>">
  </div>

  <div class="field">
    <label for="concept">Concept</label>
    <textarea id="concept" name="concept" style="min-height:100px"><?= h($v('concept')) ?></textarea>
  </div>

  <div class="field">
    <label for="more_info">More info (gear, location, date&hellip;)</label>
    <textarea id="more_info" name="more_info"><?= h($v('more_info')) ?></textarea>
  </div>

  <div class="field">
    <label>Credits</label>
    <div id="credits-list">
      <?php foreach ($credits as $c): ?>
        <div class="credit-row">
          <input type="text" name="credit_role[]" placeholder="Role" value="<?= h($c['role']) ?>">
          <input type="text" name="credit_names[]" placeholder="Name(s)" value="<?= h($c['names']) ?>">
          <button type="button" onclick="this.closest('.credit-row').remove()">&times;</button>
        </div>
      <?php endforeach; ?>
    </div>
    <button type="button" class="btn" style="height:32px;padding:0 14px;font-size:13px;" onclick="addCreditRow()">+ Add credit</button>
  </div>

  <div class="field" style="display:flex;align-items:center;gap:10px;">
    <input type="checkbox" id="published" name="published" value="1" <?= $v('published', true) ? 'checked' : '' ?> style="width:16px;height:16px;">
    <label for="published" style="margin:0">Published (visible on the live site)</label>
  </div>

  <div class="field" style="display:flex;align-items:center;gap:10px;">
    <input type="checkbox" id="featured" name="featured" value="1" <?= $v('featured', false) ? 'checked' : '' ?> style="width:16px;height:16px;">
    <label for="featured" style="margin:0">Featured <span style="color:rgba(255,255,255,0.5)">(shows on the "Selected work" nav page)</span></label>
  </div>

  <button type="submit" class="btn"><?= $isEdit ? 'Save changes' : 'Create project' ?></button>
</form>

<script>
function addCreditRow() {
  const wrap = document.createElement('div');
  wrap.className = 'credit-row';
  wrap.innerHTML = '<input type="text" name="credit_role[]" placeholder="Role">' +
    '<input type="text" name="credit_names[]" placeholder="Name(s)">' +
    '<button type="button" onclick="this.closest(\'.credit-row\').remove()">&times;</button>';
  document.getElementById('credits-list').appendChild(wrap);
}
</script>
