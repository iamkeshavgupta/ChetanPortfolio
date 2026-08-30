<?php

function h(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function slugify(string $input): string {
    $slug = strtolower(trim($input));
    $slug = preg_replace('/[\'\x{2018}\x{2019}]/u', '', $slug);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug !== '' ? $slug : ('project-' . time());
}

/** @return array<int, array{role:string,names:string}> */
function decode_credits(string $json): array {
    $data = json_decode($json, true);
    if (!is_array($data)) {
        return [];
    }
    $out = [];
    foreach ($data as $row) {
        if (is_array($row) && isset($row['role'], $row['names'])) {
            $out[] = ['role' => (string) $row['role'], 'names' => (string) $row['names']];
        }
    }
    return $out;
}

function project_url(string $slug): string {
    return '/projects/' . rawurlencode($slug);
}

const PROJECT_CATEGORIES = ['photography' => 'Photography', 'videography' => 'Videography', 'design' => 'Design', 'other' => 'Other'];

function category_url(string $category): string {
    return '/category/' . rawurlencode($category);
}

function category_label(string $category): string {
    return PROJECT_CATEGORIES[$category] ?? ucfirst($category);
}

/**
 * Normalizes a YouTube or Vimeo watch/share URL (in any of their common
 * formats — youtu.be short links, /watch?v=, /shorts/, vimeo.com/ID) into
 * an embeddable player URL. Returns null if the URL doesn't look like a
 * recognized YouTube/Vimeo link, so the caller can fall back to a plain
 * link instead of a broken iframe.
 */
/**
 * Renders a gallery item (image, self-hosted video, or embedded
 * YouTube/Vimeo video) as the appropriate HTML tag. $class is applied to
 * the tag so the existing image CSS (object-fit: cover, sizing, etc.)
 * carries over to <video>/<iframe> too.
 */
function render_gallery_media(array $img, string $class = ''): string {
    $type = $img['media_type'] ?? 'image';
    $classAttr = $class !== '' ? ' class="' . h($class) . '"' : '';

    if ($type === 'video_file') {
        return '<video src="' . h($img['url']) . '" controls playsinline preload="metadata"' . $classAttr . '></video>';
    }

    if ($type === 'video_embed') {
        $embed = normalize_video_embed_url($img['url']);
        if ($embed !== null) {
            return '<iframe src="' . h($embed) . '" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen frameborder="0"' . $classAttr . '></iframe>';
        }
        return '<a href="' . h($img['url']) . '" target="_blank" rel="noopener"' . $classAttr . ' style="display:flex;align-items:center;justify-content:center;background:#111;color:#fff;">Watch video &#8599;</a>';
    }

    return '<img src="' . h($img['url']) . '" alt="' . h($img['caption'] ?? '') . '"' . $classAttr . '>';
}

function normalize_video_embed_url(string $url): ?string {
    $url = trim($url);

    if (preg_match('#youtu\.be/([a-zA-Z0-9_-]{6,})#', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }
    if (preg_match('#youtube\.com/(?:watch\?v=|shorts/|embed/)([a-zA-Z0-9_-]{6,})#', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }
    if (preg_match('#vimeo\.com/(?:video/)?(\d+)#', $url, $m)) {
        return 'https://player.vimeo.com/video/' . $m[1];
    }

    return null;
}
