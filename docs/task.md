# MYLO — MASTER TASK AND IMPLEMENTATION SPECIFICATION

**Project:** Mylo  
**Product:** Premium Multi-Author Digital Magazine, Independent Publishing Platform and Book Marketplace  
**Architecture:** Three Independent Portals + One Shared Laravel Backend  
**Development Approach:** Modular Monolith, API-First, Incremental Implementation  
**Completion Authority:** Acceptance criteria per stage are the sole authority for marking a stage complete and proceeding to the next stage.

---

# 1. PROJECT OBJECTIVE

Build Mylo, a premium multi-author digital magazine and independent publishing ecosystem bringing together readers, independent writers, publishers, photographers, storytellers, and knowledge creators.

The platform must provide three independent application environments:

1. Reader Portal.
2. Writer Portal.
3. Administrator Portal.

All three environments must communicate with one centralized Laravel REST API backend and share the same underlying business logic and database.

The primary objective is to create a content-first publishing destination where:

- Readers discover and consume interesting material.
- Writers create, publish, distribute, and monetize their work.
- Administrators govern and manage the entire publishing ecosystem.

Mylo must not become a conventional social networking platform.

---

# 2. PROJECT ENVIRONMENT

## 2.1 Available Development Environment

- [x] Project directory created: `mylo`.
- [x] Project environment inspected.
- [x] PHP 8.2 available.
- [x] Composer 2.8 available.
- [x] Node.js 24 available.
- [x] PostgreSQL 17 available.
- [x] Redis available in environment but excluded from initial implementation requirements.

## 2.2 Infrastructure Decisions

- [x] PostgreSQL is the primary database.
- [x] Redis is not required initially.
- [x] Use Laravel database/file cache as appropriate.
- [x] Use Laravel database or synchronous queue processing initially.
- [ ] Keep caching and queue implementations replaceable so Redis can be introduced later.
- [ ] Document external dependencies and environment requirements.

Do not introduce Redis as a mandatory dependency.

---

# 3. TECHNOLOGY STACK

## Frontend

- [ ] Next.js.
- [ ] React.
- [ ] TypeScript.
- [ ] Tailwind CSS.
- [ ] TanStack Query.
- [ ] Shared component and design system.

## Backend

- [ ] Laravel REST API.
- [ ] Modular monolithic architecture.
- [ ] API versioning.
- [ ] Service-oriented business logic.
- [ ] Request validation.
- [ ] API resources and consistent response formats.
- [ ] Policies and permission enforcement.
- [ ] Background processing architecture.

## Database and Search

- [ ] PostgreSQL.
- [ ] Meilisearch integration.
- [ ] Database migrations.
- [ ] Seeders and factories.
- [ ] Database indexes and constraints.

## Media and Infrastructure

- [ ] Configurable media storage.
- [ ] S3-compatible storage readiness.
- [ ] Local development storage.
- [ ] Image optimization.
- [ ] CDN readiness.
- [ ] Error logging.
- [ ] Environment-based configuration.

## Mobile Readiness

- [ ] API-first architecture.
- [ ] Mobile-compatible authentication.
- [ ] Consistent API contracts.
- [ ] Responsive media delivery.
- [ ] Documentation for future React Native integration.

---

# 4. THREE-PORTAL ARCHITECTURE

## 4.1 Reader Portal

- [ ] Independent reader registration.
- [ ] Independent reader authentication.
- [ ] Reader homepage.
- [ ] Content discovery.
- [ ] Personalized recommendations.
- [ ] Article reading interface.
- [ ] Category browsing.
- [ ] Search.
- [ ] Likes.
- [ ] Comments.
- [ ] Comment replies.
- [ ] Bookmarks.
- [ ] Reading history.
- [ ] Books marketplace.
- [ ] Shopping cart.
- [ ] Checkout.
- [ ] Personal digital library.
- [ ] Writer contributions.
- [ ] Notification preferences.
- [ ] Reader account settings.

**Acceptance criteria:**

A reader must be able to register, configure interests, discover relevant content, read articles, engage with content, purchase books, access purchased ebooks, and support writers without accessing the writer or administrator workspace.

## 4.2 Writer Portal

- [ ] Independent writer registration.
- [ ] Independent writer authentication.
- [ ] Open registration and publishing.
- [ ] Writer dashboard.
- [ ] Article management.
- [ ] Block-based article editor.
- [ ] Draft management.
- [ ] Autosave.
- [ ] Scheduled publishing.
- [ ] Media library.
- [ ] Image galleries.
- [ ] Category and tag selection.
- [ ] Article previews.
- [ ] Publishing analytics.
- [ ] Comment management.
- [ ] Book listings.
- [ ] Ebook listings.
- [ ] Financial support configuration.
- [ ] Earnings dashboard.
- [ ] Payout management interface.
- [ ] Writer account settings.

**Acceptance criteria:**

A newly registered writer must be able to independently create an account, publish content without manual approval, manage articles and media, list books, activate financial support, and monitor engagement and earnings.

## 4.3 Administrator Portal

- [ ] Independent administrator authentication.
- [ ] Invitation-only administrator creation.
- [ ] Administrator dashboard.
- [ ] Reader management.
- [ ] Writer management.
- [ ] Article management.
- [ ] Editorial management.
- [ ] Category management.
- [ ] Tag management.
- [ ] Comment moderation.
- [ ] Media management.
- [ ] Marketplace management.
- [ ] Order management.
- [ ] Financial management.
- [ ] Transaction management.
- [ ] Payout management.
- [ ] Refund management.
- [ ] Payment gateway configuration.
- [ ] Homepage configuration.
- [ ] Notification management.
- [ ] Analytics and reporting.
- [ ] Administrator management.
- [ ] Roles and permissions.
- [ ] Audit logs.
- [ ] Platform settings.

**Acceptance criteria:**

An authorized administrator must be able to manage users, content, editorial selections, marketplace operations, financial activities, administrative permissions, and platform settings without directly modifying the database.

All sensitive administrative actions must be permission-controlled and auditable.

---

# 5. IDENTITY AND AUTHENTICATION

- [ ] Reader identities stored separately from writer identities.
- [ ] Administrator identities independently managed.
- [ ] Separate registration interfaces.
- [ ] Separate login entry points.
- [ ] Email verification.
- [ ] Password recovery.
- [ ] Secure session handling.
- [ ] Backend authorization.
- [ ] Route protection.
- [ ] Invitation-only administrator creation.
- [ ] Open writer registration.
- [ ] Open reader registration.
- [ ] No automatic account conversion.

**Acceptance test:**

Attempt to access each portal with credentials from another account type. Unauthorized access must be rejected by the backend.

---

# 6. CONTENT PUBLISHING ENGINE

- [ ] Block-based article editor.
- [ ] Heading blocks.
- [ ] Paragraph blocks.
- [ ] Image blocks.
- [ ] Image galleries.
- [ ] Full-width images.
- [ ] Image captions.
- [ ] Image attribution.
- [ ] Pull quotes.
- [ ] Lists.
- [ ] Tables.
- [ ] Video embeds.
- [ ] Audio embeds.
- [ ] Hyperlinks.
- [ ] Callout blocks.
- [ ] Related articles.
- [ ] Image reordering.
- [ ] Image optimization.
- [ ] Alternative text.
- [ ] Autosave.
- [ ] Drafts.
- [ ] Preview.
- [ ] Scheduled publishing.
- [ ] Editing history.
- [ ] SEO metadata.
- [ ] Reading-time estimation.

**Acceptance criteria:**

A writer must be able to create a visually rich article containing multiple content blocks and images, save it, preview it, publish it, and have it render correctly in the reader portal.

Image uploads must have configurable size and storage limits rather than an arbitrary unlimited upload policy.

---

# 7. TAXONOMY AND DISCOVERY

- [ ] Main categories.
- [ ] Subcategories.
- [ ] Tags.
- [ ] Multiple tags per article.
- [ ] Category descriptions.
- [ ] Category imagery.
- [ ] Featured categories.
- [ ] Administrator category management.
- [ ] Meilisearch integration.
- [ ] Search suggestions.
- [ ] Search filters.
- [ ] Search pagination.

**Acceptance criteria:**

Administrators must be able to create new categories and tags without code modifications. Readers must be able to discover articles using search, categories, and tags.

---

# 8. PERSONALIZATION

- [ ] Preference onboarding.
- [ ] Category selection.
- [ ] Topic selection.
- [ ] Preference editing.
- [ ] Personalized homepage.
- [ ] Preference-based recommendations.
- [ ] Reading history.
- [ ] Bookmark-based recommendations.
- [ ] Reading behaviour tracking.
- [ ] Editorially featured content.
- [ ] Recommendation controls.

**Acceptance criteria:**

Two readers with different preferences should receive appropriately different recommended content, while editorially featured content remains discoverable.

---

# 9. ENGAGEMENT

- [ ] Article likes.
- [ ] Comments.
- [ ] Comment replies.
- [ ] Bookmarks.
- [ ] External sharing.
- [ ] Content reporting.
- [ ] Writer comment responses.
- [ ] Administrator moderation.

**Explicit exclusions:**

- [x] No followers.
- [x] No following.
- [x] No friend requests.
- [x] No social connections.
- [x] No follower counts.
- [x] No social popularity rankings.

**Acceptance criteria:**

Readers can engage with published content without creating social connections or following writers.

---

# 10. FINANCIAL SUPPORT AND PAYMENT SYSTEM

## Writer Support

- [ ] Optional writer support activation.
- [ ] One-time contributions.
- [ ] Custom contribution amounts.
- [ ] Suggested contribution amounts.
- [ ] Recurring monthly contributions.
- [ ] Article-specific contributions.
- [ ] Contribution history.
- [ ] Receipts.
- [ ] Recurring contribution cancellation.

## Payment Integrations

- [ ] Payment gateway abstraction.
- [ ] M-Pesa integration.
- [ ] International card payment integration.
- [ ] Sandbox environments.
- [ ] Production configuration.
- [ ] Payment verification.
- [ ] Webhook processing.
- [ ] Idempotency.
- [ ] Failed transaction handling.
- [ ] Reconciliation.

## Financial Administration

- [ ] Platform commissions.
- [ ] Writer earnings.
- [ ] Available balances.
- [ ] Pending balances.
- [ ] Payout requests.
- [ ] Payout approval.
- [ ] Payout processing.
- [ ] Refund management.
- [ ] Financial ledger.
- [ ] Transaction audit records.

**Acceptance criteria:**

Every financial transaction must have a traceable reference, verified status, and corresponding ledger entry. No frontend-only payment confirmation may be treated as a completed transaction.

Recurring contributions must have a clearly defined payment provider and billing workflow.

---

# 11. BOOKS AND EBOOK MARKETPLACE

## Products

- [ ] Physical books.
- [ ] Ebooks.
- [ ] Digital publications.
- [ ] Guides and educational materials.
- [ ] Product categories.
- [ ] Book covers.
- [ ] Product descriptions.
- [ ] Pricing.
- [ ] Previews.
- [ ] Writer ownership.

## Shopping

- [ ] Shopping cart.
- [ ] Checkout.
- [ ] Order creation.
- [ ] Payment verification.
- [ ] Purchase history.
- [ ] Order status.

## Digital Products

- [ ] Protected ebook access.
- [ ] Personal digital library.
- [ ] Download permissions.
- [ ] In-browser reading option.
- [ ] Secure file delivery.

## Physical Products

- [ ] Inventory.
- [ ] Shipping information.
- [ ] Order processing.
- [ ] Delivery status.
- [ ] Refunds.
- [ ] Cancellations.

**Acceptance criteria:**

A reader must be able to purchase a physical or digital book. Successful ebook purchases must grant access only to the appropriate purchaser. Physical purchases must generate manageable orders.

---

# 12. ADMINISTRATOR CONTROL CENTRE

## Dashboard

- [ ] User statistics.
- [ ] Publishing statistics.
- [ ] Engagement statistics.
- [ ] Marketplace statistics.
- [ ] Financial statistics.
- [ ] Date filtering.
- [ ] Trend charts.
- [ ] Drill-down reports.

## User Management

- [ ] Reader management.
- [ ] Writer management.
- [ ] Account search.
- [ ] Account filtering.
- [ ] Account suspension.
- [ ] Account reinstatement.
- [ ] Account restrictions.
- [ ] Account activity.

## Editorial

- [ ] Article oversight.
- [ ] Article featuring.
- [ ] Homepage curation.
- [ ] Scheduled editorial content.
- [ ] Article reports.
- [ ] Content removal.
- [ ] Revision history.

## Moderation

- [ ] Comment oversight.
- [ ] Report management.
- [ ] Spam management.
- [ ] Media reports.
- [ ] User restrictions.
- [ ] Moderation history.

## Marketplace

- [ ] Book management.
- [ ] Listing moderation.
- [ ] Inventory oversight.
- [ ] Order management.
- [ ] Refund handling.
- [ ] Marketplace disputes.

## Finance

- [ ] Transaction overview.
- [ ] Platform commissions.
- [ ] Writer earnings.
- [ ] Payouts.
- [ ] Refunds.
- [ ] Reconciliation.
- [ ] Financial exports.

## Permissions

- [ ] Super Administrator.
- [ ] Editorial Administrator.
- [ ] Community Moderator.
- [ ] Marketplace Manager.
- [ ] Finance Administrator.
- [ ] Analytics Administrator.
- [ ] Configurable permissions.

## Security

- [ ] Administrator audit logs.
- [ ] Permission checks.
- [ ] Secure authentication.
- [ ] Sensitive action tracking.
- [ ] Financial action traceability.

---

# 13. DESIGN SYSTEM

- [ ] Deep burgundy primary background.
- [ ] Dark maroon surfaces.
- [ ] Wine accent colours.
- [ ] Champagne highlights.
- [ ] Soft ivory text.
- [ ] Charcoal backgrounds.
- [ ] Elegant editorial typography.
- [ ] Readable article typography.
- [ ] Subtle textured backgrounds.
- [ ] Gradient treatments.
- [ ] Responsive image layouts.
- [ ] Consistent spacing.
- [ ] Responsive navigation.
- [ ] Accessible colour contrast.
- [ ] Reduced-motion support.
- [ ] Mobile-responsive layouts.

The reader portal must prioritize visual elegance and reading comfort.

The writer portal must prioritize productivity and publishing simplicity.

The administrator portal must prioritize operational efficiency and information clarity.

---

# 14. DEVELOPMENT STAGES AND GATES

Each stage must have an explicit completion gate.

**Rule:** A stage cannot be marked complete merely because its files or UI components have been created. Acceptance criteria are the authority for completion and for moving on to the next stage.

## Stage 1: Discovery and Architecture

- [x] Product specification established.
- [x] Technology decisions documented.
- [x] Environment inspected.
- [x] Initial infrastructure constraints established.
- [x] Complete architecture diagram.
- [x] Database ERD.
- [x] Database schema plan.
- [x] Three-portal architecture.
- [x] Authentication and identity model.
- [x] Permission matrix.
- [x] API contract specification.
- [x] Reader user journey.
- [x] Writer user journey.
- [x] Administrator user journey.
- [x] Payment architecture.
- [x] Development roadmap.

**Gate:** Architecture and database design documented and reviewed before feature implementation. GATE MET — Stage 1 complete.

## Stage 2: Foundation and Shared Backend

- [ ] Laravel initialized.
- [ ] PostgreSQL connected.
- [ ] Environment configuration.
- [ ] API versioning.
- [ ] Database migrations.
- [ ] Identity models.
- [ ] Authentication.
- [ ] Authorization.
- [ ] Permissions.
- [ ] Shared services.
- [ ] API documentation.
- [ ] Automated foundation tests.

**Gate:** Backend starts successfully, database migrations run, authentication works, and permissions are tested.

## Stage 3: Three Portal Foundations

- [ ] Reader frontend initialized.
- [ ] Writer frontend initialized.
- [ ] Administrator frontend initialized.
- [ ] Shared design system.
- [ ] Independent layouts.
- [ ] Independent navigation.
- [ ] Authentication integrations.
- [ ] API connectivity.
- [ ] Protected routes.
- [ ] Responsive layouts.

**Gate:** All three portals run independently and communicate with the same backend.

## Stage 4: Administrator Core

- [ ] Administrator dashboard.
- [ ] Reader management.
- [ ] Writer management.
- [ ] Basic content management.
- [ ] Category management.
- [ ] Permissions.
- [ ] Audit logs.
- [ ] Platform configuration.

**Gate:** Administrators can manage the initial platform through the UI without direct database intervention.

## Stage 5: Publishing Engine

- [ ] Article editor.
- [ ] Block storage and rendering.
- [ ] Media uploads.
- [ ] Article management.
- [ ] Categories and tags.
- [ ] Drafts.
- [ ] Publishing.
- [ ] Scheduling.
- [ ] Search.
- [ ] Editorial controls.

**Gate:** Writers can publish fully rendered articles, and administrators can manage editorial content.

## Stage 6: Reader Experience

- [ ] Reader homepage.
- [ ] Preference onboarding.
- [ ] Recommendations.
- [ ] Article discovery.
- [ ] Likes.
- [ ] Comments.
- [ ] Bookmarks.
- [ ] Reading history.
- [ ] Notifications.

**Gate:** Readers can independently discover, consume, and engage with published material.

## Stage 7: Financial Ecosystem

- [ ] Contribution system.
- [ ] One-time payments.
- [ ] Recurring support.
- [ ] M-Pesa.
- [ ] International cards.
- [ ] Financial ledger.
- [ ] Writer earnings.
- [ ] Payouts.
- [ ] Refunds.
- [ ] Financial administration.

**Gate:** Payment workflows pass sandbox tests and financial records reconcile correctly.

## Stage 8: Marketplace

- [ ] Product listings.
- [ ] Physical books.
- [ ] Ebooks.
- [ ] Cart.
- [ ] Checkout.
- [ ] Order management.
- [ ] Inventory.
- [ ] Digital library.
- [ ] Protected downloads.
- [ ] Marketplace administration.

**Gate:** Physical and digital purchase workflows operate correctly, including access control and order tracking.

## Stage 9: Advanced Administration

- [ ] Advanced analytics.
- [ ] Reporting.
- [ ] Exports.
- [ ] Advanced moderation.
- [ ] Editorial scheduling.
- [ ] Payment reconciliation.
- [ ] Operational monitoring.

**Gate:** Authorized administrators can operate and monitor the complete platform.

## Stage 10: Optimization and Mobile Preparation

- [ ] Performance testing.
- [ ] Security testing.
- [ ] API review.
- [ ] Automated regression tests.
- [ ] Documentation.
- [ ] Backup strategy.
- [ ] Deployment instructions.
- [ ] Mobile integration documentation.

**Gate:** All critical workflows pass testing and the system is deployment-ready.

---

# 15. TESTING AND ACCEPTANCE REQUIREMENTS

Every major module must have tests.

## Backend tests

- [ ] Registration.
- [ ] Authentication.
- [ ] Identity separation.
- [ ] Authorization.
- [ ] Article creation.
- [ ] Article publishing.
- [ ] Media uploads.
- [ ] Categories.
- [ ] Comments.
- [ ] Recommendations.
- [ ] Contributions.
- [ ] Recurring payments.
- [ ] Payment verification.
- [ ] Payouts.
- [ ] Refunds.
- [ ] Book purchases.
- [ ] Ebook access.
- [ ] Physical orders.
- [ ] Administrator operations.

## Frontend tests

- [ ] Reader registration.
- [ ] Writer registration.
- [ ] Administrator login.
- [ ] Navigation.
- [ ] Article rendering.
- [ ] Article editor.
- [ ] Image galleries.
- [ ] Preferences.
- [ ] Search.
- [ ] Checkout.
- [ ] Writer dashboard.
- [ ] Administrator dashboard.

## Integration tests

- [ ] Reader and writer portal integration.
- [ ] All portals using shared API.
- [ ] Backend permission enforcement.
- [ ] Payment provider callbacks.
- [ ] Marketplace transaction consistency.
- [ ] Digital access permissions.
- [ ] Administrative audit logging.

**Completion rule:** A feature is not complete until its core functionality works, relevant tests pass, and its documentation is updated.

---

# 16. CURRENT STAGE STATUS

**Active Stage:** Stage 1 — Discovery and Architecture  
**Overall Progress:** Stage 1 in progress (documentation authored; architecture, API contracts, DB schema drafted)

See `docs/progress.md` for the live stage-tracking record.
