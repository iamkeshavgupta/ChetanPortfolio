<?php
// Copy this file to config.php and fill in your real cPanel MySQL credentials.
// cPanel -> MySQL Databases: create a database and a user, add the user to the
// database with "All Privileges", then paste the details below.
//
// Database/user names on shared hosting are usually prefixed with your
// cPanel username, e.g. "myuser_portfolio" and "myuser_dbuser".

define('DB_HOST', 'localhost');
define('DB_NAME', 'yourcpaneluser_portfolio');
define('DB_USER', 'yourcpaneluser_dbuser');
define('DB_PASS', 'your-db-password');

// Initial login for the admin dashboard (/admin/). These values are only
// used ONCE, the first time you run `php db/seed.php` (or visit
// db/seed.php in a browser) — at that point they're copied into the
// database (password hashed) and become the real source of truth. After
// that, change your username/password from the Account page inside the
// admin dashboard, not by editing this file.
define('ADMIN_USER', 'admin');
define('ADMIN_PASSWORD', 'change-this-password');
