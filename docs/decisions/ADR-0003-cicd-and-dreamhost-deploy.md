# ADR-0003: CI/CD and DreamHost Deployment Flow

- Status: Accepted
- Date: 2026-04-28

## Context

The application needs automated quality checks and environment-specific deployments for DreamHost-compatible hosting.

## Decision

Adopt GitHub Actions with three workflows:

- `ci.yml`: lint/test/build for pull requests and pushes.
- `deploy-staging.yml`: auto deploy on `develop` to staging environment.
- `deploy-production.yml`: deploy on `main` to production environment.

Use SSH + rsync for code sync and a remote release script for non-interactive Laravel release tasks.

## Consequences

- Consistent delivery pipeline across staging and production.
- Requires proper GitHub environment secrets and protected production approvals.
- Release behavior stays centralized in `scripts/deploy/release.sh`.

## Notes

- Build artifacts are generated in CI before deployment.
- Remote release performs migrations and Laravel cache warmup.
