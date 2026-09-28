# AGENTS.md

Internal web app **"AD KL SN, QR and PIAT Monitoring"** — tracks electricity service connections (SN = service numbers), QR records, and PIAT (Pre-commissioning, Inspection And Testing) checklists across four business areas (BA): `KLB - 6121`, `KLT - 6122`, `KLP - 6123`, `KLS - 6124`. Malaysian utility domain; many DB columns and UI labels are in Malay (`tarikh_siap` = completion date, `jenis_sambungan` = connection type, `alamat` = address).

## Stack / Environment

- **Plain PHP multi-page app** — no framework, no routing, no templating, no build step, no tests, no linter, no CI.
- Runs on **XAMPP** (Apache + PHP) from `D:/xampp/htdocs/piat/` → visit `http://localhost/piat/`. The root `index.php` is just a landing page; the real app is `src/index.php`.
- **Database is PostgreSQL (not MySQL!)** via PDO with the `pgsql` extension — db `piat_checklist` on `localhost:5432`. XAMPP ships without `pdo_pgsql` by default; it must be enabled in `php.ini`.
- Composer dependency: only `phpoffice/phpspreadsheet` (Excel import/export). **`vendor/` is committed to the repo** — `composer install` is normally unnecessary.
- Frontend: Bootstrap 5 + jQuery + DataTables + Chart.js loaded from CDNs (app needs internet), plus local `assets/lib/dselect.js`.

## Critical gotchas

1. **`src/services/connection.php` is gitignored** (it holds DB credentials). A fresh clone is broken until you recreate it:
   ```php
   <?php
   $hostname = 'localhost'; $port = 5432; $database = 'piat_checklist';
   $username = 'postgres'; $password = '<local password>';
   $pdo = new PDO("pgsql:host=$hostname;dbname=$database;port=$port", $username, $password);
   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   ```
   It exposes a single global `$pdo` that every script expects after `include`.
2. **A second, hardcoded DB connection exists**: `src/services/submitSnMonitoring.php` connects to `permit_tracking` on `192.168.1.34:5432` and inserts into `permit_records` when permit type is `DBKL` or `PBT`. Failures are logged via `error_log` and do **not** fail the main insert. Don't remove or "fix" this — it feeds an external system.
3. **Deletes are backups**: `src/services/removeSn.php` copies the full row into `remove_ad_service_qr` before deleting from `ad_service_qr`. Preserve this when touching delete logic.
4. **Hardcoded year cutoffs**: many queries filter `(status in ('Inprogress','KIV') or complete_date >= '2026-01-01' or tarikh_siap >= '2026-01-01')`. The literal `2026-01-01` appears in `src/index.php`, `src/user/dashboard-count.php`, `src/admin/dashboard-count.php`, `src/services/generateExcel.php`, and legacy files — it is bumped manually each year.
5. **Aging is inclusive**: aging/day counts add **+1** (`csp_paid_date` → `tarikh_siap` or today). This `+1` is repeated in SQL (`src/index.php` aging query) and PHP (`checkAgging()` in `src/index.php`, `checkAging()` in `src/tables/sn-table.php`). Keep all copies consistent.
6. **Relative include paths vary by directory depth** — pages in `src/` use `./services/...` and `../../assets/...`, pages in `src/*/` use `../services/...` and `../../assets/...`. When adding/moving pages, check every include/require/href/img src.
7. **Legacy/dead files — do not edit or copy from these**:
   - `src/piat_old.php` — old dashboard, still linked via "OLD PIAT" button (keep working, but it's frozen).
   - `src/index-------.php`, `src/piat_old.php`, `src/*_old.php`, `src/services/submitSnMonitoring-1.php`, `src/services/submit-foam-1.php`, `src/sn-monitoring/sn_monitoring-1.php` — superseded snapshots.
   - `src/includes/` (modals, filter-form, action-buttons) and `src/tables/` — only included by the legacy `index-------.php`; the live tables/modals are inlined in `src/index.php`.
   - `src/services/queries.php` — empty stub; `src/auth/register.php` — empty file.
   - `src/services/excel.php` — a one-off data-migration script (hardcoded file/rows, `$stmt->execute()` commented out). Not part of the app flow.
8. **BA code mapping**: `ad_service_qr.ba` stores the long form (`KLB - 6121`) but the `users` table `station` column stores the short form (`KLB`, `KLT`, `KLP`, `KLS`). `src/services/foamRedirect.php` does the mapping; PIC dropdowns (`getUsersByBA.php`) query `users` by short code.

## Auth & roles

- Login (`src/auth/login.php`) queries `auth_users` (username column is **`name`**) and `password_verify()` against bcrypt hashes. Session keys: `user_name`, `user_id`, `user_ba`, `user_role`.
- Roles (see `src/services/access.php`):
  - `admin` — literally username `admin`; sees all BAs.
  - `user` — full write access, locked to their own `user_ba`.
  - `viewer` — read-only; sees everything admin sees (all BAs + dashboard), no add/edit/delete buttons.
- **Every write endpoint must call `deny_if_viewer()`** (from `services/access.php`) right after `session_start()`. UI pages additionally gate buttons with `if (!$isViewer)`.
- `is_admin_view()` returns true for username `admin` **or** any viewer — used to decide whether BA filter/all-BA data is shown.
- Page guard pattern: `session_start(); if (!isset($_SESSION['user_name']) && !isset($_SESSION['user_id'])) { header('location:../auth/login.php'); }` — `src/partials/header.php` already does this for subpages (include it first).

## Data model (PostgreSQL, schema `public`)

| Table | Purpose |
|---|---|
| `ad_service_qr` | Main record: SN, BA, connection type (`jenis_sambungan` = `OH`/`UG`), status (`Complete`/`Inprogress`/`KIV`), CSP date, completion date, aging, vendor, remarks |
| `inspection_checklist` | PIAT checklist linked by `ad_service_id` → `ad_service_qr.id`; several columns store **JSON-encoded arrays** (`company`, `company_name`, `company_phone_no`, `company_sign`, `inspection_checklist` — exactly 31 items). `project_no` mirrors `no_sn` and is re-synced on update. |
| `users` | PIC list per station (short BA code) |
| `auth_users` | Login accounts (name, ba, role, bcrypt password) |
| `vendors` | Vendor list (managed via Add Vendor modal → `services/addVendor.php`) |
| `remove_ad_service_qr` | Backup table populated on delete |
| `permit_records` (external `permit_tracking` DB) | Fed from `submitSnMonitoring.php` |

Business rule: `jenis_sambungan = 'OH'` requires PIAT (`piat = 'yes'`, status starts `Inprocess`); `UG` auto-completes with `piat = 'no'`.

## Architecture / flows

- `src/index.php` is the dashboard: filter form (BA, date type CSP/Completion/Both, date range, aging bucket), tabs **SN Monitoring / QR / Dashboard** (Dashboard tab is admin/viewer only), status + aging charts (Chart.js), and both data tables rendered inline with `foreach` + `echo`. Filtering is a POST back to itself; empty date ranges are filled with MIN/MAX from the DB.
- **SN flow**: `sn-monitoring/create.php` (or `edit.php?no_sn=`) → POST `services/submitSnMonitoring.php` (insert-or-update chosen by presence of `id`; duplicate `no_sn` check on insert) → redirect `../index.php`.
- **PIAT flow**: `services/foamRedirect.php?sn=` stashes record data in `$_SESSION` then redirects to `piat-foam/create.php` or `edit.php` (depending on whether a checklist row exists) → POST `services/submitFoam.php` → printable checklist at `generate-pdf/previewPDF.php?no_sn=` (print CSS + `exportToPDF()`).
- **JSON AJAX endpoints** (`services/getSnDetail.php`, `getUsersByBA.php`, `getVendors.php`): GET, return `{success, message, data}` envelope with `Content-Type: application/json`. Follow this shape for new endpoints.
- **Excel export**: `services/generateExcel.php` (PhpSpreadsheet) streams/downloads per BA + date range; the "Download QR/SN" buttons on the dashboard POST hidden fields mirroring the filter form.

## Conventions to follow

- **Flash messages**: set `$_SESSION['message']` + `$_SESSION['alert']` (`'alert-success'`/`'alert-danger'`), redirect, then display-and-`unset()` in the target page. Copy this pattern, don't introduce a new one.
- **Always use prepared statements** with `bindParam`/`execute([...])` (existing style). Note `generateExcel.php`/`index.php` interpolate `$col_name` into SQL — it comes from a whitelist of literals, keep that whitelist closed.
- Set `$pdo = null;` after finishing DB work in a script (existing habit).
- Forms POST to a `services/submit*.php` action which redirects (`header('Location: ../index.php'); exit;`) — no inline rendering of results.
- Subpages include `src/partials/header.php` (auth guard + Bootstrap/jQuery/dselect + navbar) and use a breadcrumb block; page CSS goes in a `<style>` tag or `assets/style.css`, page JS in `assets/js/*.js`.

## Commands

- **Run**: start Apache (and PostgreSQL) via XAMPP; app at `http://localhost/piat/`. No dev server, no watchers.
- **Lint a PHP file** (syntax only): `D:/xampp/php/php.exe -l <file>` (PHP is not on PATH).
- **Composer**: `composer install` only if `vendor/` is missing/dirty; never commit updated vendor casually (it's tracked).
- **Tests**: none exist. Verify changes by clicking through the affected flow in the browser.
- `assets/Excel/` holds generated exports; `*.xlsx` is gitignored (some historical exports were committed — leave them).
