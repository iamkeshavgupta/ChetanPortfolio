# Chetan Gupta — Portfolio (PHP + MySQL)

Plain PHP + MySQL version of the portfolio site — deploys on any standard
cPanel / shared hosting account with zero special setup, the same way
Cabsonhire does. No Node.js, no build step: upload the files, create a
database, done.

## Deploying to cPanel

1. **Upload the files** — zip this folder and extract it into `public_html`
   (or a subfolder) via cPanel's File Manager, or upload over FTP.
2. **Create a database** — cPanel → MySQL Databases → create a database and
   a user, add the user to the database with "All Privileges". Note the
   database name, username, and password (they're usually prefixed with your
   cPanel username, e.g. `myuser_portfolio`).
3. **Import the schema** — cPanel → phpMyAdmin → select your new database →
   Import → choose `db/schema.sql` → Go. (If you're updating an existing
   deployment: run `db/migrate-add-media-type.sql` if it predates video
   support, and/or `db/migrate-add-category-featured.sql` if it predates
   categories/featured/About-Contact.)
4. **Configure**: copy `config.sample.php` to `config.php` and fill in the
   database credentials from step 2, plus an initial admin username/password
   (these seed the database once — see "Admin credentials" below).
5. **Seed the database** — either run `php db/seed.php` via cPanel's
   Terminal (if available), or open `https://yourdomain.com/db/seed.php` in
   a browser once, then delete/rename that file afterwards so it can't be
   run again. This populates site settings (title, nav, footer), migrates
   your admin login into the database, and (the first time only) adds the 4
   starter projects.
6. **Make sure `/uploads` is writable** — it should be by default (755), but
   if image uploads fail from the admin panel, set it to 755 (or 775) via
   File Manager's permissions dialog.

Your site is now live at your domain, with `/admin` as the admin panel.

## Local development (XAMPP / similar)

1. Install XAMPP (bundles PHP + MySQL/MariaDB + Apache).
2. Point Apache's `DocumentRoot` at this folder (or place it under `htdocs`
   and adjust asset paths — the templates use root-absolute paths like
   `/css/style.css`, so serving from the actual domain/document root, not a
   subfolder, matches production).
3. Create a database + user, import `db/schema.sql`, copy
   `config.sample.php` to `config.php` with those local credentials.
4. Run `php db/seed.php` once to populate starter content.
5. Start Apache + MySQL, visit `http://localhost/`.

## Admin dashboard

The admin panel is organized into separate sections (same idea as
Cabsonhire's admin), reachable from the nav bar on every admin page:

- **Dashboard** (`/admin/`) — landing page with live counts (projects,
  published/draft/featured, gallery media) and a card linking into each
  section below.
- **Projects** (`/admin/projects.php`) — the project list: reorder,
  publish/unpublish, edit, delete, create new. Each project's edit page
  also has its own gallery media manager.
- **Pages** (`/admin/pages.php`) — About and Contact page content.
- **Settings** (`/admin/settings.php`) — site-wide stuff: homepage title,
  hero image, footer text, nav menu links.
- **Account** (`/admin/account.php`) — change the admin login.

**Projects** (`/admin/projects.php`)
- Create / edit / delete projects (title, slug, category, tagline, concept,
  more info, credits)
- **Category** (Photography / Videography / Design / Other) — drives the
  Photography/Videography/Design nav links, each showing only projects in
  that category
- **Featured** checkbox — drives the "Selected work" nav link, which shows
  only featured projects (separate from "All projects", which shows
  everything published)
- Upload a card image (homepage) and hero image (project page) per project —
  re-encoded server-side to a capped-width JPEG on upload
- Add / remove / reorder gallery **media** per project, grouped into sections
  (`gallery`, `sneak-peek`, `social`, `kidsfilm`) — each item is an image, an
  uploaded video file, or an embedded YouTube/Vimeo video (see "Video
  support" below)
- Publish / unpublish a project (unpublished projects 404 on the live site)
- Reorder projects on the homepage

**Pages** (`/admin/pages.php`)
- About page content and Contact page content (plain text, one paragraph per
  blank line)

**Settings** (`/admin/settings.php`)
- Homepage title ("CHETAN GUPTA") and hero banner image
- Footer text
- Both nav menu columns (label + URL for each link, add/remove freely) —
  this is also where the Instagram link lives

**Account** (`/admin/account.php`)
- Change the admin username and/or password from the UI — no more editing
  `config.php` on the server to rotate credentials

Login is protected by the same brute-force lockout Cabsonhire uses (5 failed
attempts locks an IP out for 15 minutes).

## Pages / URL routes

| URL | What it shows |
|---|---|
| `/` | All published projects |
| `/projects/{slug}` | One project's detail page |
| `/category/photography`, `/category/videography`, `/category/design` | Published projects in that category |
| `/selected-work` | Published projects marked "Featured" |
| `/about`, `/contact` | Static content pages, edited from Pages |

All of these are wired into the nav menu by default, but the nav menu itself
is just data (see Settings above) — rename, remove, or add links freely; the
underlying pages exist independently of what's in the menu.

## Video support

Adding a video to a project gallery has two modes, picked per-item from the
"Add media" dropdown on the project's edit page:

- **Embed video URL (recommended)** — paste a YouTube or Vimeo link. It's
  normalized to an embeddable player and rendered in an `<iframe>`. No file
  size limit, no hosting bandwidth used, works on any host. Use this for
  anything longer than a few seconds.
- **Video file (max 50MB)** — uploads the actual file into `/uploads` and
  serves it directly via an HTML5 `<video>` tag, with no server-side
  compression (there's no practical way to transcode video without
  `ffmpeg`, which typical shared hosting doesn't have — so whatever you
  upload is exactly what gets served, at your hosting's bandwidth).

**Before uploading a video file**, check your host's PHP limits — a lot of
budget shared hosting caps uploads well under 50MB by default. If an upload
silently fails, it's almost always this. To check or raise it:
- cPanel → **MultiPHP INI Editor** (or **Select PHP Version** → Options) →
  raise `upload_max_filesize` and `post_max_size` (both need to be big
  enough — `post_max_size` should be a bit larger than
  `upload_max_filesize`), or
- if you have shell access, edit `php.ini` directly.

If raising the limit isn't available on your plan, use the embed option
instead — it has no such ceiling.

## Admin credentials

`config.php`'s `ADMIN_USER` / `ADMIN_PASSWORD` are only used **once**, the
first time you run `db/seed.php` — at that point they're copied into the
`settings` table (password hashed with `password_hash()`) and become the
real source of truth. From then on, change your login from **Account**
inside the dashboard, not by editing `config.php`. Re-running `db/seed.php`
later won't overwrite credentials you've already changed from the UI.

## Notes on fidelity to the Figma design

Same notes as the Next.js version this was ported from:

- Fonts are the real Canela Text Trial / Canela Trial (Light) files, in
  `fonts/`.
- A few Summer '26 detail-page images had an artistic torn-photo/polaroid
  mask effect in Figma that wasn't reproduced pixel-for-pixel — those photos
  are shown in a plain rounded-corner grid instead, since the effect was
  hand-crafted per-photo and wouldn't generalize to new projects added
  through the admin panel.
- Denimwear '26, Swimwear '26, and Mom & Me only had a homepage card in the
  Figma file (no dedicated detail-page design existed), so their project
  pages use the same template with placeholder-level content — fill in real
  credits/concept/gallery images from the admin panel.

## Tech

- Plain PHP 8+ (no framework, no Composer dependencies)
- MySQL / MariaDB via PDO
- GD extension for image resizing on upload (enable it in `php.ini` if it's
  not already: `extension=gd`)
- Session-based admin auth with the same login-lockout table as Cabsonhire
