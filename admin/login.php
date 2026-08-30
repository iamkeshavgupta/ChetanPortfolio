<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/settings.php';

const MAX_LOGIN_ATTEMPTS = 5;
const LOCKOUT_MINUTES = 15;

$error = '';

if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$minutesLeft = null;

try {
    $stmt = get_pdo()->prepare(
        'SELECT TIMESTAMPDIFF(SECOND, NOW(), locked_until) AS seconds_left
         FROM admin_login_attempts WHERE ip_address = :ip AND locked_until > NOW()'
    );
    $stmt->execute([':ip' => $ip]);
    $row = $stmt->fetch();
    if ($row) {
        $minutesLeft = max(1, (int) ceil($row['seconds_left'] / 60));
    }
} catch (Throwable $e) {
}

if ($minutesLeft !== null) {
    $error = 'Too many failed login attempts. Try again in ' . $minutesLeft . ' minute' . ($minutesLeft === 1 ? '' : 's') . '.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = (string) ($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    $validUser = get_setting('admin_username', '');
    $validPassHash = get_setting('admin_password_hash', '');

    if ($validUser !== '' && $validPassHash !== '' && hash_equals($validUser, $username) && password_verify($password, $validPassHash)) {
        try {
            get_pdo()->prepare('DELETE FROM admin_login_attempts WHERE ip_address = :ip')->execute([':ip' => $ip]);
        } catch (Throwable $e) {
        }
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    }

    sleep(1); // slow down brute-force attempts

    try {
        $stmt = get_pdo()->prepare(
            'INSERT INTO admin_login_attempts (ip_address, attempts, last_attempt_at, locked_until)
             VALUES (:ip, 1, NOW(), NULL)
             ON DUPLICATE KEY UPDATE
               locked_until = IF(attempts + 1 >= :max_attempts, DATE_ADD(NOW(), INTERVAL :lockout_minutes MINUTE), NULL),
               attempts = attempts + 1,
               last_attempt_at = NOW()'
        );
        $stmt->execute([':ip' => $ip, ':max_attempts' => MAX_LOGIN_ATTEMPTS, ':lockout_minutes' => LOCKOUT_MINUTES]);
    } catch (Throwable $e) {
    }

    $error = 'Invalid username or password.';
}

require_once __DIR__ . '/../includes/helpers.php';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login — Chetan Gupta</title>
<meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <h1>Chetan Gupta</h1>
    <p>Admin dashboard login</p>
<?php if ($error): ?>
    <div class="flash flash-error"><?= h($error) ?></div>
<?php endif; ?>
    <form method="post">
      <div class="field">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" autocomplete="username" required autofocus>
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
      </div>
      <button type="submit" class="btn" style="width:100%">Log in</button>
    </form>
  </div>
</div>
</body>
</html>
