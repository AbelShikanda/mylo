# Mobile Application Readiness Documentation

## Overview

The Mylo platform is designed with an API-first architecture. All core functionality is exposed through a centralized Laravel REST API (`/api/v1/`), which is consumed by three independent Next.js frontends (Reader, Writer, Administrator). The same backend API is intended to serve future mobile applications built with React Native + Expo.

## API Architecture

### Base URL
```
https://api.mylo.test/api/v1/
```

### Authentication
- **Sanctum token-based authentication** for SPA and mobile clients
- Separate guards for `reader`, `writer`, and `admin` identities
- Tokens issued via `/api/v1/auth/{guard}/login`
- Token revocation via `/api/v1/auth/{guard}/logout`
- Token format: `Bearer <plainTextToken>`

### API Versioning
- All endpoints are versioned under `/api/v1/`
- Future versions (`/api/v2/`) can be introduced without breaking v1 clients
- Route groups separate concerns: `auth`, `public`, `reader`, `writer`, `admin`

### Response Format
All API responses follow a consistent envelope:
```json
{
  "success": true,
  "data": { ... },
  "meta": { "pagination": { ... } },
  "errors": [ ... ]
}
```

### Pagination
All list endpoints use Laravel's length-aware paginator with metadata:
```json
{
  "pagination": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 20,
    "total": 195,
    "from": 1,
    "to": 20
  }
}
```

## Authentication Flows

### Reader Registration & Login
1. `POST /api/v1/auth/reader/register` - Create reader account (open registration)
2. `POST /api/v1/auth/reader/login` - Authenticate and receive Sanctum token
3. `POST /api/v1/auth/reader/logout` - Revoke token
4. `POST /api/v1/auth/reader/password/email` - Password reset request
5. `POST /api/v1/auth/reader/password/reset` - Reset password
6. `POST /api/v1/auth/reader/email/verify` - Verify email
7. `POST /api/v1/auth/reader/mfa/enable` - Enable MFA (optional)
8. `POST /api/v1/auth/reader/mfa/verify` - Verify MFA

### Writer Registration & Login
1. `POST /api/v1/auth/writer/register` - Create writer account (open registration, no approval required)
2. `POST /api/v1/auth/writer/login` - Authenticate and receive Sanctum token
3. `POST /api/v1/auth/writer/logout` - Revoke token

### Administrator Login
1. `POST /api/v1/auth/admin/login` - Authenticate (invitation-only account creation)
2. `POST /api/v1/auth/admin/logout` - Revoke token

## Mobile-Ready Endpoints

### Reader Endpoints (Guard: `reader`)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/v1/reader/profile` | GET/PUT | Reader profile management |
| `/api/v1/reader/preferences` | GET/PUT | Content preference management |
| `/api/v1/reader/bookmarks` | GET | List bookmarked articles |
| `/api/v1/reader/bookmarks/{article}/toggle` | POST | Toggle bookmark |
| `/api/v1/reader/bookmarks/{article}/status` | GET | Check bookmark status |
| `/api/v1/reader/articles/{article}/like/toggle` | POST | Toggle article like |
| `/api/v1/reader/articles/{article}/like/status` | GET | Check like status |
| `/api/v1/reader/likes` | GET | List liked articles |
| `/api/v1/reader/reading-history` | GET | List reading history |
| `/api/v1/reader/articles/{article}/reading-history` | POST | Record reading history |
| `/api/v1/reader/notifications` | GET | List notifications |
| `/api/v1/reader/notifications/{id}/read` | PUT | Mark notification as read |
| `/api/v1/reader/contributions` | GET/POST | Contribution history and create contribution |
| `/api/v1/reader/contributions/initiate-payment` | POST | Initiate payment (M-Pesa, cards) |
| `/api/v1/reader/library` | GET | List purchased ebooks |
| `/api/v1/reader/orders` | GET | List orders |
| `/api/v1/reader/orders/{order}` | GET | Get order details |
| `/api/v1/reader/reports` | POST | Submit content report |

### Writer Endpoints (Guard: `writer`)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/v1/writer/profile` | GET/PUT | Writer profile management |
| `/api/v1/writer/articles` | GET/POST | List/create articles |
| `/api/v1/writer/articles/{id}` | GET/PUT/DELETE | Get/update/delete article |
| `/api/v1/writer/articles/{id}/publish` | POST | Publish article |
| `/api/v1/writer/articles/{id}/schedule` | POST | Schedule article |
| `/api/v1/writer/articles/{id}/autosave` | POST | Autosave article |
| `/api/v1/writer/drafts` | GET | List drafts |
| `/api/v1/writer/scheduled` | GET | List scheduled articles |
| `/api/v1/writer/media` | GET/POST | Media library |
| `/api/v1/writer/media/{id}` | DELETE | Delete media |
| `/api/v1/writer/books` | GET/POST | List/create books |
| `/api/v1/writer/books/{id}` | GET/PUT/DELETE | Get/update/delete book |
| `/api/v1/writer/comments` | GET/PUT | Manage comments |
| `/api/v1/writer/support/activate` | POST | Activate financial support |
| `/api/v1/writer/support/deactivate` | POST | Deactivate financial support |
| `/api/v1/writer/support/recurring` | POST | Create recurring support |
| `/api/v1/writer/support/recurring/{id}` | DELETE | Cancel recurring support |
| `/api/v1/writer/earnings` | GET | View earnings |
| `/api/v1/writer/earnings/payout-request` | POST | Request payout |
| `/api/v1/writer/earnings/payouts` | GET | List payouts |

### Public Endpoints (No authentication required)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/v1/articles` | GET | List published articles |
| `/api/v1/articles/{slug}` | GET | Get article by slug |
| `/api/v1/categories` | GET | List categories |
| `/api/v1/categories/{slug}` | GET | Get category by slug |
| `/api/v1/tags` | GET | List tags |
| `/api/v1/search?q=...` | GET | Search articles and books |
| `/api/v1/books` | GET | List books/ebooks |
| `/api/v1/books/{slug}` | GET | Get book by slug |

### Cart & Checkout (Guard: `reader`)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/v1/cart` | GET | View cart |
| `/api/v1/cart/items` | POST | Add item to cart |
| `/api/v1/cart/items/{item}` | DELETE | Remove item from cart |
| `/api/v1/checkout` | POST | Create order from cart |

### Administrator Endpoints (Guard: `admin`)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/v1/admin/dashboard/stats` | GET | Dashboard statistics |
| `/api/v1/admin/readers` | GET | List readers |
| `/api/v1/admin/writers` | GET | List writers |
| `/api/v1/admin/users/{id}/suspend` | PUT | Suspend user |
| `/api/v1/admin/users/{id}/reinstate` | PUT | Reinstate user |
| `/api/v1/admin/articles` | GET | List all articles |
| `/api/v1/admin/articles/{id}/feature` | PUT | Feature article |
| `/api/v1/admin/articles/{id}/unfeature` | PUT | Unfeature article |
| `/api/v1/admin/articles/{id}` | DELETE | Delete article |
| `/api/v1/admin/categories` | GET/POST | List/create categories |
| `/api/v1/admin/categories/{id}` | PUT/DELETE | Update/delete category |
| `/api/v1/admin/comments` | GET | List comments |
| `/api/v1/admin/comments/{id}` | DELETE | Delete comment |
| `/api/v1/admin/media` | GET | List media |
| `/api/v1/admin/media/{id}` | DELETE | Delete media |
| `/api/v1/admin/books` | GET | List books |
| `/api/v1/admin/orders` | GET | List orders |
| `/api/v1/admin/finance/overview` | GET | Financial overview |
| `/api/v1/admin/finance/transactions` | GET | List transactions |
| `/api/v1/admin/finance/earnings` | GET | List earnings |
| `/api/v1/admin/finance/payouts` | GET | List payouts |
| `/api/v1/admin/finance/payouts/{id}/approve` | POST | Approve payout |
| `/api/v1/admin/finance/payouts/{id}/reject` | POST | Reject payout |
| `/api/v1/admin/finance/refunds` | GET | List refunds |
| `/api/v1/admin/finance/refunds/{id}/approve` | POST | Approve refund |
| `/api/v1/admin/finance/refunds/{id}/reject` | POST | Reject refund |
| `/api/v1/admin/payment-gateways` | GET/PUT | Manage payment gateways |
| `/api/v1/admin/homepage/sections` | GET/POST | Manage homepage sections |
| `/api/v1/admin/homepage/sections/{id}` | PUT | Update homepage section |
| `/api/v1/admin/notifications` | GET | List notifications |
| `/api/v1/admin/analytics/reports` | GET | Analytics reports |
| `/api/v1/admin/administrators` | GET | List administrators |
| `/api/v1/admin/roles` | GET | List roles |
| `/api/v1/admin/permissions` | GET | List permissions |
| `/api/v1/admin/audit-logs` | GET | List audit logs |
| `/api/v1/admin/config` | GET/PUT | Platform configuration |

### Advanced Analytics (Guard: `admin`, permission: `analytics.view`)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/v1/admin/analytics/registration-trends` | GET | Registration trends |
| `/api/v1/admin/analytics/reader-engagement` | GET | Reader engagement |
| `/api/v1/admin/analytics/writer-activity` | GET | Writer activity |
| `/api/v1/admin/analytics/article-performance` | GET | Article performance |
| `/api/v1/admin/analytics/category-popularity` | GET | Category popularity |
| `/api/v1/admin/analytics/search-activity` | GET | Search activity |
| `/api/v1/admin/analytics/book-sales` | GET | Book sales |
| `/api/v1/admin/analytics/financial-contributions` | GET | Financial contributions |
| `/api/v1/admin/analytics/payouts` | GET | Payouts |
| `/api/v1/admin/analytics/failed-transactions` | GET | Failed transactions |
| `/api/v1/admin/analytics/moderation-activity` | GET | Moderation activity |
| `/api/v1/admin/analytics/platform-overview` | GET | Platform overview |

### Advanced Moderation (Guard: `admin`, permission: `comments.moderate`)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/v1/admin/moderation/reports` | GET | List reports |
| `/api/v1/admin/moderation/reports/{id}/resolve` | POST | Resolve report |
| `/api/v1/admin/moderation/records` | GET | List moderation records |
| `/api/v1/admin/moderation/accounts/suspend` | POST | Suspend account |
| `/api/v1/admin/moderation/accounts/reinstate` | POST | Reinstate account |
| `/api/v1/admin/moderation/comments/{id}/remove` | POST | Remove comment |
| `/api/v1/admin/moderation/accounts/restrict` | POST | Restrict account |

### Editorial Scheduling (Guard: `admin`, permission: `homepage.manage`)
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/v1/admin/editorial/selections` | GET/POST | Editorial selections |
| `/api/v1/admin/editorial/selections/{id}` | PUT/DELETE | Update/delete selection |
| `/api/v1/admin/editorial/scheduled-articles` | GET | List scheduled articles |
| `/api/v1/admin/editorial/homepage-sections` | GET | Get homepage sections |

## Mobile Integration Considerations

### Token Storage
- Store Sanctum tokens securely (iOS Keychain, Android Keystore)
- Implement token refresh on 401 responses
- Implement automatic logout on token expiry

### Offline Support
- Implement local caching for articles and books
- Queue actions (likes, bookmarks) when offline
- Sync when connection is restored

### Push Notifications
- Backend supports notification storage and retrieval
- Future integration with FCM (Android) and APNs (iOS)
- Notification preferences stored per reader

### Image Optimization
- All media served via S3-compatible storage with CDN
- Responsive image sizes supported
- Lazy loading recommended for mobile

### Deep Linking
- Article deep links: `mylo://article/{slug}`
- Book deep links: `mylo://book/{slug}`
- Writer deep links: `mylo://writer/{id}`

## Rate Limiting
- Default: 60 requests per minute per IP per endpoint
- Auth endpoints: stricter limits apply
- Headers: `Retry-After` included on 429 responses

## Security
- All API traffic over HTTPS in production
- Sanctum tokens for API authentication
- CSRF protection for state-changing requests
- Content Security Policy headers
- X-Content-Type-Options, X-Frame-Options, X-XSS-Protection headers
- Referrer-Policy: strict-origin-when-cross-origin

## Testing Checklist for Mobile
- [ ] Reader registration and login
- [ ] Writer registration and login
- [ ] Article browsing and reading
- [ ] Article liking and bookmarking
- [ ] Reading history tracking
- [ ] Preference management
- [ ] Book browsing and purchase
- [ ] Cart and checkout
- [ ] Digital ebook download
- [ ] Writer article creation and publishing
- [ ] Writer earnings and payout request
- [ ] Writer financial support activation
- [ ] Payment verification (webhook-based)
- [ ] Rate limiting enforcement
- [ ] Error handling (401, 403, 404, 422, 429, 500)
