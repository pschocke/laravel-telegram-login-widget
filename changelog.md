# Changelog

All notable changes to `TelegramLoginWidget` will be documented in this file.

## [2.0.0] - 2026-03-02

### Added
- Support for PHP 8.4 and 8.5.
- Support for Laravel 12.
- Modernized codebase with PHP 8.2+ features (readonly classes, mixed types, union types).
- Migrated test annotations to PHP attributes (`#[Test]`).

### Changed
- Minimum PHP requirement bumped to `^8.4`.
- Minimum Laravel requirement bumped to `^12.0`.
- Updated to PHPUnit 11.
- Updated to Orchestra Testbench 10.
- Updated GitHub Actions CI to test against PHP 8.4/8.5 and Laravel 12.
- Updated `phpunit.xml` to the modern configuration schema.

### Fixed
- Fixed broken Facade alias in `composer.json`.
- Fixed incorrect `Collection` import and PHPDoc in `TelegramLoginWidget` facade.
- Fixed abstract class warning during PHPUnit test discovery.

### Removed
- Scrutinizer CI integration and configuration.
- StyleCI integration and configuration.
- Codecov badges and unused TravisCI links.

## Version 1.0

### Added
- Everything
