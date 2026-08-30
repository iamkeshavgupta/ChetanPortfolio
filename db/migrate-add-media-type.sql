-- Run this once if you already deployed before video support was added
-- (i.e. your `project_images` table doesn't have a `media_type` column
-- yet). Safe to run even if it's already there — MySQL/MariaDB will just
-- error harmlessly on the duplicate column, which you can ignore.
ALTER TABLE project_images ADD COLUMN media_type VARCHAR(20) NOT NULL DEFAULT 'image' AFTER url;
