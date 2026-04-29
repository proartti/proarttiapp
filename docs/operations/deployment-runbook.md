# Deployment Runbook

Last updated: 2026-04-28

## Workflows

- CI: `.github/workflows/ci.yml`
- Staging deploy: `.github/workflows/deploy-staging.yml`
- Production deploy: `.github/workflows/deploy-production.yml`

## Required GitHub Environment Secrets

- `SSH_PRIVATE_KEY`
- `DEPLOY_HOST`
- `DEPLOY_USER`
- `DEPLOY_PORT`
- `DEPLOY_PATH`

## Release Script

Remote command executed by deploy jobs:

```bash
bash scripts/deploy/release.sh <environment>
```

Current script actions:

1. Put app in maintenance mode.
2. Install production PHP dependencies.
3. Run database migrations (`--force`).
4. Warm Laravel caches.
5. Bring app back online.

## Pending Before First Live Deploy

- Confirm staging host/path and production host/path values.
- Confirm `.env` handling strategy on server (kept on host, not deployed from git).
- Confirm remote PHP binary path and version at deploy target.
- Configure GitHub environment protection rules for production approvals.
- Validate rollback strategy for failed migration or bad release.
