-- Run this once if you already deployed before category/featured/About-Contact
-- support was added (i.e. your `projects` table doesn't have `category` or
-- `featured` columns yet).
ALTER TABLE projects ADD COLUMN category VARCHAR(20) NOT NULL DEFAULT 'other' AFTER title;
ALTER TABLE projects ADD COLUMN featured TINYINT(1) NOT NULL DEFAULT 0 AFTER published;
