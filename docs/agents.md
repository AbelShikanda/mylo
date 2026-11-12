# AGENT DEFINITIONS

This document defines the specialized agents (subagents) used to implement the Mylo platform, their responsibilities, and the files/tools each owns.

## Agent registry

| Agent ID | Role | Responsibility | Primary files |
|----------|------|----------------|---------------|
| `architect` | System architecture | ERD, schema, API contracts, portal boundaries, infra decisions | `docs/decisions.md`, `docs/goals.md`, `docs/architecture.md`, `docs/api-contracts.md` |
| `backend-foundation` | Laravel backend foundation | Migrations, models, factories, seeders, auth, policies, services, jobs | `backend/` (Laravel app) |
| `backend-publishing` | Publishing engine | Articles, blocks, revisions, categories, tags, media, scheduling, search | `backend/Modules/Publishing/` |
| `backend-identity` | Identity & auth | Readers, writers, admins, profiles, roles, permissions, sessions, MFA | `backend/Modules/Identity/` |
| `backend-finance` | Financial ecosystem | Contributions, transactions, earnings, payouts, refunds, ledger | `backend/Modules/Finance/` |
| `backend-marketplace` | Marketplace | Books, ebooks, products, inventory, orders, digital access | `backend/Modules/Marketplace/` |
| `backend-admin` | Administrator portal API | Dashboard, user mgmt, moderation, config, roles, audit logs | `backend/Modules/Admin/` |
| `frontend-reader` | Reader portal | Next.js app, layouts, navigation, article reading, discovery, books, profile | `frontend/reader/` |
| `frontend-writer` | Writer portal | Next.js app, publishing workspace, editor, media library, earnings, analytics | `frontend/writer/` |
| `frontend-admin` | Admin portal | Next.js app, dashboard, management modules, finance, audit | `frontend/admin/` |
| `frontend-design` | Design system | Shared Tailwind components, palette, typography, responsive utilities | `frontend/design-system/` |
| `qa-testing` | Testing | Feature + unit tests for auth, permissions, publishing, media, finance, marketplace, admin | `tests/` in each frontend + backend |
| `devops-deploy` | Deployment | Docker, CI/CD, environment config, migrations, seeding, monitoring | `deploy/`, `.env.example`, `docker-compose.yml` |

## How agents coordinate
- All agents share the same Laravel backend and PostgreSQL database
- API contracts (`docs/api-contracts.md`) are the interface between backend and frontend agents
- `architect` owns the source of truth for schema and API; changes require architect sign-off
- Frontend agents consume backend APIs via TanStack Query; no duplicated business logic
- Backend agents enforce authorization at the policy level; frontends may not bypass

## Working directory convention
```
/home/abel/Desktop/projects/mylo/
├── backend/          # Laravel monolith
├── frontend/
│   ├── reader/       # Next.js reader portal
│   ├── writer/       # Next.js writer portal
│   ├── admin/        # Next.js admin portal
│   └── design-system/# Shared UI components
├── docs/             # Architecture docs (this folder)
├── deploy/           # Deployment configs
└── tests/            # Cross-cutting tests (optional)
```

## Communication
- Agents post material findings to the shared board (see `board_post`) when they affect peers
- Completion of a stage is reported via `goal_report`
- Blockers are escalated with evidence to the coordinator
