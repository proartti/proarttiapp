# Copilot Instructions — proarttiapp

## Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Inertia.js v2 + React 18, Tailwind CSS v3, shadcn/ui (Radix UI)
- **Auth**: Passwordless magic-link + Google OAuth, Laravel Sanctum (API tokens)
- **Authorization**: Spatie `laravel-permission` (RBAC — roles: `administrator`, `client`)
- **Testing**: PHPUnit 11, Laravel feature/unit tests
- **Code style**: Laravel Pint (PSR-12 + Laravel presets)
- **Routes**: Ziggy for frontend route helpers; web routes and API routes (`/api/v1/`) are strictly separated
- **Hosting**: DreamHost shared hosting, MySQL 8

---

## Code Quality Requirements

### Always include tests

Every new feature, controller method, service, or policy change **must** be accompanied by tests:

- **Feature tests** for HTTP endpoints (web and API), covering: happy path, authorization (unauthenticated, forbidden role), and validation errors.
- **Unit tests** for services, policies, domain rules, and utility logic.
- Place feature tests in `tests/Feature/`, unit tests in `tests/Unit/`.
- Use `RefreshDatabase` in feature tests. Seed RBAC via `$this->seed(RolesAndPermissionsSeeder::class)` when roles/permissions matter.
- Use factories for all test data; never hard-code IDs.
- Test both the web surface and the matching `/api/v1/` endpoint when both exist.

Example test structure:

```php
class ClientWebTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('administrator');
    }
}
```

### Always update documentation

When a feature, route, model, permission, or deployment procedure changes:

- Update the relevant file in `docs/` (plan, ADRs, implementation log, runbook, env-secrets catalog).
- If a new architectural decision is made, create a new ADR in `docs/decisions/`.
- Log significant implementation milestones in `docs/logs/implementation-log.md`.
- Keep `docs/operations/env-secrets-catalog.md` current when new environment variables are introduced.

---

## PHP / Laravel Conventions

### General

- All comments, docblocks, variable names, and commit messages **must be in English**.
- Follow PSR-12. Run `composer pint` before committing.
- Use strict types: `declare(strict_types=1);` at the top of every PHP file.
- Domain terms in code and UI use **English** (e.g., `Client`, `User`, `Role`).
- All database timestamps are stored in **UTC**.
- Use explicit soft-delete per-entity (not globally); document the decision when adding `SoftDeletes`.

### Controllers

- Keep controllers thin: delegate business logic to Service classes in `app/Services/`.
- Always call `$this->authorize()` at the top of every controller method that reads or mutates data.
- Use dedicated `FormRequest` classes for all validation — never validate inline in controllers.
- Return typed responses: `Inertia\Response` for web, `Illuminate\Http\JsonResponse` for API.
- Web controllers render Inertia pages; API controllers return JSON using API Resources.

```php
// Web controller method pattern
public function store(StoreClientRequest $request): RedirectResponse
{
    $this->authorize('create', Client::class);
    // delegate to service...
    return redirect()->route('clients.index');
}

// API controller method pattern
public function store(StoreClientRequest $request): JsonResponse
{
    $this->authorize('create', Client::class);
    $client = $this->clientService->create($request->validated());
    return response()->json(new ClientResource($client), 201);
}
```

### Models

- Define `$fillable` explicitly — never use `$guarded = []`.
- Cast types explicitly via `$casts`.
- Keep Eloquent relationships, scopes, and accessors in the model; move any non-trivial query logic to a Repository or Service.
- Add a factory for every new model.

### API

- All API routes live under `/api/v1/` prefix.
- Return consistent JSON envelopes: `{ "data": ..., "meta": ..., "message": ... }`.
- Use API Resource classes (`app/Http/Resources/`) for all API responses — never return raw model instances.
- Apply rate limiting to all public API routes.

### Migrations

- Migrations are **forward-only** and **idempotent** — never edit an existing migration.
- Column names use `snake_case`.
- Always add foreign key constraints.
- Add database-level `NOT NULL` constraints where applicable.

### Authorization

- Every resource must have a Policy class in `app/Policies/`.
- Register policies in `app/Providers/AppServiceProvider.php`.
- Use `viewAny`, `view`, `create`, `update`, `delete` as standard policy method names.

---

## Frontend Conventions (React / Inertia / Tailwind)

### General

- All components, props, and variable names in **English**.
- Components live in `resources/js/` — Pages in `resources/js/Pages/`, shared UI in `resources/js/components/ui/` (shadcn/ui owned source files).
- Use Ziggy's `route()` helper for all route references — never hard-code URLs.
- TypeScript types/interfaces are preferred for Inertia page props.

### Components

- Use shadcn/ui primitives (Radix UI) for interactive elements (dialogs, selects, labels).
- Style exclusively with Tailwind utility classes — no inline styles, no CSS modules.
- Keep components focused; extract reusable logic into custom hooks.

### Forms and Validation

- Use Inertia's `useForm` hook for all form submissions.
- Display server-side validation errors from `errors` prop returned by Inertia.
- Never suppress or silently swallow form errors.

---

## Testing Checklist

Before considering any implementation complete, verify:

- [ ] Feature test covers authenticated happy path
- [ ] Feature test covers unauthenticated redirect (web: `assertRedirect(route('login'))`)
- [ ] Feature test covers forbidden role (`assertForbidden()`)
- [ ] Feature test covers validation errors (`assertInvalid()`)
- [ ] Unit test covers service/policy logic in isolation
- [ ] API endpoint test mirrors the web test (if both surfaces exist)
- [ ] `php artisan test` passes with no failures
- [ ] `composer pint` passes with no style violations

---

## Documentation Checklist

Before considering any implementation complete, verify:

- [ ] `docs/logs/implementation-log.md` updated with what was implemented
- [ ] New env variables added to `docs/operations/env-secrets-catalog.md`
- [ ] New architectural decisions recorded as ADRs in `docs/decisions/`
- [ ] Deployment steps updated in `docs/operations/deployment-runbook.md` if the deploy process changed
- [ ] Route/API surface changes reflected in relevant docs

---

## Security (OWASP Top 10)

- Never trust user input — always validate via `FormRequest` before use.
- Never expose stack traces or internal errors to API consumers — use structured error responses.
- Enforce authorization on every endpoint; rely on Policies, not manual role-string checks.
- Use parameterized queries (Eloquent/query builder) — never raw string interpolation in SQL.
- Secrets live in `.env` only — never commit credentials, tokens, or keys to source control.
- CSRF protection is active for all web routes (Laravel default); do not disable it.
- Sanctum token scopes must be explicitly defined for API consumers.
