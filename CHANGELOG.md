# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 2026-03-18

### Added

#### API Coverage
- **New endpoints**:
  - `UserResource::me()` — `GET /users/me` (get authenticated user)
  - `TimeTrackingResource::status()` — `GET /timetracking/status` (get active tracking session)
  - `InvoiceResource::getReportsJson()` — `GET /issued-invoice/{id}/reports-json` (invoice work reports)
- **New models**: `TaskBasic`, `TaskWork`, `WorkReportExtended`
- **Updated models**: `Task` now includes `trackingUsers` field
- **OpenAPI auto-generation**: Generator script (`scripts/generate-models.php`) parses the OpenAPI spec
  and generates model classes into `src/Generated/Model/` with `fromArray()`/`toArray()` pattern
- **CI workflow**: `.github/workflows/openapi-check.yml` verifies generated code stays in sync with spec

### Changed
- Updated `homepage` and `support` URLs from GitLab to GitHub

#### Testing & Documentation (Phase 6)
- **Comprehensive Model Tests**: Added unit tests for all model classes
  - StateTest, UserTest, TasklistTest, SubtaskTest
  - HourRateTest, CurrencyTest, WorkReportTest
  - NoteTest, EventTest, TaskLabelTest, NotificationTest
  - ProjectLabelTest, CustomFieldTest, SearchResultTest, PinnedItemTest
  - IssuedInvoiceTest, TimeEstimateTest, ClientTest
- **Test Fixtures**: JSON response fixtures for testing
  - projects-list.json, all-projects-paginated.json, project-detail.json
  - tasks-list.json, all-tasks-paginated.json, task-detail.json
  - comments-list.json, users-list.json, work-reports-list.json
  - Error fixtures: error-not-found.json, error-validation.json, error-rate-limit.json
  - TestCase base class with fixture loading helpers
- **Integration Tests**: Integration test infrastructure
  - IntegrationTestCase base class with API credential handling
  - ProjectIntegrationTest, TaskIntegrationTest, TasklistIntegrationTest
  - Documentation for running integration tests
- **Example Files**: New usage examples
  - basic-usage.php - Demonstrates common SDK operations
  - pagination.php - Manual and automatic pagination, FilterBuilder
  - time-tracking.php - Time tracking and work reports
- **Documentation**: Updated README.md with comprehensive documentation
  - Accurate API examples for all resources
  - Pagination section with Paginator and FilterBuilder
  - Rate limiting and batch operations documentation
  - Complete resource reference table

#### Advanced Features (Phase 8)
- **Webhook Support**: Handle and verify webhook events from Freelo API
  - WebhookHandler class for signature verification and payload parsing
  - Event classes: TaskCreatedEvent, TaskUpdatedEvent, CommentAddedEvent, ProjectUpdatedEvent
  - PSR-7 compatible webhook handling
- **Rate Limiting**: Automatic rate limit detection and tracking
  - RateLimiter class to parse rate limit headers
  - Automatic rate limit status tracking from API responses
  - Rate limit threshold detection and delay calculation
- **Automatic Retry**: Exponential backoff retry for transient failures
  - RetryHandler class with configurable retry strategies
  - Automatic retry for rate limits, server errors, and network issues
  - Custom retry logic support with callbacks
  - Exponential backoff with jitter to avoid thundering herd
- **Batch Operations**: Execute multiple API operations efficiently
  - BatchRequest class for queuing multiple operations
  - Support for all HTTP methods (GET, POST, PUT, PATCH, DELETE)
  - Named operations with key-based result access
  - Error handling with continue-on-error or stop-on-error modes
  - Comprehensive batch result analysis and statistics

#### Documentation & Examples
- Updated advanced features documentation with webhook, rate limiting, and batch operations
- Added webhook handling example (examples/webhooks.php)
- Added batch operations example (examples/batch-operations.php)
- Added rate limiting example (examples/rate-limiting.php)
- Comprehensive test coverage for all advanced features

## [1.0.0] - 2025-12-28

### Added

#### Core Features
- Full Freelo API v1 support with all endpoints
- PSR-18 HTTP Client support for flexible HTTP client choice
- PSR-17 HTTP Factories support
- PSR-16 Simple Cache support for authentication token caching
- API key + email authentication
- Modern PHP 8.1+ with strict types and readonly properties

#### Resource Management
- Project management (CRUD operations, owned/invited/archived/templates)
- Task management (CRUD, status, priority, comments)
- To-do list management
- File upload and download
- Tag management
- Comment management
- Project workers retrieval

#### Developer Experience
- Comprehensive error handling with specific exception types
- Type-safe enums for currencies, priorities, and statuses
- Fluent, intuitive API design
- Custom HTTP client configuration support
- Response parsing and data models
- PHPStan level 8 compliance
- PSR-12 code style
- 80%+ test coverage with PHPUnit
- Complete PHPDoc documentation

#### Documentation
- Comprehensive README with quick start guide
- Installation guide with multiple HTTP client options
- Authentication guide with security best practices
- Detailed usage guides for all resources (projects, tasks, files, to-dos)
- Error handling guide with examples
- Advanced features guide (caching, custom clients, middleware)
- 9 runnable examples covering common use cases
- Contributing guidelines
- API documentation setup with phpDocumentor

#### Quality Assurance
- Unit tests with PHPUnit
- Integration test structure
- Static analysis with PHPStan (level 8)
- Code style checks with PHP_CodeSniffer (PSR-12)
- GitHub Actions CI/CD pipeline
- Code coverage reporting

[Unreleased]: https://github.com/freeloapp/php-sdk/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/freeloapp/php-sdk/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/freeloapp/php-sdk/releases/tag/v1.0.0
