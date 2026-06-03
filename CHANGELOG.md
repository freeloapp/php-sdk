# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- **`TasklistResource::edit()`** — new `POST /tasklist/{id}/edit` endpoint. Partial updates of name, budget, time fund, priority (positional ordering), followers and default worker. Returns the API's `priorityApplied` flag — `false` means the other fields committed but the priority renumber failed (best-effort semantics; retry the priority alone).
- **Taskcheck endpoints on `SubtaskResource`** — `updateTaskcheck()` (name/worker only), `deleteTaskcheck()`, `finishTaskcheck()`, `activateTaskcheck()` for simple checklist items via the new `/taskcheck/{taskcheck_id}/*` API endpoints. Smart subtask IDs return 404 on these — use the task endpoints for those.
- **`TaskLabelResource::findAvailable()`** — new `GET /task-labels/find-available` endpoint; returns all task labels usable by the authenticated user, sorted by name.
- **`User::$mentionKey`** — normalized fullname used as the visible text after `@` in comment mention spans; now returned by the API on `UserBasic` shapes (and surfaced in `/users/get-authenticated`).
- **`Task::$type`** — `task`/`subtask` discriminator newly exposed on task detail responses.
- **`Subtask::$type`** — `subtask` (smart subtask) vs `taskcheck` (simple checklist item) discriminator.
- **`SearchResult::isTaskcheck()`** — helper for the new `taskcheck` search hit type.
- **`TimeTrackingResource::edit()` accepts `$dateReported`** — rewrites the running session's start time (backdating a forgotten timer); `/timetracking/stop` computes the duration from it.
- **`my_priorities` filter** documented on `TaskResource::getAll()` — only tasks in the authenticated user's priorities.
- **`update-api-spec.yml` workflow** — weekly (Monday 06:00 UTC) and manually triggerable job that downloads the latest upstream OpenAPI spec (`make spec`), regenerates models + endpoints digest (`composer generate`), runs PHPStan/PHPCS/tests against the new client, and opens a `chore/update-api-spec` PR via `peter-evans/create-pull-request` if anything changed. Mirrors the equivalent automation in the Go SDK; `openapi-check.yml` remains for drift detection on every push/PR.

### Changed
- **Synced OpenAPI spec to upstream** — new endpoints above plus: `page` documented as alias of the `p` pagination param across list endpoints; boolean-ish query params (`no_due_date`, `finished_overdue`, `only_unread`, …) documented as strict `0`/`1` integers (string `true`/`false` silently falls back to default); fulltext search request documents `exclude_entity_types`, `is_subtask`, `sort` (`last_updated` `ASC`/`DESC`), `taskcheck` entity type and `lang` enum (`cs_cz`/`en_us`); several list endpoints now return `UserWithEmail` instead of `UserBasic`; `WorkReport.date_reported` documented as full datetime (start of work session) rather than date. Generated models and `docs/ENDPOINTS.md` regenerated (new `UserWithEmail` model).
- **`SearchResult::isComment()`** now also matches the granular comment hit types the search API actually returns (`task_comment`, `note_comment`, `file_comment`, `link_comment`).
- **`SearchResource::search()`** PHPDoc documents the full filter set (entity types, states, structural narrowing, due-date range, sort, lang, paging).

### Fixed
- **`ProjectLabelResource::findAvailable()`** — reads the `labels` response key (API renamed it from the erroneous singular `label`; the old key is still read as fallback).

## [2.0.0] - 2026-05-01

### Changed (BREAKING)
- **Datetime fields in models are now `?\DateTimeImmutable` (UTC)** — previously `?string` raw passthrough. Affects `Task`, `Subtask`, `Tasklist`, `Project`, `Comment`, `WorkReport`, `WorkReportExtended`, `Note`, `File`, `Event`, `Notification`, `IssuedInvoice`, `Report`, `CustomField`. Constructors require `\DateTimeImmutable` instead of strings.
  - **Why:** Freelo Public API V1 returns naive datetimes (no timezone) in `Europe/Prague` local time — now formally documented in the OpenAPI spec's "Timestamp Format" section. Treating them as RFC3339 would silently produce wrong moments. The SDK interprets V1 strings as Europe/Prague and exposes UTC `\DateTimeImmutable`.
  - **Migration:** Where you previously did `$task->dateAdd` and got `'2024-04-24T11:12:38'`, you now get a `\DateTimeImmutable` in UTC. To get the original string, use `$task->toArray()['date_add']` or call `->format('Y-m-d\TH:i:s')` after `->setTimezone(new \DateTimeZone('Europe/Prague'))` on the object.

### Added
- **`Freelo\Sdk\Internal\DateTimeParser`** — central parser for V1 API datetime strings. Handles naive `Y-m-d\TH:i:s` (Prague), RFC3339 with `Z`/offset, pure `Y-m-d`, and `\DateTimeImmutable` passthrough. Throws `Freelo\Sdk\Exception\InvalidDateTimeException` for malformed input.
- **`FilterBuilder` accepts `\DateTimeImmutable`** for `dueDateRange`, `createdInRange`, `finishedDateRange`, `dateReportedRange`, `dateAddRange`, `dateRange`, and `dateEditedFrom`. Strings still work — the new overload is purely additive ergonomics. Objects are formatted via `Europe/Prague` to match what the API expects.
- **OpenAPI generator** now maps `format: date-time` and `format: date` to `\DateTimeImmutable` and emits `DateTimeParser::parseDateTime()` calls in `fromArray()`. Generated models in `src/Generated/Model/` regenerated accordingly.

### Changed
- **Synced OpenAPI spec to upstream** — `.openapi/freelo-api.yaml` now includes the new "Timestamp Format" section in `info.description`, per-field timestamp descriptions and examples, and expanded task-relations endpoint documentation (plan-gating, multi-project child handling, 403 collapsed into 404). `docs/ENDPOINTS.md` regenerated with the additional context.

### Fixed
- **Test fixtures no longer use the bogus `Z` suffix** — fixtures previously had `'2024-01-01T00:00:00Z'` which never appears in real V1 responses. Updated to naive `'2024-01-01T00:00:00'` so tests verify behavior against the format the API actually returns.

### Notes
- `toArray()` continues to return the raw API response payload — datetime fields appear there as the original strings, not formatted from the parsed object. For typed access use the object properties.

## [1.3.0] - 2026-04-22

### Added
- **`make build` pipeline** — single command that downloads latest OpenAPI spec, regenerates models, runs static analysis and tests. New Makefile targets: `spec`, `generate`, `build` (chains `spec → generate → analyse → test`). Source URL overridable via `OPENAPI_URL`.
- **Schema descriptions in generated models** — class-level PHPDoc now carries the description from the OpenAPI schema (descended through `allOf`/`oneOf`/`anyOf` variants when necessary). Gives IDEs and LLMs immediate context, e.g. `TaskLabelAddInput` now documents its dual input modes.
- **Endpoints digest** — new `scripts/generate-endpoints-digest.php` emits `docs/ENDPOINTS.md` with every endpoint's summary, description, parameters, request body, and responses, grouped by tag. Surfaces `use cases`, `behavior notes`, and `side effects` from the upstream spec so LLMs can work with endpoints without parsing raw YAML. Regenerated alongside models on every `composer generate`.
- **Split composer generate scripts** — `composer generate:models` and `composer generate:endpoints` are runnable individually; `composer generate` and `composer generate:check` orchestrate both.

### Changed
- **Regenerated models from upstream spec** — added `TaskRelation` model, updated `TasklistDetail`.
- **`openapi-check.yml` CI** — now also watches `docs/ENDPOINTS.md` and `scripts/generate-endpoints-digest.php` for drift.

## [1.2.0] - 2026-04-10

### Added
- **oneOf/anyOf schema support** in OpenAPI model generator — merges variant properties into flat nullable models

### Changed
- **Updated OpenAPI spec** from upstream API (`TaskLabelInput` replaced by `TaskLabelAddInput` and `TaskLabelRemoveInput` with oneOf variants)
- **TaskLabelResource** PHPDoc updated to document all label input modes (UUID-only, name-based, name+color)

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

[Unreleased]: https://github.com/freeloapp/php-sdk/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/freeloapp/php-sdk/compare/v1.3.0...v2.0.0
[1.3.0]: https://github.com/freeloapp/php-sdk/compare/v1.2.0...v1.3.0
[1.2.0]: https://github.com/freeloapp/php-sdk/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/freeloapp/php-sdk/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/freeloapp/php-sdk/releases/tag/v1.0.0
