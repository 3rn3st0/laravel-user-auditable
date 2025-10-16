# Changelog

All notable changes to `laravel-user-auditable` will be documented in this file.

## [1.0.0] - 2025-10-15

### Added
- Initial release! 🎉
- User auditing macros: `userAuditable()`, `fullAuditable()`, etc.
- `UserAuditable` trait for automatic user tracking
- Support for ID, UUID, and ULID key types
- Query scopes: `createdBy()`, `updatedBy()`, `deletedBy()`
- Relationships: `creator()`, `updater()`, `deleter()`
- Comprehensive configuration file
- Service provider with auto-discovery

### Technical
- PSR-4 autoloading
- Laravel package auto-discovery
- Configuration publishing
- MIT License
