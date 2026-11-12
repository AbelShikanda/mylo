# ARCHITECTURAL DECISIONS

## Portal boundaries (spec §2)
Three distinct Next.js applications (can live in one monorepo or separate repos), each with:
- Separate navigation, layouts, authentication entry points, user journeys, access permissions, dashboard functionality, design priorities
- Shared Laravel backend, database, services, business logic
- Backend-level authorization enforcement (not frontend-only hiding)

| Portal | URL pattern | Primary audience | Auth entry | Design priority |
|--------|-------------|------------------|------------|-----------------|
| Reader | `/` (or `reader.mylo.test`) | Readers | `/register`, `/login` (reader) | Editorial elegance, readability |
| Writer | `/writer` (or `writer.mylo.test`) | Writers | `/writer/login` | Content creation, publishing workspace |
| Admin | `/admin` (or `admin.mylo.test`) | Administrators | `/admin/login` | Operational usability, information density |

## Identity & authentication (spec §4)
- Separate identity tables: `readers`, `writers`, `administrators`
- Separate profile tables: `reader_profiles`, `writer_profiles`
- Separate roles/permissions per identity
- No auto-conversion between reader and writer
- Writer registration open; admin registration by invitation/authorized process only
- Email verification, password recovery, session management, rate limiting, optional MFA
- API authentication via Sanctum (SPA) + optional Passport (future mobile)

## Authorization model
- Laravel Gates + Policies per model
- Reader policies, Writer policies, Admin policies
- Admin roles: Super Admin, Editorial Admin, Community Moderator, Marketplace Manager, Finance Admin, Analytics Admin
- Permission matrix stored in `admin_roles` and `admin_permissions` tables with pivot `admin_role_permissions`
- Admin users assigned roles via `admin_user_roles`

## Database (spec §20)
- PostgreSQL 17
- Soft deletes where appropriate (articles, books, comments, users)
- Foreign keys + constraints
- Indexes on frequently queried columns
- Separate financial ledger (append-only, never overwrite balances)
- Idempotency keys for payment transactions

## Storage (spec §3)
- S3-compatible object storage (local MinIO for dev, S3 for prod)
- CDN for media delivery
- Image optimization + responsive sizes + lazy loading
- Protected digital downloads (signed URLs, download permissions)

## Search
- Meilisearch for articles, writers, categories, tags, books, ebooks
- Index on publish events

## Queues / cache (Redis dropped per user instruction)
- Database queue driver for local/dev (swap to Redis in production)
- Database/cache driver for caching (swap to Redis in production)
- Background jobs for emails, image processing, search indexing, payouts

## Payments (spec §11, §13.9)
- M-Pesa (Kenya) prioritized
- International card payments (Stripe or similar)
- Modular payment gateway integrations
- Backend-verified webhooks (never trust frontend-only responses)
- Idempotency, reconciliation, sandbox + production configs
- Platform commission rates configurable

## Design system (spec §16)
- Palette: Deep Burgundy #350E1C, Dark Maroon #501629, Wine #702B43, Muted Rose #A56D7D, Champagne #D8BC9A, Soft Ivory #EDE3D8, Charcoal #171316, Muted Grey #9C9295
- Elegant serif headlines, readable body typography, generous spacing, paper-grain textures, subtle gradients
- No blank black backgrounds; backgrounds never interfere with readability

## Mobile readiness (spec §18)
- API-first; all core functionality consumable via API
- Pagination, optimized media, synchronized preferences/history/bookmarks/purchases
- Same Laravel backend reused by future React Native/Expo app

---

## Decisions log

| ID | Decision | Rationale | Date |
|----|----------|-----------|------|
| D01 | Three separate Next.js portal frontends | Spec §2 requires distinct experiences, not minor visual variants | 2026-11-12 |
| D02 | Separate reader/writer/admin identity tables | Spec §4 — deliberate separation, no auto-conversion | 2026-11-12 |
| D03 | Modular Laravel monolith (not microservices) | Spec §21 — can evolve later | 2026-11-12 |
| D04 | Sanctum for SPA auth, Passport reserved for mobile | Laravel convention; future mobile readiness | 2026-11-12 |
| D05 | Database queue/cache drivers initially (Redis optional) | User instruction to drop Redis requirement; spec allows evolution | 2026-11-12 |
| D06 | Meilisearch for search | Spec §15 | 2026-11-12 |
| D07 | Append-only financial ledger | Spec §13.8 — never overwrite balances without traceable adjustment | 2026-11-12 |
| D08 | S3-compatible storage + CDN | Spec §3 | 2026-11-12 |
| D09 | M-Pesa + international cards | Spec §13.9 — Kenyan and international from start | 2026-11-12 |
