<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
require_once __DIR__ . '/../includes/settings.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: pages.php');
    exit;
}

set_setting('about_content', (string) ($_POST['about_content'] ?? ''));
set_setting('contact_content', (string) ($_POST['contact_content'] ?? ''));

header('Location: pages.php?saved=1');
exit;
