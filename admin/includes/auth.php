<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/** Call at the top of every protected admin page. Redirects to login if not authenticated. */
function require_admin(): void {
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: login.php');
        exit;
    }
}
