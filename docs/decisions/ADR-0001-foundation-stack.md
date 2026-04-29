# ADR-0001: Foundation Stack and Delivery Model

- Status: Accepted
- Date: 2026-04-28

## Context

The project targets DreamHost hosting and needs fast web delivery while preserving an API-first trajectory for future mobile clients.

## Decision

Adopt a Laravel-first monolith with Inertia.js + React + TailwindCSS + shadcn/ui and MySQL.
Implement web and versioned API endpoints in parallel from sprint 1.

## Consequences

- Faster MVP delivery with one deployable artifact.
- Shared validation and business rules between web and API layers.
- Lower rework when mobile clients are introduced.
- Requires strict route/controller separation between web and API boundaries.

## Notes

- API versioning starts at `/api/v1`.
- English naming in code and UI.
- UTC timestamps in database records.
