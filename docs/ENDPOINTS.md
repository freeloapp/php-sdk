<!-- @generated Auto-generated from OpenAPI spec — do not edit manually. -->
<!-- @see scripts/generate-endpoints-digest.php -->

# Freelo API Endpoints

**Freelo API — v1.0.0**

This digest exists to give LLMs and humans fast access to endpoint semantics (use cases, behavior notes, side effects) without parsing the full spec. Regenerated from `.openapi/freelo-api.yaml` on every `composer generate`.

Total endpoints: **108** across 19 tag(s).

## Table of contents

- [Comments](#comments) — 3 endpoints
- [Custom Fields](#custom-fields) — 14 endpoints
- [Events](#events) — 1 endpoint
- [Files](#files) — 3 endpoints
- [Invoicing](#invoicing) — 5 endpoints
- [Notes](#notes) — 4 endpoints
- [Notifications](#notifications) — 3 endpoints
- [Pinned Items](#pinned-items) — 3 endpoints
- [Project Labels](#project-labels) — 5 endpoints
- [Projects](#projects) — 15 endpoints
- [Search](#search) — 1 endpoint
- [States](#states) — 1 endpoint
- [Subtasks](#subtasks) — 2 endpoints
- [Task Labels](#task-labels) — 3 endpoints
- [Tasklists](#tasklists) — 5 endpoints
- [Tasks](#tasks) — 25 endpoints
- [Time Tracking](#time-tracking) — 4 endpoints
- [Users](#users) — 7 endpoints
- [Work Reports](#work-reports) — 4 endpoints

## Comments

### `GET /all-comments`

**Get all comments (paginated, filterable)**

`operationId`: `getAllComments`

Global comment feed across all accessible projects / tasks / files / docs / links. Supports filtering by `type` (task comments, document comments, etc.).

**Use cases:**
- Activity-feed widgets
- Auditing recent comments across a portfolio
- Extracting comment history for AI summarization

**Behavior notes:**
- Response is ACL-filtered — only comments on entities the caller can read are returned.
- Default order: `date_add desc` (newest first).
- `type=all` is the default and combines every comment category; narrow down as needed.

**Parameters:**

- `projects_ids[]` [query] (array<integer>)
- `type` [query] (string enum: all|task|document|file|link) — Comment type
- `order_by` [query] (string enum: date_add|date_edited_at)
- `order` [query] (string enum: asc|desc)
- `p` [query] (integer) — Page number (starting from 0)

**Responses:**

- `200` — Successful response

---

### `POST /comment/{comment_id}`

**Edit an existing comment**

`operationId`: `editComment`

Overwrites the text and / or attachments of an existing comment.

**Use cases:**
- Correcting typos in a posted comment
- Updating a status comment with new info

**Behavior notes:**
- `files` replaces the full attachment set — pass the complete list of file UUIDs you want attached, not a delta.
- ACL: only the comment's author can edit (or project owner / commander depending on role rules). Otherwise 404 `NotFoundException` is returned (not 403, to avoid leaking the existence of inaccessible comments).
- The method used is `POST` for historical reasons, not `PUT`/`PATCH`.

**Parameters:**

- `comment_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `content` **required** (string)
    - `files` (array<FileUpload>)

**Responses:**

- `200` — Comment updated _(schema: `Comment`)_

---

### `POST /task/{task_id}/comments`

**Add a comment to a task**

`operationId`: `createComment`

Posts a new comment on the given task. Text is passed as `content` (HTML / plain text); attachments are passed as `files` (referencing previously uploaded file UUIDs).

**Use cases:**
- Logging progress or questions on a task
- Posting automated status updates from integrations
- Attaching supporting files to a task body

**Behavior notes (non-obvious):**
- **If the task has no comments yet, this call creates the task's description instead of a regular comment** (the `ICommentIsDescriptionFiller` auto-flips `is_description=true` on the first comment). From the second comment onward this endpoint behaves like a normal comment.
- Subsequent calls are always regular comments; the description is managed separately via `/task/{id}/description`.
- Fires notifications to the task's tracking users and a `comment_created` event.

**Parameters:**

- `task_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `content` **required** (string)
    - `files` (array<FileUpload>)

**Responses:**

- `200` — Comment created _(schema: `Comment`)_

---

## Custom Fields

### `POST /custom-field-enum/change/{custom_field_enum_uuid}`

**Rename an enum option**

`operationId`: `editEnumOption`

Updates the display `value` (label) of an enum option. The option's UUID is preserved, so any existing task values referencing it continue to work.

**Use cases:**
- Fixing a typo in a dropdown value across all tasks using it
- Re-labeling a status category

**Behavior notes:**
- Only `value` is editable via this endpoint; the order and type are managed elsewhere.

**Parameters:**

- `custom_field_enum_uuid` [path, required] (string<uuid>)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `value` **required** (string)

**Responses:**

- `200` — Enum option updated

---

### `POST /custom-field-enum/create/{custom_field_uuid}`

**Add an option to an enum custom field**

`operationId`: `createEnumOption`

Creates a new enum option (dropdown value) on an enum-typed custom field.

**Use cases:**
- Extending a "Status" dropdown with a new option ("Blocked", "In review")
- Importing categorical data from an external system

**Behavior notes:**
- Caller-supplied `uuid` is respected if present; otherwise server-generated.
- Calling on a non-enum custom field produces a validation error.
- ACL: project commander.

**Parameters:**

- `custom_field_uuid` [path, required] (string<uuid>)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `uuid` (string<uuid>)
    - `value` **required** (string)

**Responses:**

- `200` — Enum option created

---

### `DELETE /custom-field-enum/delete/{custom_field_enum_uuid}`

**Delete an unused enum option (safe)**

`operationId`: `deleteEnumOption`

Removes an enum option **only if no tasks currently reference it**.

**Use cases:**
- Safe cleanup of never-used options
- Quality gating: if this succeeds, no data loss happened

**Behavior notes (non-obvious):**
- If the option is in use by any task value, the delete is **refused** with a `UserVisibleErrorMessageException`. To delete anyway (and null out the referencing values), use `/custom-field-enum/force-delete/{id}`.
- Even non-used soft-deleted references may block deletion — inspect carefully.

**Parameters:**

- `custom_field_enum_uuid` [path, required] (string<uuid>)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `DELETE /custom-field-enum/force-delete/{custom_field_enum_uuid}`

**Force-delete enum option (destructive)**

`operationId`: `forceDeleteEnumOption`

Deletes the enum option even if it is currently used by task values. Referencing task values are cleared.

**Use cases:**
- Cleanup after a renamed / merged option that can't be resolved with a regular delete
- Workspace housekeeping when preserving data in tasks is not required

**Behavior notes (non-obvious):**
- **Destructive:** any task value that referenced this option is cleared. The `custom_field_value_history` row is kept for audit, but the current value becomes null/empty.
- There is no undo. Prefer `/custom-field-enum/delete` when in doubt.
- ACL: project commander.

**Parameters:**

- `custom_field_enum_uuid` [path, required] (string<uuid>)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `GET /custom-field-enum/get-for-custom-field/{custom_field_uuid}`

**List enum options of a custom field**

`operationId`: `getEnumOptionsForCustomField`

Returns the list of enum options (dropdown values) defined for an enum-typed custom field. Use the returned `uuid`s as `value` in `/custom-field/add-or-edit-enum-value`.

**Use cases:**
- Populating a dropdown picker in a UI
- Resolving display labels to option UUIDs when importing

**Behavior notes:**
- Only non-deleted options are returned; soft-deleted options are filtered out.

**Parameters:**

- `custom_field_uuid` [path, required] (string<uuid>)

**Responses:**

- `200` — Successful response

---

### `POST /custom-field/add-or-edit-enum-value`

**Upsert enum custom-field value on a task**

`operationId`: `addOrEditEnumValue`

Sets the value of an **enum-typed** custom field on a task by referencing one of the field's predefined enum options (by UUID). Upsert semantics — creates or updates based on the (`task_id`, `customFieldUuid`) pair.

**Use cases:**
- Selecting a dropdown / status value on a task programmatically
- Bulk-updating categorical fields during data imports

**Behavior notes (non-obvious):**
- `value` is the **UUID of an enum option** (fetched from `/custom-field-enum/get-for-custom-field/{uuid}`), **not** the display string.
- Field name casing mismatch: body uses camelCase `customFieldUuid` (unlike the scalar endpoint which uses snake_case `custom_field_uuid`). Matches the server's internal data key.
- Same cross-project rule applies: custom field and task must be in the same project; otherwise 409.
- If the enum option UUID does not exist or belongs to a different custom field, 404 with "Enum was not found.".
- Response uses the camelCase key `customFieldEnum` for the value wrapper.

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `customFieldUuid` **required** (string<uuid>)
    - `task_id` **required** (integer)
    - `value` **required** (string<uuid>) — UUID of enum option

**Responses:**

- `200` — Enum value set

---

### `POST /custom-field/add-or-edit-value`

**Upsert scalar custom-field value on a task**

`operationId`: `addOrEditCustomFieldValue`

Sets the value of a **non-enum** (text / number) custom field on a task. Upsert semantics — if the task already has a value for this `custom_field_uuid`, it is updated; otherwise a new value is created.

**Use cases:**
- Syncing structured metadata from an external system into Freelo tasks
- Allowing a user to set / change a custom field value without knowing whether one already exists

**Behavior notes (non-obvious):**
- Matching key for upsert is the **pair (`task_id`, `custom_field_uuid`)**, not a UUID. Callers cannot specify the resulting value's UUID — it is generated server-side on create and preserved on update.
- The task and the custom field must belong to the **same project** — otherwise HTTP 409 Conflict with `"Custom field is in the different project than the task."`.
- For enum-typed custom fields, use `/custom-field/add-or-edit-enum-value` instead. Using this endpoint on an enum field produces unexpected `checker` validation errors.
- Writes a custom-field-value-history row on every call (even no-op updates). Use sparingly for heavy polling loads.
- Response is wrapped: `{ "custom_field_value": { ... } }`.

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `custom_field_uuid` **required** (string<uuid>)
    - `task_id` **required** (integer)
    - `value` **required** (string)

**Responses:**

- `200` — Value set

---

### `POST /custom-field/create/{project_id}`

**Define a custom field on a project**

`operationId`: `createCustomField`

Creates a custom-field definition (column) on the specified project. Tasks in this project then expose this field in their custom field values.

**Use cases:**
- Adding "Estimated points", "Client ID", "Severity" fields to projects
- Structured metadata for reporting

**Behavior notes (non-obvious):**
- Caller must be a **project commander** of the target project. Otherwise `UserIsNotProjectCommander` → 403.
- `type` is a **UUID referencing a predefined type** from `GET /custom-field/get-types` (text / number / enum). Invalid UUIDs → 404.
- If `uuid` is provided in the body it is honored (useful for reproducible provisioning); otherwise the server generates one.
- Plan limits apply — creating a field beyond the account's allowance throws `PlanExceededException` (typically 402/429).

**Parameters:**

- `project_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `uuid` (string<uuid>)
    - `name` **required** (string)
    - `type` **required** (string<uuid>) — UUID of custom field type: - `2f7bfe3a-c950-470e-b910-95b4caf5dc4f` - text - `b1e56fa9-a97a-429b-8ab4-82bebe58933a` - number - `f9729a8f-d340-40e4-b2c0-dc46c37e18ce` - enum

**Responses:**

- `200` — Custom field created

---

### `DELETE /custom-field/delete-value/{uuid}`

**Remove a custom-field value from a task**

`operationId`: `deleteCustomFieldValue`

Deletes the specific custom-field value by its UUID. The field definition itself and other tasks' values are unaffected.

**Use cases:**
- Clearing a field on a task (differentiating "no value" from "empty string")

**Behavior notes:**
- A history row is written capturing the previous value.
- 404 if the value UUID doesn't exist or belongs to a deleted custom field.

**Parameters:**

- `uuid` [path, required] (string<uuid>)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `DELETE /custom-field/delete/{uuid}`

**Soft-delete custom field**

`operationId`: `deleteCustomField`

Marks a custom-field definition as deleted. Existing task values of this field are preserved but hidden; can be restored via `/custom-field/restore/{uuid}`.

**Use cases:**
- Retiring an obsolete field without losing historical data

**Behavior notes:**
- Soft-delete; use `/restore` to undo.
- ACL: requires project commander of the field's project.

**Parameters:**

- `uuid` [path, required] (string<uuid>)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `GET /custom-field/find-by-project/{project_id}`

**List custom fields of a project**

`operationId`: `findCustomFieldsByProject`

Returns all custom-field definitions configured on a project, plus a boolean indicating whether the caller is the project's commander (relevant for the admin-level mutation endpoints in this tag).

**Use cases:**
- Rendering the custom-field columns on a task board
- Deciding in UI whether to show "Create custom field" button (based on `is_commander`)

**Behavior notes:**
- Soft-deleted custom fields are excluded.
- Includes enum fields with their options embedded.

**Parameters:**

- `project_id` [path, required] (integer)

**Responses:**

- `200` — Successful response

---

### `GET /custom-field/get-types`

**List supported custom-field types**

`operationId`: `getCustomFieldTypes`

Returns the catalog of custom-field type definitions (e.g. text, number, enum) available for use in `POST /custom-field/create/{project_id}`. Response includes the UUID of each type — that's the value you pass in `type` when creating a custom field.

**Use cases:**
- Populating a type picker in a custom-field creation UI
- Validating `type` UUIDs before sending a create request

**Responses:**

- `200` — Successful response

---

### `POST /custom-field/rename/{uuid}`

**Rename a custom field**

`operationId`: `renameCustomField`

Changes the display name of an existing custom field.

**Behavior notes:**
- ACL: caller must be project commander of the field's project; otherwise 403.
- The field's UUID / type are immutable via this endpoint; only `name` changes.

**Parameters:**

- `uuid` [path, required] (string<uuid>)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `name` **required** (string)

**Responses:**

- `200` — Custom field renamed

---

### `POST /custom-field/restore/{uuid}`

**Restore a soft-deleted custom field**

`operationId`: `restoreCustomField`

Reverses a prior `/custom-field/delete`. Previously preserved values become visible again.

**Behavior notes:**
- 404 if the custom field doesn't exist or was never soft-deleted.
- ACL: requires project commander.

**Parameters:**

- `uuid` [path, required] (string<uuid>)

**Responses:**

- `200` — Custom field restored

---

## Events

### `GET /events`

**Get activity events (audit log)**

`operationId`: `getAllEvents`

Paginated activity feed across projects, tasks, users, and event types the caller has access to. This is effectively the audit / history log.

**Use cases:**
- Activity timeline UI
- Webhook-like polling for "what changed since last sync"
- Reconstructing a project's history

**Behavior notes (non-obvious):**
- The caller's accessible `projects_ids`, `users_ids`, and allowed event types are **injected implicitly** into the filter as a safety net. Requests filtered for projects / users / types the caller can't see are silently constrained; you won't see data you shouldn't, even if you pass the IDs explicitly.
- Use `/events-types` (see `Events:findTypes` in the router) to discover valid `events_types[]` values.
- Default order: `date desc` (newest first). No sort-by-type support.

**Parameters:**

- `projects_ids[]` [query] (array<integer>)
- `users_ids[]` [query] (array<integer>)
- `events_types[]` [query] (array<string>)
- `order` [query] (string enum: asc|desc)
- `date_range[date_from]` [query] (string<date>)
- `date_range[date_to]` [query] (string<date>)
- `tasks_ids[]` [query] (array<integer>)
- `p` [query] (integer) — Page number (starting from 0)

**Responses:**

- `200` — Successful response

---

## Files

### `GET /all-docs-and-files`

**List docs, files, links, and directories (paginated)**

`operationId`: `getAllDocsAndFiles`

Global listing of **all four types of project assets** — directories, links, files, and documents — across accessible projects. Use `type` to narrow to a single category.

**Use cases:**
- Building a cross-project "library" search
- Export / backup of non-task assets

**Behavior notes:**
- ACL-filtered by the caller's visible projects.
- Without `projects_ids[]`, spans every project the caller can see.

**Parameters:**

- `projects_ids[]` [query] (array<integer>)
- `type` [query] (string enum: directory|link|file|document)
- `p` [query] (integer) — Page number (starting from 0)

**Responses:**

- `200` — Successful response

---

### `POST /file/upload`

**Upload a file (multipart)**

`operationId`: `uploadFile`

Uploads a single file (**max 100 MB**). Returns a UUID that can be referenced from other endpoints.

**Use cases:**
- Attaching an image to a comment: embed `<a data-freelo-uuid="{uuid}">caption</a>` in the comment content
- Attaching files to a task description via `POST /task/{id}/description`
- Pinning files to a project

**Behavior notes:**
- The upload does **not** automatically attach the file anywhere — it produces a UUID you then reference.
- `multipart/form-data` is mandatory; JSON body is rejected.
- Size / content-type checks come from `FileUploadChecker`; oversize or forbidden types return 400.

**Request body:**

_Request body (required)_

- Content-Type: `multipart/form-data`
- Schema: `object`
- Properties:
    - `file` **required** (string<binary>)

**Responses:**

- `200` — File uploaded

---

### `GET /file/{file_uuid}`

**Download a file by UUID**

`operationId`: `downloadFile`

Streams the raw file content (any MIME type) identified by UUID. ACL-checked: the caller must have access to a project the file belongs to.

**Use cases:**
- Rendering an attachment preview in a client
- Proxying file downloads through an integration

**Behavior notes:**
- Content-Type is derived from the stored MIME type. Content-Disposition header carries the original filename.
- Returns 404 if the file does not exist, was deleted, or the caller has no access to any project it is attached to.

**Parameters:**

- `file_uuid` [path, required] (string<uuid>)

**Responses:**

- `200` — File content

---

## Invoicing

### `GET /issued-invoice/{invoice_id}`

**Get an issued invoice detail**

`operationId`: `getIssuedInvoiceDetail`

Full detail of a single issued invoice — total amount, currency, period, linked project, and metadata.

**Use cases:**
- Opening an invoice in the billing UI
- Fetching metadata to pre-fill an external accounting entry

**Parameters:**

- `invoice_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `IssuedInvoiceDetail`)_

---

### `POST /issued-invoice/{invoice_id}/mark-as-invoiced`

**Mark invoice as actually invoiced (via external app)**

`operationId`: `markAsInvoiced`

Records that an external invoicing tool (Fakturoid, iDoklad, etc.) has issued the real invoice. Associates the invoice with the external URL + subject and freezes the underlying work reports so they can't be edited or re-billed.

**Use cases:**
- After creating an invoice in an external accounting tool, close the loop in Freelo
- Prevent accidental re-invoicing of already billed work

**Behavior notes (non-obvious):**
- This action is **not reversible** via API. Once marked, the underlying work reports are effectively frozen — edit / delete calls on those reports may be refused.
- `url` is stored verbatim; it should point to the external invoice detail.
- `subject` is the display title shown in Freelo's billing UI.

**Parameters:**

- `invoice_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `url` **required** (string<uri>)
    - `subject` **required** (string)

**Responses:**

- `200` — Invoice marked _(schema: `IssuedInvoiceDetail`)_

---

### `GET /issued-invoice/{invoice_id}/reports`

**Download invoice's work reports as CSV**

`operationId`: `downloadIssuedInvoiceReports`

Streams a **CSV download** of all work reports included in the invoice. Content-Type is `text/csv`.

**Use cases:**
- Attaching the work-report backup to a physical invoice
- Manual review before marking invoiced

**Behavior notes:**
- This is a file download — the body is not JSON. Response framework sets a Content-Disposition header with a server-chosen filename.
- For programmatic access prefer `/reports-json` which returns structured data.

**Parameters:**

- `invoice_id` [path, required] (integer)

**Responses:**

- `200` — CSV file

---

### `GET /issued-invoice/{invoice_id}/reports-json`

**Get invoice's work reports as JSON**

`operationId`: `getIssuedInvoiceReportsJson`

Returns the work reports composing the invoice as a JSON array — programmatic equivalent of the `/reports` CSV endpoint.

**Use cases:**
- Feeding invoice data into an external accounting or BI system
- Rendering a detail breakdown in a custom UI

**Parameters:**

- `invoice_id` [path, required] (integer)

**Responses:**

- `200` — JSON array of work report rows _(schema: `array<WorkReportExtended>`)_

---

### `GET /issued-invoices`

**List issued invoices (paginated)**

`operationId`: `getIssuedInvoices`

Returns a paginated list of invoice draft groups ("issued invoices") — each representing a set of work reports grouped for billing on a project in a date range.

**Use cases:**
- Displaying an invoicing queue in a client-facing UI
- Exporting unbilled-but-ready batches to an external accounting tool

**Behavior notes:**
- Scope: only invoices from projects the caller has billing access to.
- Filtering by `date_range` narrows to invoices whose reporting period overlaps the range (not by `date_issued`).

**Parameters:**

- `date_range[date_from]` [query] (string<date>)
- `date_range[date_to]` [query] (string<date>)
- `projects_ids[]` [query] (array<integer>)
- `p` [query] (integer) — Page number (starting from 0)

**Responses:**

- `200` — Successful response

---

## Notes

### `DELETE /note/{note_id}`

**Delete a note**

`operationId`: `deleteNote`

Soft-deletes the note. It is hidden from listings but retained in the database for audit.

**Behavior notes:**
- Response returns the (now-deleted) note's state for confirmation. This is a quirk — most delete endpoints return a SuccessResponse; this one returns the Note.

**Parameters:**

- `note_id` [path, required] (integer)

**Responses:**

- `200` — Note deleted _(schema: `Note`)_

---

### `GET /note/{note_id}`

**Get a note detail**

`operationId`: `getNote`

Fetches a single note by ID. As with create, notes are backed by the Document entity.

**Behavior notes:**
- ACL-checked via the note's project.

**Parameters:**

- `note_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `Note`)_

---

### `POST /note/{note_id}`

**Edit a note**

`operationId`: `editNote`

Updates an existing note's title (`name`) and / or body (`content`).

**Behavior notes:**
- Overwrites content — there is no history / diff tracking exposed via the API.

**Parameters:**

- `note_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `name` **required** (string)
    - `content` (string)

**Responses:**

- `200` — Note updated _(schema: `Note`)_

---

### `POST /project/{project_id}/note`

**Create a note in a project**

`operationId`: `createNote`

Creates a project-level **note** (a Document entity internally — notes and documents share the same storage and presenter). Notes are rich-text blocks attached to the project rather than to a task.

**Use cases:**
- Capturing meeting minutes on a project
- Storing shared reference docs without needing a separate doc tool

**Behavior notes (non-obvious):**
- Internally handled by `DocumentPresenter` — the `/note/*` paths are aliases of `/document/*`. The response shape is a Document. `name` maps to the note title, `content` to the body.

**Parameters:**

- `project_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `name` **required** (string)
    - `content` (string)

**Responses:**

- `200` — Note created _(schema: `Note`)_

---

## Notifications

### `GET /all-notifications`

**Get my notifications (paginated, filterable)**

`operationId`: `getAllNotifications`

Paginated list of notifications addressed to the authenticated caller.

**Use cases:**
- Rendering the notifications dropdown
- Digest / email summaries
- Integrations that mirror Freelo activity into Slack / Teams

**Behavior notes:**
- Notifications are **always scoped to the caller** — there is no way to read another user's notifications through this endpoint.
- `users_ids[]` filters by **authors** of the notification-triggering events, not recipients.
- `teams_uuids[]` filters by the team context of the notification (e.g. all notifications from Team X).
- `only_unread=true` is useful for badge counters.
- Default order: `date_add desc` (newest first).

**Parameters:**

- `projects_ids[]` [query] (array<integer>)
- `users_ids[]` [query] (array<integer>) — Authors of notifications
- `teams_uuids[]` [query] (array<string<uuid>>)
- `order` [query] (string enum: asc|desc)
- `notification_types[]` [query] (array<string>)
- `only_unread` [query] (boolean)
- `p` [query] (integer) — Page number (starting from 0)

**Responses:**

- `200` — Successful response

---

### `POST /notification/{notification_id}/mark-as-read`

**Mark a notification as read**

`operationId`: `markNotificationAsRead`

Marks a single notification (addressed to the caller) as read.

**Use cases:**
- Acknowledging an item from the bell dropdown
- Auto-read flow in an integration when a user interacts with the linked entity

**Behavior notes:**
- Idempotent — calling on an already-read notification returns 200.
- 404 if the notification does not exist **or** does not belong to the caller.

**Parameters:**

- `notification_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `POST /notification/{notification_id}/mark-as-unread`

**Mark a notification as unread**

`operationId`: `markNotificationAsUnread`

Reverts a previous "read" on a notification — re-surfaces it in the unread feed.

**Use cases:**
- Restoring a notification the user wants to revisit
- Bulk "unread" automation after a snooze

**Behavior notes:**
- Idempotent; 404 if notification does not belong to caller.

**Parameters:**

- `notification_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

## Pinned Items

### `DELETE /pinned-item/{pinned_item_id}`

**Delete pinned item**

`operationId`: `deletePinnedItem`

Removes a single pinned item from its project. The underlying target (task / document / file / link) is **not** affected — only the pin is deleted.

**Use cases:**
- Un-pinning outdated references
- Cleanup flows after a resource was moved / renamed

**Behavior notes:**
- ACL: the caller must have rights to modify pinned items in the owning project (usually worker+).
- Returns 404 if the pinned item does not exist or the caller has no access to its project.

**Parameters:**

- `pinned_item_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `GET /project/{project_id}/pinned-items`

**Get pinned items of project**

`operationId`: `getPinnedItems`

Returns all pinned items (pinned links, tasks, documents, files, project-links, directories) attached to the given project, filtered by the caller's ACL.

**Use cases:**
- Rendering the "pinned" sidebar of a project detail page
- Exporting quick-access resources for reporting / documentation

**Behavior notes:**
- The result is an ACL-filtered list — pinned items whose target (a file, a task, a document) the caller cannot see are omitted silently.
- The response is a flat array, not paginated.

**Parameters:**

- `project_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `array<PinnedItem>`)_

---

### `POST /project/{project_id}/pinned-items`

**Pin an external link to project**

`operationId`: `pinItemToProject`

Pins a user-supplied external URL to the project. The returned item is either the newly created pin or — if an equivalent internal pin (same task / document / file / project-link / directory) already exists — the pre-existing one.

**Use cases:**
- Quick-attach of reference material (spec doc, drive link) to a project
- Programmatic pinning from integrations (e.g. Slack → Freelo bot)

**Behavior notes (non-obvious):**
- The endpoint accepts an **external link** (schema shown here), but in the internal code path it is a dispatcher: if the URL is recognized as an internal Freelo resource (task, document, file, project-link, project-directory), the PinnedItemCreator performs a **fetch-or-create** — returning the existing internal pin if one already exists for that target, instead of creating a duplicate. So a `POST` with the same internal resource is idempotent.
- For purely external URLs, each POST creates a new row even if the same URL was pinned before.
- `title` is optional; when omitted the server derives a display name from the link target.

**Parameters:**

- `project_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `link` **required** (string<uri>) — Full URL to pin. If the URL matches an internal Freelo resource, the endpoint is idempotent (see description).
    - `title` (string) — Optional display label. If omitted, a default is derived from the target.

**Responses:**

- `200` — Item pinned _(schema: `PinnedItem`)_

---

## Project Labels

### `POST /project-labels/add-to-project/{projectId}`

**Attach label to project (fetch-or-create)**

`operationId`: `addProjectLabelToProject`

Attaches a project label to the given project. The request body selects the label in **one of two mutually exclusive modes**:

1. **By ID** — pass `id` of an existing label. In this mode `name`, `color` and `is_private` are **ignored** (even if sent).
2. **By data** — pass `name` + `is_private` (and optionally `color`). The server looks up an existing label owned by the correct user with the same data and re-uses it; if none exists, it **creates a new label** and attaches it.

> Non-standard behavior: the presence of `id` completely overrides the other fields. Sending `{id: 123, name: "typo", color: "#ff0000"}` will attach label 123 and silently ignore the name/color. If you want a new label with a specific name, omit `id`.

**Use cases:**
- Organizing projects by client / status / priority using a shared label
- Bulk-tagging projects during an import

**Behavior notes:**
- Attaching a label that is already on the project swallows the `UniqueConstraintViolationException` and returns 200 (idempotent).
- ACL: private labels can only be attached by their owner; public labels require the caller to be a project manager of the target project.

**Parameters:**

- `projectId` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `name` (string) — Label name. Used only when `id` is omitted.
    - `color` (string) — Hex color or named color from the server enum. Used only when `id` is omitted.
    - `is_private` (boolean) — Whether the label is private to its owner. Used only when `id` is omitted. Required in data mode.
    - `id` (integer) — ID of an existing label. When present, `name`/`color`/`is_private` are ignored.

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `GET /project-labels/find-available`

**Get project labels usable by caller**

`operationId`: `findAvailableProjectLabels`

Returns all project labels (tags) the authenticated user can assign — their own private labels plus public labels from projects they participate in.

**Use cases:**
- Populating a label picker before calling `/project-labels/add-to-project/{projectId}`
- Showing the caller which labels already exist so they don't create a duplicate

**Behavior notes:**
- Response field is `label` (singular) — it is an array. The tag's `tag` entity property is exposed as `name` in API shape (the TagNameKeyReplacer maps it).

**Responses:**

- `200` — Successful response

---

### `POST /project-labels/remove-from-project/{projectId}`

**Detach label from project**

`operationId`: `removeProjectLabelFromProject`

Detaches a label from a single project. The label itself continues to exist and remains attached to other projects.

**Use cases:**
- Re-categorizing a project without deleting the label from the workspace
- Bulk cleanup of mis-applied labels

**Behavior notes (non-obvious):**
- Request body follows the same two-mode rule as `/add-to-project`: if `id` is present, `name`/`color`/`is_private` are **ignored**; otherwise the server looks the label up by data (name + is_private + owner).
- In data mode (no `id`), the label is matched by owner + data; in ID mode, any referenced label is targeted.
- If the label is not attached to the project, `ITagForRemoveFromProjectFetcher` throws `NotFoundException` → HTTP 404.

**Parameters:**

- `projectId` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `name` (string)
    - `color` (string)
    - `is_private` (boolean)
    - `id` (integer) — ID of the label to detach. When present, `name`/`color`/`is_private` are ignored.

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `DELETE /project-labels/{labelId}`

**Delete project label (global)**

`operationId`: `deleteProjectLabel`

Removes the label entirely — it is detached from all projects it was attached to and the tag entity itself is deleted.

**Use cases:**
- Cleaning up unused / obsolete labels
- Consolidating duplicates (delete one, re-attach projects to the other)

**Behavior notes:**
- This is a **hard delete of the global label**, not a "detach from one project". To just unlink a label from a single project use `POST /project-labels/remove-from-project/{projectId}`.
- ACL applies: only the owner of a private label (or a user passing `IProjectTagAclChecker`) can delete it.

**Parameters:**

- `labelId` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `POST /project-labels/{labelId}`

**Edit project label**

`operationId`: `editProjectLabel`

Updates an existing project label identified by path parameter `labelId`. Renames it, changes its color, and/or toggles private/public visibility.

**Use cases:**
- Recoloring / renaming a label across all projects that use it
- Flipping a label from private (owner-only) to public once a team is ready to share it

**Behavior notes:**
- The label is global — the edit propagates to **every project** where the label is attached.
- ACL: only the label's owner (or a user passing the `IProjectTagAclChecker`) may edit private labels. Editing public labels requires the relevant project-manager permission.
- All fields in the body are optional; omit a field to leave it unchanged.

**Parameters:**

- `labelId` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `name` (string)
    - `color` (string)
    - `is_private` (boolean)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

## Projects

### `GET /all-projects`

**Get all accessible projects (owned + invited)**

`operationId`: `getAllProjects`

Paginated collection of all projects the authenticated user can see — both owned and those they were invited to, filtered by state and tag.

**Use cases:**
- Primary project list for end-user UI where both owned and invited projects are shown
- Backfills / syncing to external systems that need every project regardless of ownership
- Narrow search by state (active / archived / template) combined with tag and owner filters

**Behavior notes:**
- Pagination is **required** for large accounts — use `p` parameter; page size is fixed server-side.
- `states_ids[]` accepts any combination of `1=active`, `2=archived`, `3=template`. When omitted, the server applies a default (typically active only) — always pass it explicitly if you need archived/templates.
- `tags[]` matches any of the specified tags; pass the literal string `"without"` to get projects **without** any tag (this is a magic value, not a real tag name).
- `users_ids[]` filters by project **owner** only, not by workers.
- Default ordering is `date_add asc`.

**Parameters:**

- `order_by` [query] (string enum: name|date_add|date_edited_at)
- `order` [query] (string enum: asc|desc)
- `tags[]` [query] (array<string>) — Filter by tags. Use "without" to get projects without tags.
- `states_ids[]` [query] (array<integer enum: 1|2|3>) — Project states: 1=active, 2=archived, 3=template
- `users_ids[]` [query] (array<integer>) — Filter by project owner IDs
- `created_in_range[date_from]` [query] (string<date>)
- `created_in_range[date_to]` [query] (string<date>)
- `p` [query] (integer) — Page number (starting from 0)

**Responses:**

- `200` — Successful response

---

### `GET /archived-projects`

**Get archived projects**

`operationId`: `getArchivedProjects`

Paginated list of projects the authenticated user can see that are in the **archived** state.

**Use cases:**
- Archive browser UI (read-only view of finished work)
- Exporting historical project data for reporting / accounting
- Restoring an archived project — find its ID here, then call `POST /project/{id}/activate`

**Behavior notes:**
- Includes both owned and invited archived projects.
- Each project comes with its tasklists embedded (active and archived tasklists alike — the project's archived state does not restrict the embedded tasklists).
- Only pagination parameters are accepted.

**Parameters:**

- `p` [query] (integer) — Page number (starting from 0)

**Responses:**

- `200` — Successful response

---

### `GET /invited-projects`

**Get projects where I am a worker (not owner)**

`operationId`: `getInvitedProjects`

Paginated list of active projects where the authenticated user is a worker — i.e. they were **invited** to the project, not its owner. Each project includes its active tasklists.

**Use cases:**
- "Projects shared with me" view, separating them from my own projects
- Freelancer dashboards that show only client work
- Cross-account collaboration views

**Behavior notes:**
- Returns only **active** projects. Archived invited projects do not appear here.
- Ordering and filtering parameters are **not** accepted — only pagination.

**Parameters:**

- `p` [query] (integer) — Page number (starting from 0)

**Responses:**

- `200` — Successful response

---

### `POST /project/create-from-template/{template_id}`

**Create project from a template**

`operationId`: `createProjectFromTemplate`

Clones a project template (state_id=3) into a brand-new active project, copying its tasklists, tasks, subtasks, and optionally shifting floating due-dates based on `preset_date_from`.

**Use cases:**
- Spinning up a standardized client-onboarding project
- Mass-provisioning projects from a shared blueprint
- Kick-starting recurring engagements

**Behavior notes (non-obvious):**
- `currency_iso` is optional — if omitted, the server derives it from the caller's locale (CZ → CZK, EN → USD, etc.) via `LanguageCurrencyMapper`. Pass it explicitly if you need a predictable result.
- `project_owner_id` defaults to the authenticated caller if not provided. The user must be owner-eligible; otherwise `400 InvalidArgumentException`.
- `preset_date_from` shifts any "relative" due dates defined in the template (e.g. "+3 days") to absolute dates anchored at this value.
- `users_ids` is a list of users **from the template's member list** you want to carry over as invitees; it is validated against the template's membership, not arbitrary user IDs.
- `name` defaults to the template's name (often with a suffix applied in front-end flows) — pass it to override.

**Parameters:**

- `template_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `name` (string)
    - `project_owner_id` (integer)
    - `currency_iso` (string enum: CZK|EUR|USD)
    - `preset_date_from` (string<date>) — Date to set floating due dates in templates
    - `general_settings` (object)
    - `users_ids` (array<integer>) — Users from template to invite

**Responses:**

- `200` — Project created from template

---

### `DELETE /project/{project_id}`

**Delete project (soft-delete)**

`operationId`: `deleteProject`

Marks the project as deleted. The project disappears from all listings, but is retained in the database.

**Use cases:**
- Removing a test / mistakenly-created project
- Permanent cleanup when archiving is not enough (e.g. client contract terminated)

**Behavior notes:**
- This is a **soft-delete** — the project row stays, but `deletedAt` is set. `POST /project/{id}/activate` restores it (the activate endpoint un-archives and un-deletes).
- Side effects: cascades to tasks/tasklists that inherit the deletion; running timetrackings may be stopped server-side; webhooks fire.
- Requires the caller to have project delete permissions (usually owner / commander).

**Parameters:**

- `project_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `GET /project/{project_id}`

**Get project detail**

`operationId`: `getProject`

Returns full detail of a single project — metadata, tasklists (filtered by caller's ACL), date-edited timestamp, the caller's hourly rate on the project, and current budget / spent totals.

**Use cases:**
- Opening the project detail page in a UI
- Pulling budget vs. spent numbers for reporting
- Retrieving the list of tasklists the caller can see before drilling into tasks

**Behavior notes:**
- Embedded tasklists respect the caller's ACL (tasklists the caller is not authorized for are filtered out).
- Budget / spent numbers are computed on the fly for the **calling** user's view — different users may see different totals depending on their hourly rates and worker relationships.
- Works for any project state (active, archived, template) as long as the caller has access.

**Parameters:**

- `project_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `ProjectDetail`)_

---

### `POST /project/{project_id}/activate`

**Activate (unarchive / undelete) project**

`operationId`: `activateProject`

Restores a project into the **active** state. Works as a single entry point for both "unarchive" and "undelete".

**Use cases:**
- Re-opening an archived project for follow-up work
- Recovering a project that was soft-deleted by mistake

**Behavior notes (non-obvious):**
- The endpoint inspects the project's current state and performs the appropriate transition: if archived → unarchive; if deleted → undelete; otherwise no-op returning 200.
- Can fail with `PlanExceededException` — restoring a project counts against the caller's plan limits and may be refused if the plan is already at its project cap.
- Requires project-admin permission.

**Parameters:**

- `project_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `POST /project/{project_id}/archive`

**Archive project**

`operationId`: `archiveProject`

Moves an active project into the **archived** state (state_id=2). Archived projects are hidden from the default lists but are still readable and can be reactivated via `POST /project/{id}/activate`.

**Use cases:**
- Completing a client engagement and removing it from active views
- Freezing a project at year-end without losing data

**Behavior notes:**
- No request body is expected.
- Archiving is idempotent: calling it on an already archived project succeeds (200) without side effects.
- Archiving **does not** stop running timetrackings automatically — check timetracking state separately if needed.
- Requires project-admin level permission (owner / commander).

**Parameters:**

- `project_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `POST /project/{project_id}/remove-workers/by-emails`

**Remove workers from project by email**

`operationId`: `removeProjectWorkersByEmails`

Same behavior as `/remove-workers/by-ids`, but the caller references workers by their email instead of user ID. Useful when IDs are not available (e.g. integration receives emails from CRM).

**Use cases:**
- Removing invited externals by their email address
- Integrations that sync membership with email-based identity sources

**Behavior notes:**
- Every email **must** belong to a user currently in the project — otherwise the request fails (pre-check via `IProjectWorkersByEmailChecker`). No partial success.
- Emails are resolved to user IDs server-side and then the ID-based ACL check is run. Same "owner cannot be removed" rule applies.

**Parameters:**

- `project_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `users_emails` **required** (array<string<email>>)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `POST /project/{project_id}/remove-workers/by-ids`

**Remove workers from project by user IDs**

`operationId`: `removeProjectWorkersByIds`

Removes one or more users (by their internal user IDs) from the project's worker list.

**Use cases:**
- Off-boarding teammates from a specific project
- Cleaning up external collaborators after an engagement ends
- Batch deprovisioning as part of automated HR flows

**Behavior notes:**
- All given IDs are checked at once by the remove-workers ACL checker — if the caller lacks rights to remove any single user, the whole request fails (no partial removal).
- The project **owner** cannot be removed via this endpoint; attempting to do so results in an error.
- Removing a user also cleans up their task assignments and ACL tasklists in this project.

**Parameters:**

- `project_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `users_ids` **required** (array<integer>)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `GET /project/{project_id}/workers`

**Get project workers**

`operationId`: `getProjectWorkers`

Paginated list of all users (workers + owner + guests) assigned to the given project.

**Use cases:**
- Worker picker when assigning tasks / time estimates / work reports
- Validating that an email is already a member before calling `POST /users/manage-workers`
- Audit / export of project membership

**Behavior notes:**
- Returns basic user data only — it is **not** filtered by ACL-tasklist membership (a worker assigned only to some tasklists still appears in the full list).
- Deleted (former) workers do not appear.

**Parameters:**

- `project_id` [path, required] (integer)
- `p` [query] (integer) — Page number (starting from 0)

**Responses:**

- `200` — Successful response

---

### `GET /projects`

**Get own active projects**

`operationId`: `getProjects`

Returns active projects **owned** by the authenticated user (not projects they were only invited to), each with its active tasklists eagerly loaded.

**Use cases:**
- Primary project picker in dashboards / onboarding — show projects the user owns and manages
- Populating a "my projects" widget without paging (this endpoint is **not** paginated)
- Bulk tooling that needs owner-scoped projects before branching into tasklists

**Behavior notes:**
- Scope is owner-only. Projects where the user is only a worker / guest are **not** returned — use `GET /all-projects` or `GET /invited-projects` for those.
- Filters only projects in the **active** state (state_id=1). Archived / template projects are excluded — use `/archived-projects` or `/template-projects`.
- Response is a flat array (not paginated). For large accounts consider `GET /all-projects` with paging.
- Tasklists embedded in each project also include only active tasklists.

**Parameters:**

- `order_by` [query] (string enum: name|date_add|date_edited_at) — Order column
- `order` [query] (string enum: asc|desc) — Order direction

**Responses:**

- `200` — Successful response _(schema: `array<ProjectWithTasklists>`)_

---

### `POST /projects`

**Create project**

`operationId`: `createProject`

Creates a new active project. The authenticated user becomes the project **author** automatically. If `project_owner_id` is omitted, the author is also the owner; otherwise the referenced user must already exist.

**Use cases:**
- Provisioning a new project from an integration (e.g. after a deal closes in CRM)
- Bulk-creating projects when onboarding a client
- Delegating ownership: the API caller creates the project and immediately assigns a different owner via `project_owner_id`

**Side effects:**
- Business-account captains are auto-invited as commanders/workers (via `BusinessAccountCaptainProjectInviter`).
- Emits `project_owner_assigner` and `project_commander_promote` events (webhooks, notifications).
- If `project_owner_id` does not map to an owner-eligible user, the request fails with `400` (`project_owner_id X is not valid`) — the business rule is enforced by `IProjectCreator`.

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `name` **required** (string)
    - `currency_iso` **required** (string enum: CZK|EUR|USD) — Currency used for budgets and invoicing in this project. Cannot be changed afterwards.
    - `project_owner_id` (integer) — ID of user assigned as owner. Must be an owner-eligible user in the caller's account. If omitted, the authenticated caller becomes the owner.

**Responses:**

- `200` — Project created _(schema: `ProjectBasic`)_

---

### `GET /template-projects`

**Get project templates**

`operationId`: `getTemplateProjects`

Paginated list of **project templates** (projects in state 3 — template) the caller can use as a source for new projects via `POST /project/create-from-template/{template_id}`.

**Use cases:**
- Populating a "create from template" picker in a client UI
- Syncing templates to an external project-provisioning tool
- Reporting on which template tags are in use

**Behavior notes:**
- The returned templates can be picked for copying regardless of whether the caller owns them, as long as they have view access.
- `users_ids[]` filters by template **owner**, not by invitees.
- Default order: `date_add asc`.

**Parameters:**

- `order_by` [query] (string enum: name|date_add|date_edited_at)
- `order` [query] (string enum: asc|desc)
- `tags[]` [query] (array<string>)
- `users_ids[]` [query] (array<integer>)
- `created_in_range[date_from]` [query] (string<date>)
- `created_in_range[date_to]` [query] (string<date>)
- `p` [query] (integer) — Page number (starting from 0)

**Responses:**

- `200` — Successful response

---

### `GET /user/{user_id}/all-projects`

**Get projects of another user**

`operationId`: `getUserProjects`

Paginated list of projects where the given `user_id` is the **owner**, intersected with what the caller has permission to see.

**Use cases:**
- Viewing teammate's workload before reassigning tasks
- HR / PM views showing all projects a specific person owns
- Filtering projects by owner in cross-functional reporting

**Behavior notes:**
- The caller sees only projects they themselves have access to — if the target user owns projects the caller can't see, they are silently omitted.
- `states_ids[]` combines states (1=active, 2=archived, 3=template). Omitting returns the server default.
- Default order: `date_add desc` (newest first).

**Parameters:**

- `user_id` [path, required] (integer)
- `states_ids[]` [query] (array<integer enum: 1|2|3>) — States: 1=active, 2=archived, 3=template
- `order_by` [query] (string enum: name|date_add|date_edited_at)
- `order` [query] (string enum: asc|desc)
- `p` [query] (integer) — Page number (starting from 0)

**Responses:**

- `200` — Successful response

---

## Search

### `POST /search`

**Fulltext search across Freelo (Elasticsearch)**

`operationId`: `search`

Unified fulltext search powered by Elasticsearch. Supports entity-type scoping (task, subtask, project, tasklist, file, comment), structural narrowing (projects, tasklists, workers, authors), state and due-date filtering.

**Use cases:**
- Global search bar in the UI
- Content-discovery integrations ("find comments about Sentry issue 123")
- Reporting that starts from a text query ("find all tasks mentioning keyword X in last quarter")

**Behavior notes (non-obvious):**
- `POST` method despite being a read — request bodies are JSON because query filters are too complex for query strings.
- `search_query` is **required**. Passing only filters without a text query is not supported here; use tag-specific list endpoints (like `/all-tasks`) for filter-only queries.
- `state_ids` defaults to `["active"]` — pass explicit states if you also want archived / finished / template results.
- `entity_type` narrows to a single category; omit for mixed results.
- ACL-filtered — Elasticsearch returns only documents the caller can read based on project membership.
- `lang` influences analyzer / stemming — defaults to account language.
- Query length is capped; `ElasticsearchQueryLengthExceededException` → 400 with a UserVisibleErrorMessage.

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `search_query` **required** (string)
    - `projects_ids` (array<integer>)
    - `tasklists_ids` (array<integer>)
    - `tasks_ids` (array<integer>)
    - `authors_ids` (array<integer>)
    - `workers_ids` (array<integer>)
    - `state_ids` (array<string enum: active|archived|finished|template|not_template|archived_finished|archived_unfinished>)
    - `lang` (string)
    - `due_date` (object)
    - `entity_type` (string enum: task|subtask|project|tasklist|file|comment)
    - `page` (integer)
    - `limit` (integer)

**Responses:**

- `200` — Search results

---

## States

### `GET /states`

**List all state definitions (active/archived/template/deleted…)**

`operationId`: `getAllStates`

Returns the reference list of entity states used across Freelo (projects, tasks, tasklists) — a static enumeration you can use to interpret numeric state IDs in other endpoints.

**Use cases:**
- Translating state IDs returned by list endpoints into human-readable names in a UI
- Building filter pickers that use `state_id` (tasks) or `states_ids[]` (projects)

**Behavior notes:**
- This is a global lookup; response is the same for every caller. Cache aggressively on the client.

**Responses:**

- `200` — Successful response

---

## Subtasks

### `GET /task/{task_id}/subtasks`

**Get subtasks (taskchecks) of a task**

`operationId`: `getSubtasksInTask`

Paginated list of subtasks ("taskchecks") under the given task. Subtasks come in two flavors — **smart taskchecks** (full tasks with their own worker, due date, comments) and **simple taskchecks** (checklist items with just a label). The response represents both uniformly.

**Use cases:**
- Rendering the subtask checklist inside a task view
- Iterating subtasks for completion reporting

**Behavior notes:**
- Use the `states_ids[]` filter (if supported by the schema) to narrow by active / finished subtasks.
- Subtasks returned are ACL-filtered by the parent task's tasklist rules.

**Parameters:**

- `task_id` [path, required] (integer)
- `p` [query] (integer) — Page number (starting from 0)

**Responses:**

- `200` — Successful response

---

### `POST /task/{task_id}/subtasks`

**Create subtask (smart or simple, auto-fallback)**

`operationId`: `createSubtask`

Creates a subtask under the given task. The endpoint automatically picks the best representation.

**Behavior notes (non-obvious):**
- The server first attempts to create a **smart taskcheck** — a full-featured subtask with worker, due date, tracking users, etc.
- If the parent task is not eligible for smart taskchecks (e.g. it's a multi-project parent, or a nested smart taskcheck), the code catches `SmartTaskcheckCanNotBeCreatedException` and silently falls back to creating a **simple taskcheck** (a checkbox item with just a name). The body you sent may be partially discarded in that case — extra fields like `worker`, `due_date`, `tracking_users_ids` are ignored for simple taskchecks.
- `tracking_users_ids` is **ACL-filtered** — user IDs without access to the parent task's tasklist are silently removed from the set via `ITrackingUsersIdsPrepender::prependWithAcl()`.
- If you need deterministic smart-taskcheck creation, verify the parent task's eligibility first (e.g. make sure it's not already a taskcheck itself).

**Parameters:**

- `task_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `SubtaskCreate`

**Responses:**

- `200` — Subtask created _(schema: `Subtask`)_

---

## Task Labels

### `POST /task-labels`

**Bulk-create task labels in the caller's workspace**

`operationId`: `createTaskLabels`

Creates task-label definitions (not assignments). Use this before `/task-labels/add-to-task/{taskId}` when you want to provision a set of labels upfront.

**Use cases:**
- Seeding a shared label palette during onboarding
- Importing labels from another system

**Behavior notes (non-obvious):**
- This is a **fetch-or-create** — labels with an existing matching name are **re-used**, not duplicated. The endpoint only creates those that don't already exist.
- The label is scoped to the caller's account and available across their accessible projects via the "task-labels-used" relation.
- If the caller has **no projects at all**, the "used" relation is silently skipped (no `NoProjectsException` bubbles up to the caller).
- The response does not explicitly report which labels were new vs. reused — query `/project-labels/find-available` or the task detail to verify.

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `labels` (array<object>)

**Responses:**

- `200` — Labels created

---

### `POST /task-labels/add-to-task/{task_id}`

**Attach labels to task (UUID or name+color)**

`operationId`: `addTaskLabelsToTask`

Assigns one or more task labels to the given task. Labels can be addressed by UUID (existing) or by name/color (fetch-or-create).

**Two input modes per label:**
1. **UUID only** — assigns an existing label by UUID as-is.
2. **Name-based** — provide `name` (required), optionally `color` and `uuid`. If `color` is omitted it defaults to `#77787a` (gray). If `uuid` is omitted it is auto-generated. An existing label is **reused when both name AND color match**; otherwise a new label is created.

**Use cases:**
- Tagging a task with a known label from the palette (UUID mode — reliable, no ambiguity)
- Quick-labeling by name from an integration without pre-creating labels (name mode — fetch-or-create)

**Behavior notes (non-obvious):**
- Name+color matching is **case-sensitive**. `"bug"` and `"Bug"` are different labels.
- If you pass a UUID that doesn't match any existing label, `CannotCreateWithProvidedUuidException` is thrown.
- When labels are actually added (vs. already present), a `task_labels_change` event is emitted (→ webhooks, audit log).
- Calling with an empty array short-circuits — no event, no ACL check, 200 response.
- Bad colors return 400 with `Unsupported color (X) provided.` using the server's color enum.

**Parameters:**

- `task_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `labels` **required** (array<TaskLabelAddInput>)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `POST /task-labels/remove-from-task/{task_id}`

**Detach labels from task**

`operationId`: `removeTaskLabelsFromTask`

Removes one or more task labels from the given task. The label definitions themselves are **not** deleted globally — they remain available for reuse.

**Three input modes per label:**
1. **UUID** — removes the label identified by UUID.
2. **Name only** — removes all labels with that name regardless of color (can affect multiple labels).
3. **Name + color** — removes only the label matching **both** name and color.

**Use cases:**
- Cleanup after a relabeling operation
- Removing a mis-assigned label

**Behavior notes (non-obvious):**
- **Name-only mode is aggressive** — it removes every label with that name, even if they have different colors. Use name+color or UUID if you want precision.
- Emits a `task_labels_change` event only if the task's label set actually changed.
- An empty `labels` array short-circuits: no ACL check, no event, 200.

**Parameters:**

- `task_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `labels` **required** (array<TaskLabelRemoveInput>)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

## Tasklists

### `GET /all-tasklists`

**Get all tasklists (across projects)**

`operationId`: `getAllTasklists`

Paginated list of tasklists visible to the caller, across all accessible projects. Can be filtered to a subset of projects via `projects_ids[]`.

**Use cases:**
- Cross-project reporting (e.g. "all tasklists touching client X")
- Building a global tasklist picker in tooling
- Dashboards that aggregate progress across a portfolio

**Behavior notes:**
- ACL is applied — tasklists the caller can't see are filtered out, even if `projects_ids[]` includes their project.
- Default order: `date_add asc`.

**Parameters:**

- `projects_ids[]` [query] (array<integer>)
- `order_by` [query] (string enum: name|date_add|date_edited_at)
- `order` [query] (string enum: asc|desc)
- `p` [query] (integer) — Page number (starting from 0)

**Responses:**

- `200` — Successful response

---

### `GET /project/{project_id}/tasklist/{tasklist_id}/assignable-workers`

**List users who can be assigned tasks in this tasklist**

`operationId`: `getAssignableWorkers`

Returns the workers who are allowed to be set as `worker` on tasks inside this tasklist — i.e. the intersection of project membership and the tasklist's ACL (if the tasklist is ACL-restricted).

**Use cases:**
- Populating the assignee picker when creating / editing a task
- Validating `worker_id` before calling `POST /tasks` or `POST /task/{id}`

**Behavior notes:**
- If the tasklist is NOT ACL-restricted, the result equals the project's full worker list.
- If the tasklist IS ACL-restricted, only users explicitly granted tasklist ACL (plus the project owner/commander) appear.

**Parameters:**

- `project_id` [path, required] (integer)
- `tasklist_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `array<UserBasic>`)_

---

### `POST /project/{project_id}/tasklists`

**Create tasklist in project**

`operationId`: `createTasklist`

Creates a new tasklist inside the given project. The tasklist inherits project-level ACL and becomes visible to all project workers unless the tasklist's own ACL is narrowed later.

**Use cases:**
- Creating a new phase / milestone inside a project
- Bulk-provisioning tasklists from an external system (imported project structure)

**Behavior notes:**
- Requires the caller to be a project manager or higher — otherwise `AclForbiddenException` / `RoleActionForbiddenException`.
- `budget` is optional and uses the stringified-currency format (e.g. "100000" = 1000.00 of the project's currency).

**Parameters:**

- `project_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `name` **required** (string)
    - `budget` (string) — Currency amount (2 decimal places, no separator)

**Responses:**

- `200` — Tasklist created _(schema: `TasklistWithBudget`)_

---

### `POST /tasklist/create-from-template/{template_id}`

**Copy a tasklist from a project template**

`operationId`: `createTasklistFromTemplate`

Copies a specific tasklist from a **project template** into either a brand-new project or an existing target. Path parameter `template_id` identifies the source project template; body `tasklist_id` identifies which tasklist inside that template to copy.

**Use cases:**
- Re-using a standardized tasklist (e.g. "QA checklist") across projects
- Seeding a new client project with a curated subset of blueprints

**Behavior notes (non-obvious):**
- Body field `tasklist_id` is **required** — it's the source tasklist's ID inside the template project referenced by path `template_id`. Mixing path + body IDs like this is deliberate.
- If `target_project_id` is **not** provided, a new project is created as the target (copying from the template).
- If `target_tasklist_id` is provided together with `target_project_id`, tasks are copied into that existing tasklist instead of a fresh one.
- `preset_date_from` shifts floating due-dates relative to this date (same semantics as project template copy).
- `users_ids` lists which template members to invite into the target — must be a subset of the template's members.

**Parameters:**

- `template_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `tasklist_id` **required** (integer) — ID of the tasklist from template
    - `target_project_id` (integer)
    - `target_tasklist_id` (integer)
    - `preset_date_from` (string<date>)
    - `users_ids` (array<integer>)

**Responses:**

- `200` — Tasklist created

---

### `GET /tasklist/{tasklist_id}`

**Get tasklist detail**

`operationId`: `getTasklist`

Returns metadata for a single tasklist — name, budget, parent project reference, and the latest `date_edited` / `date_add` audit timestamps.

**Use cases:**
- Opening a tasklist detail view
- Re-fetching metadata after an edit to confirm the state
- Reading budget for reporting purposes

**Behavior notes:**
- Performs both a tasklist fetch (ACL-checked) and a project fetch (ACL-checked). If the caller has no access to either, returns 404.

**Parameters:**

- `tasklist_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `TasklistDetail`)_

---

## Tasks

### `GET /all-tasks`

**Get all tasks (paginated, filterable)**

`operationId`: `getAllTasks`

Paginated global task search. Combines fulltext search (via Elasticsearch), structural filters (projects, tasklists, worker, state), label filters, and date-range filters.

**Use cases:**
- Primary task search / dashboard filter
- Reporting queries ("all tasks due next week for worker X")
- Cross-project views (e.g. "all open tasks with label blocker")

**Behavior notes (non-obvious):**
- `search_query` is a fulltext match on the task name through Elasticsearch — it prefilters the task set before other filters are applied; supplying only `search_query` without any `projects_ids[]` restricts across all visible projects.
- `with_label` is a single-value legacy alias for `with_labels[]` — when both are sent, `with_label` is merged into the array (no preemption). `with_label` is deprecated; prefer `with_labels[]`.
- `state_id` filters by task state — omit to get tasks in all states the caller can see (typically active + finished; depends on ACL).
- `no_due_date=true` returns only tasks without a due date; combining with `due_date_range` is effectively contradictory and the range is ignored.
- `finished_overdue=true` filters for tasks finished **after** their due date — a reporting lens for delivery SLAs.
- `worker_id` filters by assignee only (not by tracking users).

**Parameters:**

- `search_query` [query] (string) — Fulltext search query for the task name
- `state_id` [query] (integer) — ID of the tasks state
- `projects_ids[]` [query] (array<integer>) — Filter tasks by project IDs. If empty, tasks from all accessible projects are returned.
- `tasklists_ids[]` [query] (array<integer>) — Filter tasks by tasklist IDs
- `order_by` [query] (string enum: priority|name|date_add|date_edited_at)
- `order` [query] (string enum: asc|desc)
- `with_labels[]` [query] (array<string>) — Filter tasks that have at least one of the specified labels (case insensitive). Can be combined with with_label.
- `with_label` [query] (string) — Filter tasks by a single label name (case insensitive). If with_labels[] is also provided, this value is merged into that array.
- `without_label` [query] (string) — Exclude tasks that have the specified label (case insensitive)
- `no_due_date` [query] (boolean) — Only tasks with no due date
- `due_date_range[date_from]` [query] (string<date>) — Filter tasks with due date on or after this date
- `due_date_range[date_to]` [query] (string<date>) — Filter tasks with due date on or before this date
- `finished_overdue` [query] (boolean) — Only tasks finished after due date
- `finished_date_range[date_from]` [query] (string<date>) — Filter tasks finished on or after this date
- `finished_date_range[date_to]` [query] (string<date>) — Filter tasks finished on or before this date
- `worker_id` [query] (integer) — Filter by worker ID
- `p` [query] (integer) — Page number (starting from 0)

**Responses:**

- `200` — Successful response

---

### `GET /project/{project_id}/tasklist/{tasklist_id}/tasks`

**Get active tasks in a tasklist**

`operationId`: `getTasksInTasklist`

Returns all **active** tasks in the specified tasklist, ordered by the requested criterion. To fetch finished tasks use `GET /tasklist/{tasklist_id}/finished-tasks`.

**Use cases:**
- Rendering the task board for a given tasklist
- Iterating tasks for bulk operations (assign, move, etc.)
- Feeding integrations that mirror the task state

**Behavior notes:**
- Response is a flat (non-paginated) array. For very large tasklists, consider `GET /all-tasks` with filters for pagination.
- ACL-filtered: if the tasklist is ACL-restricted and the caller has no access to it, 404.

**Parameters:**

- `project_id` [path, required] (integer)
- `tasklist_id` [path, required] (integer)
- `order_by` [query] (string enum: priority|name|date_add|date_edited_at)
- `order` [query] (string enum: asc|desc)

**Responses:**

- `200` — Successful response _(schema: `array<TaskSummary>`)_

---

### `POST /project/{project_id}/tasklist/{tasklist_id}/tasks`

**Create task in tasklist**

`operationId`: `createTask`

Creates a new task in the given tasklist. The authenticated caller becomes the task's author; `worker` defaults to a value derived from the tasklist's default-worker rules if not supplied.

**Use cases:**
- Creating a ticket from an integration (Sentry → Freelo, Slack → Freelo, etc.)
- Programmatic task provisioning
- Migrating tasks from another system

**Behavior notes:**
- The assignee (`worker`) must be one of the tasklist's `assignable-workers` (see that endpoint). A user outside the ACL scope results in `WorkerHasNoAccessToTasklistException` → 403.
- `tracking_users_ids` defaults to the assignee + author if omitted (via `TaskDataDefaultTrackingUsersFiller`).
- Creates a `task_created` event (→ webhooks, notifications, calendar sync).

**Parameters:**

- `project_id` [path, required] (integer)
- `tasklist_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `TaskCreate`

**Responses:**

- `200` — Task created _(schema: `TaskCreated`)_

---

### `DELETE /public-link/task/{task_id}`

**Revoke public share link for a task**

`operationId`: `deletePublicLinkToTask`

Deletes the task's public link, immediately invalidating any previously shared URL.

**Use cases:**
- Rotating a compromised link
- Ending external sharing when a client engagement ends

**Parameters:**

- `task_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `GET /public-link/task/{task_id}`

**Get (or create) a public share link for a task**

`operationId`: `getPublicLinkToTask`

Returns a public, unauthenticated URL that lets anyone with the link view the task. If no public link exists yet, one is **created on-the-fly** and returned.

**Use cases:**
- Sharing a task with a client who has no Freelo account
- Embedding a shareable link in a status update

**Behavior notes (non-obvious):**
- This is a **GET that creates** — first call to this endpoint creates the link; subsequent calls return the same URL. To invalidate, use `DELETE /public-link/task/{task_id}`.
- The URL exposes the task's content read-only to anyone holding it. Rotating is done by DELETE + GET (creates a new URL).

**Parameters:**

- `task_id` [path, required] (integer)

**Responses:**

- `200` — Successful response

---

### `POST /task/create-from-template/{template_id}`

**Create task by copying from a template task**

`operationId`: `createTaskFromTemplate`

Copies a single task out of a project template into a target tasklist. Mirrors the structure of the tasklist / project template copy endpoints.

**Use cases:**
- Adding a standard boilerplate task (e.g. "Kickoff checklist") to an existing project
- Cloning a canonical bug-report template

**Behavior notes (non-obvious):**
- Body field `task_id` is **required** — it identifies the source task inside the template referenced by path `template_id`.
- If `target_tasklist_id` is omitted, the copied task lands in the **same tasklist ID** it had in the template — which only works if `target_project_id` (or an auto-created project) has a tasklist with that ID. Safer to always pass both.
- `preset_date_from` shifts floating due-dates (same as other template endpoints).
- `users_ids` is a list of template members to invite into the destination.

**Parameters:**

- `template_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `task_id` **required** (integer) — ID of the task from template
    - `target_project_id` (integer)
    - `target_tasklist_id` (integer)
    - `preset_date_from` (string<date>)
    - `users_ids` (array<integer>)

**Responses:**

- `200` — Task created

---

### `DELETE /task/{task_id}`

**Delete task (soft-delete)**

`operationId`: `deleteTask`

Soft-deletes a task. It disappears from listings and cannot be reactivated via `/activate` (activation only un-finishes a finished task, it does **not** undelete).

**Use cases:**
- Removing spam / mistaken tasks
- Cleaning up a noisy tasklist

**Behavior notes:**
- Cascades to subtasks (they are also hidden).
- Emits `task_deleted` event — webhooks and notifications fire.
- Requires delete permission (owner / commander / author, per role rules).

**Parameters:**

- `task_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `GET /task/{task_id}`

**Get task detail**

`operationId`: `getTask`

Returns full task detail — metadata, labels, worker, tracking users, due dates, subtasks count, time estimates, custom field values, and (for non-commanders) computed spent minutes and cost.

**Use cases:**
- Opening a task in the UI
- Fetching every detail before an edit (for diffing)
- Pulling full context for AI / reporting pipelines

**Behavior notes (non-obvious):**
- For multi-project tasks, the response contains a `multi_project_task` block mapping the task across its projects and may expose a `parent_task_id` if this is a subtask linked to a multi-project parent.
- Spent minutes (`minutes`) and `cost.amount` are only included when the caller is **not** a project commander (commanders see account-wide billing elsewhere).
- Labels include labels inherited from a multi-project parent.
- `copied_from_task` references the origin task if the task was created from a template or as a multi-project copy.

**Parameters:**

- `task_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `TaskDetail`)_

---

### `POST /task/{task_id}`

**Edit task (partial update)**

`operationId`: `editTask`

Partially updates a task. Only the fields listed below are editable through this endpoint — any other key in the body is **silently ignored** (the facade uses `array_intersect_key` against a fixed whitelist).

**Editable fields:** `name`, `worker`, `due_date`, `due_date_end`, `labels`, `priority_enum`, `tracking_users_ids`, `add_tracking_users_ids`, `remove_tracking_users_ids`.

**Use cases:**
- Reassigning a task
- Changing priority / due date
- Renaming or relabeling from an integration
- Adjusting tracking users (add / remove / replace)

**Behavior notes (non-obvious):**
- **`worker_id` accepted as alias for `worker`:** the facade contains a make.com-compatibility HACK — if the body has `worker_id`, it is copied into `worker`. This is undocumented elsewhere but is the same field.
- **Tracking users have three mutually-exclusive update shapes:**
  - `tracking_users_ids` **replaces** the full set (pass `[]` to clear all).
  - `add_tracking_users_ids` **merges** the given IDs into the current set.
  - `remove_tracking_users_ids` **removes** the given IDs from the current set.
  Mixing `tracking_users_ids` (replace) with add/remove in one call is accepted but the final state is determined by the facade's order of operations — keep it to one shape per call to be deterministic.
- **Labels must reference existing labels or be fully-formed new label DTOs** — the behavior matches the task-labels add-to-task semantics.
- The endpoint responds with the task's full detail (same shape as `GET /task/{id}`).
- Note the HTTP method: `POST` is used for edits here (historical REST shape), not `PUT`/`PATCH`.

**Parameters:**

- `task_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `name` (string)
    - `due_date` (string<date-time>)
    - `due_date_end` (string<date-time>)
    - `worker` (integer)
    - `priority_enum` (string enum: l|m|h) — Allowed options are l, m, h. Set to null to remove priority.
    - `tracking_users_ids` (array<integer>) — Set (replace) all tracking users. Pass an empty array to remove all.
    - `add_tracking_users_ids` (array<integer>) — Add tracking users by user ID (merged with existing).
    - `remove_tracking_users_ids` (array<integer>) — Remove tracking users by user ID.

**Responses:**

- `200` — Task updated _(schema: `TaskDetail`)_

---

### `POST /task/{task_id}/activate`

**Reopen a finished task**

`operationId`: `activateTask`

Moves a task from the **finished** state back to **active**. Use to reverse a `finish` operation.

**Use cases:**
- Reopening a task that was closed prematurely
- Reverting an incorrect "finish" action triggered by an integration

**Behavior notes (non-obvious):**
- Only works on finished tasks. On an active task, returns 200 without changes. On a **deleted** task, returns 404 (this endpoint does **not** un-delete — it's not symmetric with the project activate endpoint).
- Emits `task_activated` event / webhooks.

**Parameters:**

- `task_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `GET /task/{task_id}/description`

**Get task description**

`operationId`: `getTaskDescription`

Returns the task's description — the first "pinned" comment that serves as the canonical rich-text body of the task.

**Use cases:**
- Loading the long-form body in a task detail view
- Extracting the description for reporting or AI summarization

**Behavior notes:**
- If the task has no description yet, the response is still 200 but fields may be empty / null — use the edit endpoint to create one.

**Parameters:**

- `task_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `Comment`)_

---

### `POST /task/{task_id}/description`

**Create or overwrite task description (upsert)**

`operationId`: `editTaskDescription`

Creates the task description if none exists, or **overwrites** the existing one — there is no "append" or "history" behavior. File attachments given in `files` are attached to the description comment.

**Use cases:**
- Filling a task body from a ticketing integration
- Editing the task's main description in a UI
- Attaching files that belong to the task body (not to a separate comment)

**Behavior notes (non-obvious):**
- Upsert semantics: first call creates, subsequent call **replaces** the content entirely. Any previous content is lost — not stored in history.
- `files` expects already-uploaded file UUIDs (see `POST /file/upload`).

**Parameters:**

- `task_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `content` **required** (string)
    - `files` (array<FileUpload>)

**Responses:**

- `200` — Description updated _(schema: `Comment`)_

---

### `POST /task/{task_id}/finish`

**Mark task as finished**

`operationId`: `finishTask`

Closes a task — moves it to the **finished** state.

**Use cases:**
- Closing a task via an integration (e.g. Zapier "when ticket closed → finish Freelo task")
- Bulk-closing tasks after a release

**Behavior notes:**
- Any running timetracking **on this specific task** is stopped as part of the finish flow.
- Emits `task_finished` event / webhooks.
- Requires the caller to be the assignee, author, or a project manager (Role rules). Otherwise `RoleActionForbiddenException` → 403.

**Parameters:**

- `task_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `POST /task/{task_id}/move/{tasklist_id}`

**Move task to another tasklist (optionally cross-project)**

`operationId`: `moveTask`

Moves a task into a different tasklist. Target tasklist may belong to the same or a different project.

**Use cases:**
- Re-phasing a task (moving from "Backlog" to "In progress" tasklist)
- Escalating a task between projects (e.g. support → engineering)
- Re-organizing multi-project tasks

**Behavior notes (non-obvious):**
- For **multi-project tasks**, the optional body field `multi_project_task.source_tasklist_id` picks which project-instance to move. When omitted (or set to the primary task's own tasklist), the cross-project move flow runs and applies `work_reports_action` / `custom_fields_action` rules. When it points to a **child task's** tasklist, only that child is moved within its own project, and `work_reports_action` / `custom_fields_action` are **ignored**.
- `work_reports_action` decides what happens to existing work reports on cross-project moves: `move_to_target_project` (default) rebinds them, `keep_on_origin_project` leaves them tied to the origin.
- `custom_fields_action` controls custom-field-value handling when the target project does not have the same custom fields — destructive options (`delete_*`) lose data; `move_to_comments_*` preserves it as a comment.
- Required field on multi-project: if the caller has no ACL on the source tasklist's project, 403.

**Parameters:**

- `task_id` [path, required] (integer)
- `tasklist_id` [path, required] (integer)

**Request body:**

_Request body_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `work_reports_action` (string enum: move_to_target_project|keep_on_origin_project)
    - `custom_fields_action` (string enum: nothing|delete_what_cant_be_keep|move_to_comments_what_cant_be_keep|delete_all|move_to_comments_all)
    - `multi_project_task` (object) — Optional multi-project task context for moving a specific project instance

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `POST /task/{task_id}/projects`

**Assign task to an additional project (make it multi-project)**

`operationId`: `assignTaskToProject`

Promotes a single-project task into a multi-project task (UVVP) by creating a **child task** in another project, linked to the same logical parent.

**Use cases:**
- Sharing a ticket across departments (e.g. one ticket visible in Sales and Engineering projects)
- Cross-team visibility for long-running initiatives

**Behavior notes:**
- Target project is **derived from the `tasklist_id`** — you pass the tasklist, not the project. The target tasklist must belong to a project the caller has access to, otherwise 403.
- Subsequent content (comments, worker) operations on the parent and child task may diverge depending on the multi-project architecture (see project docs `docs/feature/multi-project-tasks.md`).

**Parameters:**

- `task_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `tasklist_id` **required** (integer) — Target tasklist ID (the project is derived from it)

**Responses:**

- `200` — Task assigned to project
- `403` — Forbidden
- `404` — Task or tasklist not found

---

### `DELETE /task/{task_id}/projects/{project_id}`

**Remove task from a secondary project**

`operationId`: `removeTaskFromProject`

Reverses a prior "assign to project" call by deleting the child task that belonged to the specified secondary project. The primary task continues to exist.

**Use cases:**
- Revoking cross-team visibility once a handoff is done
- Cleanup after an accidental multi-project assignment

**Behavior notes:**
- Attempting to remove a task from its **primary** project (the one where it was originally created) is not allowed — that requires `DELETE /task/{task_id}` instead. The endpoint returns 403 `AclException` in that case.
- Returns 404 if the task is not present in the given project at all.

**Parameters:**

- `task_id` [path, required] (integer)
- `project_id` [path, required] (integer)

**Responses:**

- `200` — Task removed from project _(schema: `SuccessResponse`)_
- `403` — Forbidden
- `404` — Task or project not found

---

### `GET /task/{task_id}/relations`

**Get task relations**

`operationId`: `getTaskRelations`

Returns all relations for a task (types: `blocked_by`, `blocks`, `related_to`, `duplicate_of`).
Relations to tasks the caller cannot access are filtered out.

**Parameters:**

- `task_id` [path, required] (integer)

**Responses:**

- `200` — Task relations
- `403` — Forbidden
- `404` — Task not found

---

### `DELETE /task/{task_id}/reminder`

**Clear caller's task reminder**

`operationId`: `deleteTaskReminder`

Removes the calling user's personal reminder for the given task.

**Behavior notes:**
- Only the caller's own reminder is deleted — reminders set by other users are unaffected.
- Idempotent: calling with no reminder present returns 200.

**Parameters:**

- `task_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `POST /task/{task_id}/reminder`

**Set reminder on task (for caller)**

`operationId`: `createTaskReminder`

Schedules a **personal** reminder for the calling user on a specific task. The reminder fires a notification to the caller at `remind_at`.

**Use cases:**
- "Ping me about this task at 9 AM tomorrow"
- Snoozing a task for later follow-up

**Behavior notes (non-obvious):**
- Reminders are **per-user** — this endpoint sets a reminder for the caller only. Other tracking users are not affected.
- Calling this endpoint on a task that already has a reminder by the caller **overwrites** the existing `remind_at` (upsert behavior).
- `remind_at` is expected in ISO 8601; the server normalizes it internally.

**Parameters:**

- `task_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `remind_at` **required** (string<date-time>)

**Responses:**

- `200` — Reminder created

---

### `DELETE /task/{task_id}/total-time-estimate`

**Remove the task's total time estimate**

`operationId`: `deleteTotalTimeEstimate`

Clears the total time estimate for the task.

**Behavior notes:**
- Per-user estimates are **not** removed by this call — delete them separately.
- Idempotent: calling on a task without an estimate returns 200.

**Parameters:**

- `task_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `POST /task/{task_id}/total-time-estimate`

**Create or update the task's total time estimate (upsert)**

`operationId`: `setTotalTimeEstimate`

Sets the total expected effort for a task (aggregated across the team). If a total estimate already exists, it is **updated**; otherwise a new one is created.

**Use cases:**
- Capturing the team-wide effort budget for capacity planning
- Refreshing an estimate after re-scoping

**Behavior notes:**
- Upsert semantics (`TimeEstimateFacade::createOrUpdate`). Calling this endpoint multiple times is safe.
- Per-user estimates are managed separately via `/task/{id}/users-time-estimates/{user_id}` — the total is not automatically derived from per-user sums.

**Parameters:**

- `task_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `minutes` **required** (integer)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `DELETE /task/{task_id}/users-time-estimates/{user_id}`

**Remove per-user time estimate**

`operationId`: `deleteUserTimeEstimate`

Clears the per-user time estimate of the given user on this task.

**Behavior notes:**
- The task's total estimate is unaffected.
- Idempotent: calling on a missing estimate returns 200.

**Parameters:**

- `task_id` [path, required] (integer)
- `user_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `POST /task/{task_id}/users-time-estimates/{user_id}`

**Set per-user time estimate on a task (upsert)**

`operationId`: `setUserTimeEstimate`

Upserts a **per-user** time estimate — how much effort the given `user_id` is expected to spend on this task.

**Use cases:**
- Project-manager capacity planning (assigning hours to each teammate)
- Feeding a billing estimate where each worker has a different rate

**Behavior notes:**
- Upsert semantics (`TimeEstimateUserFacade::createOrUpdate`).
- Does **not** automatically update the total time estimate — manage totals separately via `/task/{id}/total-time-estimate`.
- The `user_id` must be an assignable worker of the task's tasklist; otherwise 403 / 404 depending on ACL.

**Parameters:**

- `task_id` [path, required] (integer)
- `user_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `minutes` **required** (integer)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `GET /tasklist/{tasklist_id}/finished-tasks`

**Get finished tasks in a tasklist**

`operationId`: `getFinishedTasks`

Paginated list of **finished** (closed) tasks inside a given tasklist. Optionally narrow by a fulltext search of the task name.

**Use cases:**
- Archive / history view of what's been completed in a tasklist
- Retrospective reports on delivered scope
- Finding a specific closed task by name for reactivation

**Behavior notes:**
- `search_query` is an Elasticsearch-backed fulltext match on the task name; when omitted, all finished tasks in the tasklist are returned (paginated).
- Active tasks are **not** included — use `/project/{pid}/tasklist/{tid}/tasks` for those.

**Parameters:**

- `tasklist_id` [path, required] (integer)
- `search_query` [query] (string)
- `p` [query] (integer) — Page number (starting from 0)

**Responses:**

- `200` — Successful response

---

### `POST /tasks/relations`

**Find task relations in bulk**

`operationId`: `findTaskRelationsBulk`

Returns relations for a list of tasks. Each item in the response contains the task ID
and its relations (types: `blocked_by`, `blocks`, `related_to`, `duplicate_of`).
Tasks the caller cannot access are silently omitted from the response.

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `task_ids` **required** (array<integer>) — List of task IDs (1–100 items).

**Responses:**

- `200` — Relations grouped by task ID
- `400` — Invalid request body

---

## Time Tracking

### `POST /timetracking/edit`

**Edit the running time tracking session**

`operationId`: `editTimeTracking`

Updates the caller's currently running session — typically used to switch the tracked task or change the note mid-flight without losing elapsed time.

**Use cases:**
- Switching context (another task) without stopping + starting
- Fixing a wrong `note` entered at start
- Reassigning general (no-task) tracking to a task

**Behavior notes:**
- There is no session ID — the endpoint always targets the caller's single active session.
- Returns HTTP **409 Conflict** with `"Timetracking is not running."` if no session is active.
- Setting `task_id=null` disassociates the session from any task (continues as general work).
- Elapsed minutes are preserved; only the tracked task / note change.

**Request body:**

_Request body_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `task_id` (integer) — ID of the task to reassign the session to. Can be used to switch tasks on an active session.
    - `note` (string) — Updated note for the time tracking session.

**Responses:**

- `200` — Time tracking session updated
- `409` — No time tracking is currently running for this user _(schema: `ErrorResponse`)_

---

### `POST /timetracking/start`

**Start a time tracking session**

`operationId`: `startTimeTracking`

Starts a new timer ("work running") for the authenticated caller. Each user may have **at most one** active session at any time.

**Use cases:**
- User begins working on a task
- Integration starts timer when a ticket moves to "in progress"

**Behavior notes (non-obvious):**
- Only one running session per user — attempting to start while one is already running returns HTTP **409 Conflict** with message `"Timetracking is already running."`. Call `/timetracking/stop` first (or `/timetracking/edit` to reassign the current session).
- All body fields are **optional**. `task_id` is nullable — you can track general work not tied to a specific task.
- `date_reported` defaults to "now" (server time) if not provided. Passing an explicit `date_reported` backdates the session's start time.
- Returns the UUID of the newly created running-work record.

**Request body:**

_Request body_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `task_id` (integer) — ID of the task to track time for. Optional — if not provided, time tracking starts without a task assignment.
    - `note` (string) — Optional note for the time tracking session.

**Responses:**

- `200` — Time tracking started successfully
- `409` — Time tracking is already running for this user _(schema: `ErrorResponse`)_

---

### `GET /timetracking/status`

**Get current time tracking status**

`operationId`: `getTimeTrackingStatus`

Returns the caller's currently running session with task context, note, labels, billability, and cost info.

**Use cases:**
- Polling whether the user is currently tracking time
- Rendering a "you are tracking X since HH:MM" indicator
- Verifying state before issuing a `/timetracking/edit` / `/stop`

**Behavior notes (non-obvious):**
- **Returns HTTP 204 No Content** when no session is active — **not 404 and not a 200 with empty body**. Callers must treat 204 as a valid "nothing running" state.
- `cost`, `is_cost_fixed`, `is_billable`, `project_setting` reflect what would land in a work report if stopped right now.

**Responses:**

- `200` — Active time tracking session details.
- `204` — No time tracking session is currently running.

---

### `POST /timetracking/stop`

**Stop the running time tracking session**

`operationId`: `stopTimeTracking`

Stops the caller's currently running timer and converts it into a finalized **work report**. Returns the resulting work report.

**Use cases:**
- User ends work on a task
- Integration stops timer on ticket closure

**Behavior notes:**
- No request body. The endpoint always targets the caller's own active session (one per user).
- Returns HTTP **409 Conflict** with `"Timetracking is not running."` when no session is active.
- The produced work report inherits the task, note, and `date_reported` set at start / edit time. Minutes are computed from the start time to now.

**Responses:**

- `200` — Time tracking stopped and work report created _(schema: `WorkReport`)_
- `409` — No time tracking is currently running for this user _(schema: `ErrorResponse`)_

---

## Users

### `DELETE /user/{user_id}/out-of-office`

**Disable user's out-of-office**

`operationId`: `disableOutOfOffice`

Clears the user's OOO window. Idempotent — calling on a user who is not OOO returns 200.

**Use cases:**
- Ending OOO early when the user returns sooner than planned
- Cleanup flows after a scheduled OOO has elapsed

**Behavior notes:**
- Same ACL as GET/POST on this path: caller must be the target user or a valid coworker.

**Parameters:**

- `user_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `GET /user/{user_id}/out-of-office`

**Get out-of-office status of a user**

`operationId`: `getOutOfOffice`

Returns the out-of-office (OOO) period set for the given user, or `null` if they are not currently marked as away.

**Use cases:**
- Showing OOO badge next to assignees
- Letting automations re-route assignments while the user is away

**Behavior notes:**
- The caller must be the **target user themselves** or a valid coworker (share at least one project). Otherwise 404 — the endpoint does **not** differentiate "not found" from "not authorized" to avoid user enumeration.
- Dates are returned in UTC.

**Parameters:**

- `user_id` [path, required] (integer)

**Responses:**

- `200` — Successful response

---

### `POST /user/{user_id}/out-of-office`

**Enable / overwrite user's out-of-office**

`operationId`: `enableOutOfOffice`

Sets or overwrites the out-of-office window for the given user.

**Use cases:**
- Self-service: user sets their own vacation
- HR / PM sets a coworker's OOO on their behalf

**Behavior notes:**
- ACL: caller must be the target user or a valid coworker (same rule as GET).
- Calling this on a user who already has an OOO **overwrites** the existing window (not appended).
- `date_to` must be `>= date_from`, otherwise 400.
- Dates are stored normalized to UTC.

**Parameters:**

- `user_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `out_of_office` **required** (object)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `GET /users`

**Get all coworkers visible to caller**

`operationId`: `getAllUsers`

Paginated list of users the authenticated caller shares at least one project with — effectively their "coworkers book".

**Use cases:**
- Populating an assignee picker outside of a project context
- Building a company-wide people directory scoped to the caller's network
- Resolving email → user ID for offline users

**Behavior notes:**
- Does **not** return the caller themselves.
- Users with whom the caller only shares archived / deleted projects may still appear; the repository filters by membership, not by state.

**Parameters:**

- `p` [query] (integer) — Page number (starting from 0)

**Responses:**

- `200` — Successful response

---

### `POST /users/manage-workers`

**Invite users (by email or ID) to one or more projects**

`operationId`: `inviteUsersToProjects`

Invites existing users (by `users_ids`) and/or external people (by `emails`) to one or more projects. Emails for users who don't exist yet trigger user creation.

**Use cases:**
- Onboarding a batch of new teammates to a shared project
- Granting access to an existing teammate in additional projects
- Programmatic project invitations from an HR / CRM integration

**Behavior notes (non-obvious):**
- Exactly one of `emails` or `users_ids` must be non-empty. Sending both empty → `400` with message "At least one of the following fields must be filled: emails, users_ids".
- When `users_ids` is non-empty, `projects_ids` **must** also be non-empty (you can't invite an existing user to "nothing"). For email-only invitations, `projects_ids` is still required logically because invites target projects.
- Emails that do not match any existing user trigger **user creation** — the new users are returned in `newly_created_users`. This is the primary way to provision external collaborators.
- `acl_tasklists` scopes the invitation to a subset of tasklists in the target projects (ACL workers). Omit to grant full project access.
- The endpoint enforces `api_only_invite=true` internally — plan-limit side effects match the same flow as email-based invites from the UI. Exceeding the account's user-seat plan throws `PlanExceededException` (429/403 depending on context).
- The `removed_users_from_projects` key in the response is populated only when some ACL adjustment implicitly removed workers (e.g. narrowing tasklist ACLs). It is not used for "deletion" requests.

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `projects_ids` **required** (array<integer>)
    - `emails` (array<string<email>>)
    - `users_ids` (array<integer>)

**Responses:**

- `200` — Users invited

---

### `GET /users/me`

**Authentication health check**

`operationId`: `getUsersMe`

Verifies that the provided credentials are valid.
Returns 200 with the authenticated user's information.
Returns 401 when credentials are invalid or missing.

**Responses:**

- `200` — Authentication is valid
- `401` — Invalid or missing credentials

---

### `GET /users/project-manager-of`

**List users who made me their project manager**

`operationId`: `getProjectManagerOf`

Returns the list of users (project owners) who have promoted the authenticated caller to be **their** project manager. Use to discover on whose behalf the caller may act across projects.

**Use cases:**
- UI "Acting on behalf of" selector for PMs managing multiple clients
- Scoping automations to users the caller has delegated authority for

**Responses:**

- `200` — Successful response _(schema: `array<UserBasic>`)_

---

## Work Reports

### `POST /task/{task_id}/work-reports`

**Log a work report on a task**

`operationId`: `createWorkReport`

Creates a finalized work report (time entry) directly on a task — bypassing the timetracking flow. Useful for retroactively logging work.

**Use cases:**
- Logging hours after the fact (Monday morning timesheet entry)
- Importing time data from external timesheet systems
- Manual adjustments on behalf of another worker

**Behavior notes:**
- `worker_id` defaults to the caller if omitted. To log time for a different user, the caller must be the project's owner / commander / have reporting rights; otherwise `WorkerHasNoAccessToTasklistException` → 400.
- `date_reported` defaults to today if omitted; pass an explicit date to backdate.
- `cost` uses the string currency-amount format (e.g. `"100025"` = 1000.25). If omitted, the server derives it from the worker's hourly rate × minutes.
- 400 `WorkReportCanNotBeCreatedException` fires if the combination (project state, tasklist ACL) disallows logging time.

**Parameters:**

- `task_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `date_reported` (string<date>)
    - `worker_id` (integer)
    - `minutes` **required** (integer)
    - `cost` (string) — Currency amount (2 decimal places × 100)
    - `note` (string)

**Responses:**

- `200` — Work report created _(schema: `WorkReport`)_

---

### `GET /work-reports`

**Get work reports (paginated, filterable)**

`operationId`: `getWorkReports`

Returns work reports — finalized time entries — filtered by project, user, task, label, or date.

**Use cases:**
- Time-sheet exports
- Billing calculations (combine with `currency` filter)
- Utilization reporting per user

**Behavior notes (non-obvious):**
- `currency` defaults to `CZK` if not specified — costs are converted to the chosen currency for comparability across projects. Always pass it explicitly if mixing multiple currencies.
- `with_own_taskless=true` **automatically scopes the query to the caller's own user** — i.e. it implicitly adds caller to `users_ids[]`. If you need all users' taskless reports, you need elevated permissions and a different endpoint (not exposed here).
- `tasks_labels[]` accepts label UUIDs, not names or IDs.
- `date_edited_from` returns reports with `date_edited >= value` — useful for incremental sync.

**Parameters:**

- `projects_ids[]` [query] (array<integer>)
- `users_ids[]` [query] (array<integer>)
- `tasks_ids[]` [query] (array<integer>)
- `tasks_labels[]` [query] (array<string<uuid>>) — UUIDs for task labels
- `date_reported_range[date_from]` [query] (string<date>)
- `date_reported_range[date_to]` [query] (string<date>)
- `date_add_range[date_from]` [query] (string<date>)
- `date_add_range[date_to]` [query] (string<date>)
- `date_edited_from` [query] (string<date>)
- `with_own_taskless` [query] (boolean) — Include the authenticated user's work reports without an associated task. Automatically filters by the authenticated user.
- `p` [query] (integer) — Page number (starting from 0)

**Responses:**

- `200` — Successful response

---

### `DELETE /work-reports/{work_report_id}`

**Delete a work report**

`operationId`: `deleteWorkReport`

Permanently removes a work report.

**Use cases:**
- Correcting duplicate entries
- Removing a report logged by mistake

**Behavior notes:**
- ACL: only the report author or project admin (owner / commander) can delete. Unauthorized callers get 400 with `UserCannotDeleteWorkReport` message (not 403).
- If the report is tied to a project that has been **marked as invoiced**, the deletion may be refused — invoices freeze underlying reports.

**Parameters:**

- `work_report_id` [path, required] (integer)

**Responses:**

- `200` — Successful response _(schema: `SuccessResponse`)_

---

### `POST /work-reports/{work_report_id}`

**Edit an existing work report**

`operationId`: `editWorkReport`

Updates minutes, cost, date, note, or re-targets the report at a different task.

**Use cases:**
- Fixing a mistyped duration
- Reassigning a work report to the correct task
- Adjusting billable cost manually

**Behavior notes:**
- `task_id` can be changed to **re-parent** the report to a different task — ACL is re-checked against the new task.
- ACL rules: the report author and the project owner/commander can edit; other users get `NotFoundException` (hiding existence).
- If the report's parent project has been marked as invoiced, edits may be blocked — see `/issued-invoice/{id}/mark-as-invoiced`.

**Parameters:**

- `work_report_id` [path, required] (integer)

**Request body:**

_Request body (required)_

- Content-Type: `application/json`
- Schema: `object`
- Properties:
    - `minutes` (integer)
    - `cost` (string)
    - `date_reported` (string<date>)
    - `note` (string)
    - `task_id` (integer)

**Responses:**

- `200` — Work report updated _(schema: `WorkReport`)_

---
