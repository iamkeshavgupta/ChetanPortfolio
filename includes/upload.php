<?php

const UPLOAD_MAX_BYTES = 15 * 1024 * 1024; // 15MB
const UPLOAD_ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

const VIDEO_MAX_BYTES = 50 * 1024 * 1024; // 50MB — see README for why this stays small on shared hosting
const VIDEO_ALLOWED_TYPES = [
    'video/mp4' => 'mp4',
    'video/webm' => 'webm',
    'video/quicktime' => 'mov',
    'video/ogg' => 'ogv',
];

/**
 * Saves an uploaded image (from $_FILES) into /uploads, re-encoding it as a
 * JPEG capped at 2000px wide so users can't accidentally upload multi-MB
 * originals straight from a camera. Returns the public URL path.
 *
 * @throws RuntimeException on validation or processing failure.
 */
function save_uploaded_image(array $file): string {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed (error code ' . ($file['error'] ?? 'unknown') . ').');
    }
    if ($file['size'] > UPLOAD_MAX_BYTES) {
        throw new RuntimeException('File too large (max 15MB).');
    }

    $mime = mime_content_type($file['tmp_name']);
    if (!in_array($mime, UPLOAD_ALLOWED_TYPES, true)) {
        throw new RuntimeException('Unsupported file type. Use JPEG, PNG, WebP, or GIF.');
    }

    $uploadsDir = __DIR__ . '/../uploads';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
    }

    $id = bin2hex(random_bytes(16));
    $filename = $id . '.jpg';
    $destPath = $uploadsDir . '/' . $filename;

    $image = load_image_any_format($file['tmp_name'], $mime);
    if ($image === false) {
        throw new RuntimeException('Could not process image.');
    }

    // Auto-orient JPEGs that carry EXIF rotation.
    if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
        $image = fix_jpeg_orientation($image, $file['tmp_name']);
    }

    $width = imagesx($image);
    $height = imagesy($image);
    $maxWidth = 2000;
    if ($width > $maxWidth) {
        $newHeight = (int) round($height * ($maxWidth / $width));
        $resized = imagecreatetruecolor($maxWidth, $newHeight);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);
        imagedestroy($image);
        $image = $resized;
    }

    imagejpeg($image, $destPath, 85);
    imagedestroy($image);

    return '/uploads/' . $filename;
}

/** @return \GdImage|false */
function load_image_any_format(string $path, string $mime) {
    return match ($mime) {
        'image/jpeg' => imagecreatefromjpeg($path),
        'image/png' => imagecreatefrompng($path),
        'image/webp' => imagecreatefromwebp($path),
        'image/gif' => imagecreatefromgif($path),
        default => false,
    };
}

/** @param \GdImage $image @return \GdImage */
function fix_jpeg_orientation($image, string $path) {
    $exif = @exif_read_data($path);
    if (!$exif || empty($exif['Orientation'])) {
        return $image;
    }
    switch ($exif['Orientation']) {
        case 3:
            return imagerotate($image, 180, 0);
        case 6:
            return imagerotate($image, -90, 0);
        case 8:
            return imagerotate($image, 90, 0);
        default:
            return $image;
    }
}

/**
 * Saves an uploaded video file as-is (no re-encoding — that would need
 * ffmpeg, which typical shared hosting doesn't have). Returns the public
 * URL path.
 *
 * @throws RuntimeException on validation failure, including PHP-level
 *   upload errors from php.ini's upload_max_filesize/post_max_size, which
 *   silently truncate/reject uploads before this code ever runs.
 */
function save_uploaded_video(array $file): string {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        if (($file['error'] ?? null) === UPLOAD_ERR_INI_SIZE || ($file['error'] ?? null) === UPLOAD_ERR_FORM_SIZE) {
            throw new RuntimeException(
                'File exceeds this server\'s upload size limit (set by php.ini upload_max_filesize/post_max_size). ' .
                'Ask your host to raise it, or use "Embed video URL" (YouTube/Vimeo) instead.'
            );
        }
        throw new RuntimeException('Upload failed (error code ' . ($file['error'] ?? 'unknown') . ').');
    }
    if ($file['size'] > VIDEO_MAX_BYTES) {
        throw new RuntimeException('Video too large (max ' . (VIDEO_MAX_BYTES / 1024 / 1024) . 'MB). Use "Embed video URL" (YouTube/Vimeo) for longer clips.');
    }

    $mime = mime_content_type($file['tmp_name']);
    if (!isset(VIDEO_ALLOWED_TYPES[$mime])) {
        throw new RuntimeException('Unsupported video type. Use MP4, WebM, MOV, or OGV.');
    }

    $uploadsDir = __DIR__ . '/../uploads';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
    }

    $id = bin2hex(random_bytes(16));
    $filename = $id . '.' . VIDEO_ALLOWED_TYPES[$mime];
    $destPath = $uploadsDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        throw new RuntimeException('Could not save the uploaded video.');
    }

    return '/uploads/' . $filename;
}

function delete_uploaded_file(string $url): void {
    if (!str_starts_with($url, '/uploads/')) {
        return; // never delete seed assets shipped with the site
    }
    $path = __DIR__ . '/..' . $url;
    if (is_file($path)) {
        unlink($path);
    }
}
