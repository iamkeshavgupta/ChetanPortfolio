<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/settings.php';
require_once __DIR__ . '/../includes/upload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: settings.php');
    exit;
}

$heroImage = (string) ($_POST['hero_image_existing'] ?? '');
if (!empty($_FILES['hero_image_file']['name'])) {
    try {
        $heroImage = save_uploaded_image($_FILES['hero_image_file']);
    } catch (RuntimeException $e) {
        header('Location: settings.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}

set_setting('site_title', trim((string) ($_POST['site_title'] ?? '')) ?: 'CHETAN GUPTA');
set_setting('hero_image', $heroImage);
set_setting('footer_text', (string) ($_POST['footer_text'] ?? ''));

function collect_links(array $labels, array $hrefs): array {
    $out = [];
    foreach ($labels as $i => $label) {
        $label = trim((string) $label);
        $href = trim((string) ($hrefs[$i] ?? ''));
        if ($label !== '' && $href !== '') {
            $out[] = ['label' => $label, 'href' => $href];
        }
    }
    return $out;
}

set_json_setting('nav_left', collect_links($_POST['nav_left_label'] ?? [], $_POST['nav_left_href'] ?? []));
set_json_setting('nav_right', collect_links($_POST['nav_right_label'] ?? [], $_POST['nav_right_href'] ?? []));

header('Location: settings.php?saved=1');
exit;
