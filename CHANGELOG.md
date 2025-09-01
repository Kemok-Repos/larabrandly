# Changelog

All notable changes to `larabrandly` will be documented in this file.

## v1.1.1 - 2025-09-01

### Fixed
- Fixed `updateLink()` method to use POST instead of PUT as required by Rebrandly API
- Updated unit tests to match the corrected HTTP method

## v1.1.0 - 2024-12-XX

### Added
- Laravel 12 support
- Compatibility with latest Laravel framework versions

### Changed
- Updated dependency constraints to support `illuminate/contracts ^12.0`
- Updated GitHub Actions matrix to test against Laravel 12

## v1.0.0 - 2024-XX-XX

### Added
- Initial release
- Complete CRUD operations for links (create, read, update, delete, list)
- Account management functionality
- Comprehensive tag management system
- Bidirectional tag-link relationships
- Advanced link filtering with LinkFilters DTO
- Data Transfer Objects (DTOs) for type safety
- Service Layer Architecture with dependency injection
- Facade Pattern implementation for Laravel integration
- Comprehensive error handling with custom exceptions
- Complete test coverage (Unit and Integration tests)
- PHPStan static analysis (Level 8)
- PHP-CS-Fixer code formatting
- GitHub Actions CI/CD pipeline
- Support for Laravel 9, 10, and 11
- Support for PHP 8.1, 8.2, and 8.3