## Plan: DreamHost-Ready SaaS Foundation

Build a Laravel-first monolith using Inertia.js + React + TailwindCSS + shadcn/ui with MySQL, while designing and implementing public API endpoints in parallel from sprint 1. This gives fast delivery on shared hosting and avoids rework when mobile clients arrive.

**Steps**

1. Phase 1 - Architecture Baseline and Environment Constraints
2. Confirm DreamHost runtime and deployment assumptions (PHP version, CLI access, cron support, SSL, mail delivery, queue options) and lock minimum target versions for Laravel, Node, MySQL. This gates all implementation choices.
3. Define system boundaries: web app (Inertia React), API surface (versioned), auth subsystem (magic link + Google OAuth), and data domains (clients, service plans/subscriptions).
4. Establish coding conventions early: English domain terms in code and UI; UTC timestamps in DB; explicit soft-delete policy by entity; API versioning from day one (/api/v1).
5. Phase 2 - Project Bootstrap and GitHub Repository Foundation (depends on Phase 1)
6. Create GitHub repository as source-of-truth with protected main branch, pull-request checks, and environment branch strategy (main=production, optional develop=integration).
7. Initialize Laravel app with Inertia React and TailwindCSS integration, set up environment templates, and baseline security headers.
8. Bootstrap shadcn/ui component library: initialize with the React+Vite adapter, configure path aliases, and add baseline components (Button, Input, Dialog, Toast) as owned source files in resources/js/components/ui.
9. Configure DB connection strategy for DreamHost MySQL and create migration strategy (idempotent forward-only migrations with seeded baseline data for plans/statuses).
10. Configure frontend build/deploy workflow for shared hosting (build assets in CI, publish compiled assets only to runtime path).
11. Phase 3 - Identity and Access (parallel with Phase 4 after core bootstrap)
12. Implement passwordless login via signed email magic links with expiration, one-time usage, and audit trail.
13. Integrate Google OAuth for social sign-in with account-linking rules (same email merges to one user identity).
14. Define authorization model as database-driven RBAC so API and web permissions stay consistent: users can hold multiple roles; initial roles are `administrator` and `client`; permissions are seeded granularly as `USERS.*`, `CLIENTS.*`, and `ROLES.*`.
15. Public self-registration is disabled. User creation moves to the admin UI and supports optional invite delivery through the existing magic-link email flow.
16. Phase 4 - Core Domain MVP (parallel with Phase 3)
17. Model domain entities for MVP: Client + related contact and lifecycle fields; keep schema extensible for future subscription records.
18. Implement Client CRUD in web UI (Inertia pages, server-side validation, pagination/filtering, optimistic UX where safe).
19. Implement matching API endpoints for Client CRUD in /api/v1 with resource transformers, validation requests, and consistent error envelope.
20. Add basic activity history for client changes (created/updated/archived) to support traceability and mobile consistency later.
21. Phase 5 - API-First Expansion Foundation (depends on Phases 3-4)
22. Publish API contract artifacts (OpenAPI or equivalent) and enforce response conventions (pagination/meta/errors).
23. Introduce token strategy for mobile/API consumers (Sanctum personal access tokens or OAuth flow) while keeping browser auth session-based.
24. Add rate limiting and API observability baseline (request IDs, structured logs, failure metrics).
25. Phase 6 - GitHub Actions Deployment and DreamHost Operations (depends on all prior phases)
26. Implement CI workflow on GitHub Actions for lint/test/build on every pull request and push to develop and main.
27. Configure two GitHub Actions Environments: staging and production, each with isolated secrets, environment URL, and protection rules.
28. Implement staging CD workflow: auto-deploy on push to develop using staging secrets and staging host/path.
29. Implement production CD workflow: deploy from main only after required approval gate in production environment.
30. Define non-interactive release script shared by both environments: maintenance on, sync release, install dependencies, migrate --force, optimize/cache warmup, maintenance off, health check, rollback path.
31. Configure production-safe queue and scheduler approach compatible with DreamHost constraints (cron-driven scheduler, async jobs where feasible).
32. Add fallback deploy option if hosted runner cannot reach DreamHost (manual SSH deploy script or DreamHost-triggered git pull).
33. Set backups and recovery checklist for DB and uploaded assets.
34. Phase 7 - Quality Gates and Go-Live Readiness
35. Add testing pyramid baseline: unit tests for policy/domain rules, feature tests for auth, RBAC administration, and Client CRUD (web + API), and smoke tests for deploy validation.
36. Execute UAT checklist (auth flows, CRUD correctness, API parity, error handling, pagination, permission checks, admin user creation with optional invite).
37. Prepare post-MVP roadmap to add subscriptions module (plans, contracts, renewals/cancellations, billing integration) without breaking current API contracts.

**Relevant files**

- Greenfield workspace currently has no files; implementation should create standard Laravel structure plus React/Inertia frontend and API versioned routes.
- During implementation, prioritize explicit separation between web routes/controllers and API routes/controllers/services to preserve mobile-readiness.

**Verification**

1. Environment verification: confirm production and local parity for PHP extensions, queue/scheduler behavior, mail transport, and OAuth callback URLs.
2. Authentication verification: magic-link login success/failure/expiry/one-time-use cases, plus Google first-login and account-linking scenarios.
3. Web verification: Client CRUD lifecycle with validation errors, pagination, and permissions.
4. API verification: /api/v1 Client CRUD parity with web business rules, standardized errors, and rate limit responses.
5. CI/CD verification: GitHub Actions passes for PR (lint/test/build), staging deployment triggers from develop, and production deployment triggers from main with required approval.
6. Environment verification: staging and production use separate secrets/hosts/DBs, and no cross-environment secret reuse is allowed.
7. Deployment verification: dry-run deploy in staging first, validate migrations and smoke tests, then promote to production and test rollback procedure.

**Decisions**

- Confirmed stack: Laravel + Inertia.js + React + TailwindCSS + shadcn/ui + MySQL.
- UI strategy: TailwindCSS for utility styling; shadcn/ui for accessible, composable components owned in the codebase (no version lock-in).
- Hosting target: DreamHost shared host with SSH, Composer, and Node available.
- Repository strategy: GitHub-hosted repository with branch protection and PR checks.
- Branching strategy: develop branch for staging deployments and main branch for production deployments.
- Deployment strategy: GitHub Actions CI/CD with explicit staging and production environments; fallback to manual SSH deploy or server-side git pull if needed.
- Auth: passwordless magic link + Google OAuth; no password login.
- Authorization: Spatie Laravel-Permission with DB-managed multi-role users, admin-managed roles, and seeded permissions for USERS, CLIENTS, and ROLES.
- MVP scope: Client CRUD only.
- Naming: English in code and UI.
- API strategy: build public API in parallel from sprint 1.
- Included now: architecture for future subscriptions, but not full subscriptions feature implementation.
- Excluded from MVP: billing/invoicing, advanced analytics dashboard, full subscription lifecycle.

**Further Considerations**

1. Auth hardening recommendation: add optional domain allowlist for Google sign-in if this app is organization-restricted.
2. Data governance recommendation: define soft-delete retention period early to simplify compliance and restore workflows.
3. API lifecycle recommendation: freeze v1 response contracts once mobile development starts, then evolve with additive changes only.
