-- Chetan Gupta Portfolio — initial schema
-- Run this once against your cPanel MySQL database (phpMyAdmin -> Import,
-- or the SQL tab) after creating the database and user in cPanel -> MySQL
-- Databases.

-- category is one of: photography, videography, design, other — drives the
-- Photography/Videography/Design nav links (each shows projects.category =
-- that value). featured drives the "Selected work" nav link (shows only
-- featured = 1).
CREATE TABLE IF NOT EXISTS projects (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(191) NOT NULL UNIQUE,
  title VARCHAR(255) NOT NULL,
  category VARCHAR(20) NOT NULL DEFAULT 'other',
  tagline TEXT NOT NULL,
  card_image VARCHAR(500) NOT NULL DEFAULT '',
  hero_image VARCHAR(500) NOT NULL DEFAULT '',
  credits_json TEXT NOT NULL DEFAULT '[]',
  concept TEXT NOT NULL DEFAULT '',
  more_info TEXT NOT NULL DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 1,
  featured TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Despite the name, this holds gallery *media* generally: images, uploaded
-- video files, and embedded YouTube/Vimeo videos (media_type = 'image' /
-- 'video_file' / 'video_embed'). For 'video_embed' rows, `url` holds the
-- YouTube/Vimeo watch URL as pasted by the admin — it's normalized to an
-- embeddable player URL at render time, not at save time, so switching the
-- normalization logic later doesn't require a data migration.
CREATE TABLE IF NOT EXISTS project_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  project_id INT UNSIGNED NOT NULL,
  url VARCHAR(500) NOT NULL,
  media_type VARCHAR(20) NOT NULL DEFAULT 'image',
  caption VARCHAR(255) NOT NULL DEFAULT '',
  section VARCHAR(50) NOT NULL DEFAULT 'gallery',
  sort_order INT NOT NULL DEFAULT 0,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Site-wide settings: site title, hero image, footer text, nav links, and
-- the admin login credentials (username + hashed password). Populated by
-- db/seed.php, which also migrates the initial admin user/password out of
-- config.php's ADMIN_USER/ADMIN_PASSWORD constants.
CREATE TABLE IF NOT EXISTS settings (
  `key` VARCHAR(100) PRIMARY KEY,
  `value` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Same brute-force login lockout table Cabsonhire uses.
CREATE TABLE IF NOT EXISTS admin_login_attempts (
  ip_address VARCHAR(45) NOT NULL PRIMARY KEY,
  attempts INT NOT NULL DEFAULT 0,
  last_attempt_at TIMESTAMP NULL,
  locked_until TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
