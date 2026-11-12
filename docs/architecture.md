# SYSTEM ARCHITECTURE

## High-level architecture (modular monolith)

```
┌─────────────────────────────────────────────────────────────┐
│                      CLIENT LAYER                           │
├──────────────┬──────────────┬───────────────────────────────┤
│ Reader Portal│ Writer Portal│ Administrator Portal          │
│ (Next.js)    │ (Next.js)    │ (Next.js)                     │
│ TanStack Query│ TanStack Query│ TanStack Query               │
└──────┬───────┴──────┬───────┴──────────────┬───────────────┘
       │              │                      │
       └──────────────┴──────────────────────┘
                      │ HTTPS / JSON API (v1)
                      ▼
┌─────────────────────────────────────────────────────────────┐
│                      API GATEWAY (Laravel)                  │
│  Sanctum auth | Rate limiting | CORS | API versioning      │
└──────────────┬──────────────┬───────────────────────────────┘
               │              │
       ┌───────▼───────┐ ┌────▼────────┐
       │ Controllers   │ │ Resources │
       │ (thin)        │ │ (API)     │
       └───────┬───────┘ └──────┬─────┘
               │                │
               ▼                ▼
       ┌──────────────────────────────────┐
       │      Service Layer (business)    │
       │  Publishing · Identity · Finance │
       │  Marketplace · Admin · Notify ·  │
       │  Search · Media · Payment        │
       └──────────────┬───────────────────┘
                      │
       ┌──────────────▼───────────────┐
       │  Policies / Gates (authz)    │
       │  Events · Listeners · Jobs   │
       └──────────────┬───────────────┘
                      │
       ┌──────────────▼───────────────┐
       │  Eloquent Models + Migrations│
       └──────────────┬───────────────┘
                      │
       ┌──────────────▼───────────────┐
       │  PostgreSQL 17 (primary DB)  │
       │  (Redis optional: cache/queue│
       │   drop per user instruction) │
       └──────────────┬───────────────┘
                      │
       ┌──────────────▼───────────────┐
       │  S3-compatible storage + CDN │
       │  Meilisearch (search index)  │
       │  Email service               │
       │  Payment gateways (M-Pesa,   │
       │   Stripe/cards)              │
       └──────────────────────────────┘
```

## Module boundaries (backend)

Each module contains: `Controllers/`, `Services/`, `Models/`, `Policies/`, `Resources/`, `Jobs/`, `Events/`, `Notifications/`, `Migrations/`, `Factories/`, `Seeders/`.

| Module | Responsibility |
|--------|----------------|
| Identity | Readers, writers, admins, profiles, roles, permissions, auth, sessions, MFA |
| Publishing | Articles, blocks, revisions, categories, subcategories, tags, media, scheduling |
| Finance | Contributions, recurring support, transactions, earnings, payouts, refunds, ledger |
| Marketplace | Books, ebooks, products, inventory, orders, order items, digital access |
| Admin | Dashboard, user management, moderation, config, roles, audit logs |
| Search | Meilisearch indexing, search service |
| Media | Upload, optimization, storage, signed URLs |
| Notify | Notifications, preferences, email jobs |
| Payment | Gateway integrations, webhooks, reconciliation |

## Portal boundaries

Three separate Next.js apps, each consuming the same Laravel API:

- `frontend/reader` — reader.mylo.test — content discovery & reading
- `frontend/writer` — writer.mylo.test — publishing workspace
- `frontend/admin` — admin.mylo.test — platform control

Shared: `frontend/design-system` — Tailwind components, palette, typography.

Authorization enforced at backend policy level; frontends never bypass.
