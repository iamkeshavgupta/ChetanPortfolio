<?php
require_once __DIR__ . '/db.php';

function get_setting(string $key, string $default = ''): string {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        $rows = get_pdo()->query('SELECT `key`, `value` FROM settings')->fetchAll();
        foreach ($rows as $row) {
            $cache[$row['key']] = $row['value'];
        }
    }
    return $cache[$key] ?? $default;
}

/** @return array<int, array<string, string>> */
function get_json_setting(string $key, array $default = []): array {
    $raw = get_setting($key, '');
    if ($raw === '') {
        return $default;
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : $default;
}

function set_setting(string $key, string $value): void {
    $stmt = get_pdo()->prepare(
        'INSERT INTO settings (`key`, `value`) VALUES (:key, :value)
         ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)'
    );
    $stmt->execute([':key' => $key, ':value' => $value]);
}

function set_json_setting(string $key, array $value): void {
    set_setting($key, json_encode($value));
}
