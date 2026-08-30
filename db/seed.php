<?php
// Run once from the command line (php db/seed.php) or by visiting it in a
// browser, to populate the database with the 4 starter projects. Safe to
// run only when the projects table is empty — it exits early otherwise.

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/projects.php';
require_once __DIR__ . '/../includes/settings.php';

// --- Settings: site title, hero image, footer, nav links, admin login ---
// Idempotent (ON DUPLICATE KEY UPDATE via set_setting), safe to run every
// deploy. Admin credentials are migrated from config.php's ADMIN_USER /
// ADMIN_PASSWORD constants only the first time (i.e. only if not already
// present in the settings table), so changes made later from the admin UI
// are never overwritten by re-running this script.
$localConfigPath = __DIR__ . '/../config.local.php';
$configPath = file_exists($localConfigPath) ? $localConfigPath : __DIR__ . '/../config.php';
if (file_exists($configPath)) {
    require_once $configPath;
}

set_setting('site_title', get_setting('site_title', 'CHETAN GUPTA'));
set_setting('hero_image', get_setting('hero_image', '/seed/hero-home-frame.jpg'));
set_setting('footer_text', get_setting('footer_text', '© ' . date('Y') . ' Chetan Gupta'));
set_setting('instagram_url', get_setting('instagram_url', 'https://instagram.com'));

if (get_setting('nav_left', '') === '') {
    set_json_setting('nav_left', [
        ['label' => 'All projects', 'href' => '/'],
        ['label' => 'Selected work', 'href' => '/selected-work'],
        ['label' => 'Videography', 'href' => '/category/videography'],
        ['label' => 'Photography', 'href' => '/category/photography'],
        ['label' => 'Design', 'href' => '/category/design'],
    ]);
}
if (get_setting('nav_right', '') === '') {
    set_json_setting('nav_right', [
        ['label' => 'About', 'href' => '/about'],
        ['label' => 'Contact', 'href' => '/contact'],
        ['label' => 'Instagram', 'href' => defined('INSTAGRAM_URL') ? INSTAGRAM_URL : 'https://instagram.com'],
    ]);
}
if (get_setting('about_content', '') === '') {
    set_setting('about_content', "Chetan Gupta is a photographer and videographer specializing in fashion and lifestyle campaigns.\n\nEdit this from the admin panel's Pages section.");
}
if (get_setting('contact_content', '') === '') {
    set_setting('contact_content', "Get in touch for bookings and collaborations.\n\nemail@example.com\n+91 00000 00000");
}

$existingUser = get_pdo()->prepare("SELECT `value` FROM settings WHERE `key` = 'admin_username'");
$existingUser->execute();
if (!$existingUser->fetchColumn()) {
    $defaultUser = defined('ADMIN_USER') ? ADMIN_USER : 'admin';
    $defaultPass = defined('ADMIN_PASSWORD') ? ADMIN_PASSWORD : 'change-this-password';
    set_setting('admin_username', $defaultUser);
    set_setting('admin_password_hash', password_hash($defaultPass, PASSWORD_DEFAULT));
    echo "Migrated admin credentials from config.php into the database (username: $defaultUser).\n";
    echo "You can change them from the admin panel's Account page from now on.\n";
}

echo "Settings seeded.\n";

// --- Starter projects ---
$existing = (int) get_pdo()->query('SELECT COUNT(*) FROM projects')->fetchColumn();
if ($existing > 0) {
    echo "Projects already exist, skipping project seed. Truncate the `projects` table to reseed.\n";
    exit;
}

$summerTagline = "A fresh, effortless take on summer style, capturing relaxed moments in a warm, sun-soaked setting. The campaign blends everyday fashion, vibrant textures, and candid summer energy to create an aspirational yet approachable visual world.";
$denimTagline = "A contemporary take on everyday denim, combining effortless styling, youthful energy, and confident individuality. The campaign showcases denim as a versatile wardrobe essential—from casual everyday looks to elevated, statement styling.";

$projects = [
    [
        'slug' => 'summer-26',
        'title' => "Summer '26",
        'category' => 'videography',
        'featured' => true,
        'tagline' => $summerTagline,
        'card_image' => '/seed/card-summer.jpg',
        'hero_image' => '/seed/gallery-summer-hero-bg.jpg',
        'credits' => [
            ['role' => 'Cinematographer', 'names' => 'Chetan Gupta, Zofeen Raza'],
            ['role' => 'Creative Director / Head Stylist / Editor / Light / Conceptualization', 'names' => 'Sulakshna'],
        ],
        'concept' => $summerTagline,
        'more_info' => "Gear – Sony A7R3, 20-70mm f2.8, Aperture 600\nJuly 2026, Dubai, UAE",
        'published' => true,
        'images' => [
            ['url' => '/seed/gallery-summer-g10.jpg', 'section' => 'kidsfilm', 'caption' => "Kids' Film"],
            ['url' => '/seed/gallery-summer-g7.jpg', 'section' => 'social', 'caption' => ''],
            ['url' => '/seed/gallery-summer-g8.jpg', 'section' => 'social', 'caption' => ''],
            ['url' => '/seed/gallery-summer-g9.jpg', 'section' => 'social', 'caption' => ''],
            ['url' => '/seed/gallery-summer-kidsfilm.jpg', 'section' => 'sneak-peek', 'caption' => ''],
            ['url' => '/seed/gallery-summer-g1.jpg', 'section' => 'sneak-peek', 'caption' => ''],
            ['url' => '/seed/gallery-summer-g2.jpg', 'section' => 'sneak-peek', 'caption' => ''],
            ['url' => '/seed/gallery-summer-g3.jpg', 'section' => 'sneak-peek', 'caption' => ''],
            ['url' => '/seed/gallery-summer-g4.jpg', 'section' => 'sneak-peek', 'caption' => ''],
            ['url' => '/seed/gallery-summer-g5.jpg', 'section' => 'sneak-peek', 'caption' => ''],
            ['url' => '/seed/gallery-summer-g6.jpg', 'section' => 'sneak-peek', 'caption' => ''],
            ['url' => '/seed/gallery-summer-g11.jpg', 'section' => 'sneak-peek', 'caption' => ''],
        ],
    ],
    [
        'slug' => 'denimwear-26',
        'title' => "Denimwear '26",
        'category' => 'photography',
        'featured' => false,
        'tagline' => $denimTagline,
        'card_image' => '/seed/card-denimwear.jpg',
        'hero_image' => '/seed/card-denimwear.jpg',
        'credits' => [],
        'concept' => $denimTagline,
        'more_info' => '',
        'published' => true,
        'images' => [],
    ],
    [
        'slug' => 'swimwear-26',
        'title' => "Swimwear '26",
        'category' => 'photography',
        'featured' => true,
        'tagline' => $summerTagline,
        'card_image' => '/seed/card-swimwear.jpg',
        'hero_image' => '/seed/card-swimwear.jpg',
        'credits' => [],
        'concept' => $summerTagline,
        'more_info' => '',
        'published' => true,
        'images' => [],
    ],
    [
        'slug' => 'mom-and-me',
        'title' => 'Mom & Me',
        'category' => 'photography',
        'featured' => false,
        'tagline' => $denimTagline,
        'card_image' => '/seed/card-momme.jpg',
        'hero_image' => '/seed/card-momme.jpg',
        'credits' => [],
        'concept' => $denimTagline,
        'more_info' => '',
        'published' => true,
        'images' => [],
    ],
];

foreach ($projects as $p) {
    $id = create_project($p);
    foreach ($p['images'] as $i => $img) {
        add_project_image($id, $img['url'], $img['caption'], $img['section']);
    }
    echo "Inserted project \"{$p['title']}\" (" . count($p['images']) . " images)\n";
}

echo "Seed complete.\n";
