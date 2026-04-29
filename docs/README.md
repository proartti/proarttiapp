# Documentation Map

This folder stores implementation decisions, infrastructure details, and execution logs for the DreamHost-ready SaaS foundation.

## Structure

- `plan-dreamHostReadySaasFoundation.md`: Master implementation plan and phase sequencing.
- `decisions/`: Architecture Decision Records (ADRs).
- `operations/`: Environment facts, deployment constraints, and operational checklists.
- `logs/`: Chronological implementation log as work advances.

## Working Rules

1. Record every material architecture or deployment decision in an ADR file.
2. Keep server and environment facts updated in `operations/server-inventory.md`.
3. Add one log entry per meaningful implementation step in `logs/implementation-log.md`.
4. Never store secrets in docs; only document secret names and locations.
