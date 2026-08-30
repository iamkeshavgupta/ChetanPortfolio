<?php
// Loads config.php (falling back to config.local.php if present) and hands
// back a single shared PDO connection.

function get_pdo(): PDO {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $localConfigPath = __DIR__ . '/../config.local.php';
    $configPath = file_exists($localConfigPath) ? $localConfigPath : __DIR__ . '/../config.php';

    if (!file_exists($configPath)) {
        http_response_code(500);
        die('Server is not configured yet. Copy config.sample.php to config.php and fill in your database credentials.');
    }

    require_once $configPath;

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
