# ADR-0004: RBAC Roles and Permissions

## Status

Accepted

## Context

The application needs role-based control over both backend access and visible UI actions.
The initial requirement set is:

- roles start with `administrator` and `client`
- users may hold multiple roles
- permissions must be granular and reusable across web and API surfaces
- public self-registration must be removed
- administrators must be able to create users and assign roles
- administrators need a UI to create and edit roles
- admin-created users may optionally receive a magic-link invite email, disabled by default

## Decision

Use Spatie Laravel-Permission as the RBAC foundation.

The authorization model is database-driven:

- `roles` and `permissions` are stored in the database
- users may have many roles
- permissions are assigned to roles, not directly to users
- the same permission names are enforced in both web controllers and API controllers

Initial seeded roles:

- `administrator`
- `client`

Initial seeded permissions:

- `USERS.CREATE`
- `USERS.READ`
- `USERS.UPDATE`
- `USERS.DELETE`
- `ROLES.CREATE`
- `ROLES.READ`
- `ROLES.UPDATE`
- `ROLES.DELETE`

Initial seeded assignments:

- `administrator` receives all permissions
- `client` receives no permissions

Public registration is removed from guest routes. New users are created by administrators in the application UI. During creation, the admin can optionally enable `send_invite`; when enabled, the system generates a login token and sends the existing magic-link email.

## Consequences

Positive:

- permissions stay consistent across web and API entry points
- future roles can be added without code changes to enums or hardcoded lists
- UI visibility can reuse the same shared permission list exposed through Inertia
- invite sending reuses the same login flow already used by normal magic-link authentication

Tradeoffs:

- role and permission seeding must remain aligned with tests and admin UI expectations
- controller authorization must be kept explicit when adding new resources
- permissions are currently role-only; direct user permission exceptions are intentionally not supported

## Testing Notes

The RBAC implementation is covered by:

- feature tests for role-protected user management and role management
- unit tests for policy edge cases like self-delete protection and administrator role delete protection
