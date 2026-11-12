# PROJECT GOALS

## Vision (spec §1, §25)
A sophisticated independent digital magazine and publishing ecosystem where content comes first. Readers discover and enjoy content without navigating a social network. Writers publish, distribute, and monetize without becoming influencers. Administrators operate, curate, moderate, and grow the ecosystem.

The publication is the product. The content is the attraction. The reader is the priority. The writer is the creator. The administrator ensures the ecosystem operates effectively.

---

## Success criteria

### Functional
- [ ] Reader portal: browse articles, search, filter by category, like, comment, reply, bookmark, reading history, purchase books/ebooks, support writers, manage preferences/notifications
- [ ] Writer portal: create/edit articles, drafts, autosave, publish/schedule, categories/tags, media library, books, comments, financial support, earnings, analytics
- [ ] Admin portal: dashboard, user management, editorial management, categories, comment moderation, media, marketplace, orders, finance, transactions, payouts, payment gateways, homepage management, notifications, analytics, admin accounts, roles/permissions, audit logs, platform settings
- [ ] Block-based article editor with all spec §7 block types
- [ ] Preference-driven onboarding + recommendations (spec §9)
- [ ] Financial support: one-time + recurring, M-Pesa + cards, commissions, payouts, refunds
- [ ] Marketplace: physical books + ebooks, cart, checkout, protected downloads, inventory, orders
- [ ] Meilisearch content discovery
- [ ] Centralized notifications with preferences
- [ ] Granular admin roles + permissions + audit logs

### Quality
- [ ] All features genuinely functional (no static dashboards, no hardcoded sample data as production substitute)
- [ ] Backend-level authorization enforcement
- [ ] Payment success verified by backend + provider webhooks
- [ ] Append-only auditable financial ledger
- [ ] Automated tests for auth, permissions, publishing, media, financial transactions, payouts, book purchases, digital access, admin operations
- [ ] Responsive design across all portals
- [ ] API-first; mobile-ready backend

### Non-functional
- [ ] Premium editorial aesthetic (burgundy/wine/champagne/ivory palette, serif headlines, readable body, generous spacing)
- [ ] Performance: fast article loading, image delivery, homepage queries, search, recommendations
- [ ] Security: auth, validation, rate limiting, upload validation, protected downloads, payment security
- [ ] Scalability: modular monolith, indexes, caching, background jobs

---

## Stage goals (spec §21)

### Stage 1: Discovery and Architecture (current)
- System architecture diagram
- Three-portal application architecture
- Database ERD
- Database schema
- User role and permission matrix
- Reader/writer/admin user journeys
- Website sitemap
- API endpoint specification
- Payment architecture
- Financial transaction workflows
- Marketplace workflows
- Editorial workflows
- Development roadmap

### Stage 2: Foundation and Shared Backend
- Laravel project, PostgreSQL, API versioning
- Reader/writer/admin authentication
- Permission system
- Shared backend services, logging, audit infrastructure
- API contracts established before portal integration

### Stage 3: Three Portal Foundations
- Reader/writer/admin layouts and navigation
- Authentication entry points
- Route protection
- Responsive layouts
- Shared design system

### Stage 4: Administrator Core
- Dashboard, user management, writer management, editorial management
- Category management, basic moderation, platform configuration
- Roles and permissions, audit logs

### Stage 5: Publishing Engine
- Article editor, media management, categories, tags
- Publishing workflow, scheduling, article presentation, search
- Admin editorial controls

### Stage 6: Reader Experience
- Preference onboarding, personalized homepage, discovery
- Bookmarks, likes, comments, sharing, reading history, notifications

### Stage 7: Financial Ecosystem
- Writer support, one-time + recurring contributions
- M-Pesa, international cards, transaction records, earnings, payouts, refunds
- Financial administration

### Stage 8: Marketplace
- Physical books, ebooks, product listings, cart, checkout
- Personal library, digital delivery, physical orders, inventory
- Marketplace administration

### Stage 9: Advanced Administration
- Advanced analytics, reports, financial reconciliation
- Advanced moderation, editorial scheduling, operational monitoring

### Stage 10: Optimization and Mobile Preparation
- Performance optimization, security testing, API refinement
- Mobile readiness, automated tests, deployment preparation

---

## Current status
Stage 1 in progress — architecture documents being produced.
