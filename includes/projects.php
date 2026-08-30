<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

/** @return array<int, array<string, mixed>> */
function list_projects(bool $publishedOnly = false, ?string $category = null, bool $featuredOnly = false): array {
    $pdo = get_pdo();
    $where = [];
    $params = [];
    if ($publishedOnly) {
        $where[] = 'published = 1';
    }
    if ($category !== null) {
        $where[] = 'category = :category';
        $params[':category'] = $category;
    }
    if ($featuredOnly) {
        $where[] = 'featured = 1';
    }
    $sql = 'SELECT * FROM projects';
    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY sort_order ASC, id ASC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return array_map('attach_images', $stmt->fetchAll());
}

function get_project_by_slug(string $slug): ?array {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE slug = ?');
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    return $row ? attach_images($row) : null;
}

function get_project_by_id(int $id): ?array {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ? attach_images($row) : null;
}

function attach_images(array $project): array {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM project_images WHERE project_id = ? ORDER BY sort_order ASC, id ASC');
    $stmt->execute([$project['id']]);
    $project['images'] = $stmt->fetchAll();
    $project['credits'] = decode_credits($project['credits_json']);
    return $project;
}

function slug_exists(string $slug, ?int $excludeId = null): bool {
    $pdo = get_pdo();
    if ($excludeId !== null) {
        $stmt = $pdo->prepare('SELECT id FROM projects WHERE slug = ? AND id != ?');
        $stmt->execute([$slug, $excludeId]);
    } else {
        $stmt = $pdo->prepare('SELECT id FROM projects WHERE slug = ?');
        $stmt->execute([$slug]);
    }
    return (bool) $stmt->fetch();
}

function create_project(array $data): int {
    $pdo = get_pdo();
    $maxOrder = (int) $pdo->query('SELECT COALESCE(MAX(sort_order), -1) FROM projects')->fetchColumn();

    $stmt = $pdo->prepare(
        'INSERT INTO projects (slug, title, category, tagline, card_image, hero_image, credits_json, concept, more_info, sort_order, published, featured)
         VALUES (:slug, :title, :category, :tagline, :card_image, :hero_image, :credits_json, :concept, :more_info, :sort_order, :published, :featured)'
    );
    $stmt->execute([
        ':slug' => $data['slug'],
        ':title' => $data['title'],
        ':category' => $data['category'] ?? 'other',
        ':tagline' => $data['tagline'] ?? '',
        ':card_image' => $data['card_image'] ?? '',
        ':hero_image' => $data['hero_image'] ?? '',
        ':credits_json' => json_encode($data['credits'] ?? []),
        ':concept' => $data['concept'] ?? '',
        ':more_info' => $data['more_info'] ?? '',
        ':sort_order' => $maxOrder + 1,
        ':published' => !empty($data['published']) ? 1 : 0,
        ':featured' => !empty($data['featured']) ? 1 : 0,
    ]);
    return (int) $pdo->lastInsertId();
}

function update_project(int $id, array $data): void {
    $pdo = get_pdo();
    $fields = [];
    $params = [':id' => $id];

    $map = [
        'slug' => 'slug', 'title' => 'title', 'category' => 'category', 'tagline' => 'tagline',
        'card_image' => 'card_image', 'hero_image' => 'hero_image',
        'concept' => 'concept', 'more_info' => 'more_info', 'sort_order' => 'sort_order',
    ];
    foreach ($map as $key => $col) {
        if (array_key_exists($key, $data)) {
            $fields[] = "$col = :$key";
            $params[":$key"] = $data[$key];
        }
    }
    if (array_key_exists('credits', $data)) {
        $fields[] = 'credits_json = :credits_json';
        $params[':credits_json'] = json_encode($data['credits']);
    }
    if (array_key_exists('featured', $data)) {
        $fields[] = 'featured = :featured';
        $params[':featured'] = $data['featured'] ? 1 : 0;
    }
    if (array_key_exists('published', $data)) {
        $fields[] = 'published = :published';
        $params[':published'] = $data['published'] ? 1 : 0;
    }
    if (empty($fields)) {
        return;
    }
    $sql = 'UPDATE projects SET ' . implode(', ', $fields) . ' WHERE id = :id';
    $pdo->prepare($sql)->execute($params);
}

function delete_project(int $id): void {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('DELETE FROM projects WHERE id = ?');
    $stmt->execute([$id]);
}

/** @param int[] $orderedIds */
function reorder_projects(array $orderedIds): void {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('UPDATE projects SET sort_order = ? WHERE id = ?');
    $pdo->beginTransaction();
    try {
        foreach ($orderedIds as $index => $id) {
            $stmt->execute([$index, $id]);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function add_project_image(int $projectId, string $url, string $caption, string $section, string $mediaType = 'image'): int {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT COALESCE(MAX(sort_order), -1) FROM project_images WHERE project_id = ?');
    $stmt->execute([$projectId]);
    $maxOrder = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare(
        'INSERT INTO project_images (project_id, url, media_type, caption, section, sort_order) VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$projectId, $url, $mediaType, $caption, $section, $maxOrder + 1]);
    return (int) $pdo->lastInsertId();
}

function update_project_image(int $id, array $data): void {
    $pdo = get_pdo();
    $fields = [];
    $params = [':id' => $id];
    foreach (['caption', 'section', 'sort_order', 'media_type', 'url'] as $key) {
        if (array_key_exists($key, $data)) {
            $fields[] = "$key = :$key";
            $params[":$key"] = $data[$key];
        }
    }
    if (empty($fields)) {
        return;
    }
    $sql = 'UPDATE project_images SET ' . implode(', ', $fields) . ' WHERE id = :id';
    $pdo->prepare($sql)->execute($params);
}

function get_project_image(int $id): ?array {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM project_images WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function delete_project_image(int $id): void {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('DELETE FROM project_images WHERE id = ?');
    $stmt->execute([$id]);
}
