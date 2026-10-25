# Changelog

This changelog documents **only the two latest published GitHub releases** of `qbank_distractorcheck`. Each entry is based on the actual Git comparison between the published tag and its immediately preceding **published release** tag. Commits after `1.0.3` are excluded.

## [1.0.3] - 2026-10-15

Published: 2026-10-15 20:29:54 UTC.

Comparison: [1.0.2...1.0.3](https://github.com/EduardoKrausME/moodle-qbank_distractorcheck/compare/1.0.2...1.0.3)

### Continuous integration

- Updated the PostgreSQL CI service image from `postgres:16` to **`postgres:17`**.
- Updated the MariaDB CI service image from `mariadb:10` to **`mariadb:11.4`**.
- Replaced the previous MariaDB empty-password environment settings with `MARIADB_ALLOW_EMPTY_ROOT_PASSWORD=1`; changed the service health check from `mysqladmin ping` to `healthcheck.sh --connect --innodb_initialized`, with adjusted retry settings.

### Release metadata and scope

- Updated `version.php` from plugin version `2026101200` / release `1.0.2` to `2026101500` / release `1.0.3`.
- The comparison includes four commits and modifies **only** `.github/workflows/ci.yml` and `version.php`. It contains no runtime plugin source changes, new question-analysis behavior, or changes to the minimum Moodle build (`2024100700`) or `local_ai_bridge` dependency requirement.

## [1.0.2] - 2026-10-12

Published: 2026-10-12 11:50:51 UTC.

Comparison: [1.0.1...1.0.2](https://github.com/EduardoKrausME/moodle-qbank_distractorcheck/compare/1.0.1...1.0.2)

### Continuous integration and compatibility testing

- Updated the PHP 8.3 CI matrix entry from `MOODLE_502_STABLE` to **`MOODLE_503_STABLE` (Moodle 5.3)**.
- Added a CI checkout of `EduardoKrausME/moodle-local_ai_bridge` into `extra-plugins/ai_bridge` and passed `--extra-plugins ./extra-plugins` to `moodle-plugin-ci install`, so CI installs the plugin's existing AI Bridge dependency.

### Release workflow and assets

- Updated release-message parsing to pass the GitHub commit message through a `RELEASE_MESSAGE` environment variable rather than interpolating untrusted commit text directly into a shell script, reducing shell-injection risk.
- Simplified the release workflow: removed its previous custom ZIP-building and asset-upload steps (which referenced `local_ai_bridge` paths and ZIP names), while retaining annotated-tag creation and GitHub Release publication. Accordingly, this tag diff does **not** establish that the release workflow publishes an installable plugin ZIP asset.
- Added the plugin icon at `pix/icon.png`.

### Release metadata and scope

- Updated `version.php` from plugin version `2026100300` / release `1.0.1` to `2026101200` / release `1.0.2`.
- The comparison includes five commits and changes only `.github/workflows/ci.yml`, `.github/workflows/release.yml`, `pix/icon.png`, and `version.php`. No runtime plugin source files changed; the minimum Moodle build remains `2024100700`, and the `local_ai_bridge` dependency version remains `2026093001`.

---

These entries reflect published release-tag differences, not tests executed while writing this file or unreleased changes on `master`.
