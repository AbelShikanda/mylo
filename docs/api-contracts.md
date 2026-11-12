# API CONTRACTS

API versioning: `/api/v1/...`. Authentication via Laravel Sanctum (stateful SPA) + token guard reserved for future mobile.

## Base response envelope
```json
{
  "data": ...,
  "meta": { "pagination": {...} },
  "errors": [...]
}
```

Status codes: 200 OK, 201 Created, 204 No Content, 400 Validation, 401 Unauthorized, 403 Forbidden, 404 Not Found, 422 Unprocessable, 429 Rate Limited, 500 Server Error.

---

## Auth (public)
- POST `/api/v1/auth/register/reader`
- POST `/api/v1/auth/register/writer`
- POST `/api/v1/auth/login/reader`
- POST `/api/v1/auth/login/writer`
- POST `/api/v1/auth/login/admin`
- POST `/api/v1/auth/logout`
- POST `/api/v1/auth/password/email`
- POST `/api/v1/auth/password/reset`
- POST `/api/v1/auth/email/verify`
- POST `/api/v1/auth/mfa/enable` (optional)
- POST `/api/v1/auth/mfa/verify`

## Readers (auth: reader)
- GET `/api/v1/reader/profile`
- PUT `/api/v1/reader/profile`
- GET `/api/v1/reader/preferences`
- PUT `/api/v1/reader/preferences`
- GET `/api/v1/reader/bookmarks`
- POST `/api/v1/reader/bookmarks/{article}`
- DELETE `/api/v1/reader/bookmarks/{article}`
- GET `/api/v1/reader/reading-history`
- GET `/api/v1/reader/notifications`
- PUT `/api/v1/reader/notifications/{id}/read`
- POST `/api/v1/reader/contributions`
- GET `/api/v1/reader/contributions`
- GET `/api/v1/reader/library` (purchased ebooks)
- GET `/api/v1/reader/orders`

## Writers (auth: writer)
- GET `/api/v1/writer/profile`
- PUT `/api/v1/writer/profile`
- GET `/api/v1/writer/articles`
- POST `/api/v1/writer/articles`
- GET `/api/v1/writer/articles/{id}`
- PUT `/api/v1/writer/articles/{id}`
- DELETE `/api/v1/writer/articles/{id}`
- POST `/api/v1/writer/articles/{id}/publish`
- POST `/api/v1/writer/articles/{id}/schedule`
- POST `/api/v1/writer/articles/{id}/autosave`
- GET `/api/v1/writer/drafts`
- GET `/api/v1/writer/scheduled`
- GET `/api/v1/writer/media`
- POST `/api/v1/writer/media`
- DELETE `/api/v1/writer/media/{id}`
- GET `/api/v1/writer/books`
- POST `/api/v1/writer/books`
- PUT `/api/v1/writer/books/{id}`
- GET `/api/v1/writer/comments`
- PUT `/api/v1/writer/comments/{id}`
- GET `/api/v1/writer/earnings`
- GET `/api/v1/writer/analytics`
- POST `/api/v1/writer/support/activate`
- POST `/api/v1/writer/support/deactivate`

## Articles (public + auth for write)
- GET `/api/v1/articles` (list, filter, paginate)
- GET `/api/v1/articles/{slug}`
- POST `/api/v1/articles/{id}/like` (auth: reader)
- DELETE `/api/v1/articles/{id}/like` (auth: reader)
- GET `/api/v1/articles/{id}/comments`
- POST `/api/v1/articles/{id}/comments` (auth: reader)
- PUT `/api/v1/articles/{id}/comments/{cid}` (auth: reader/writer)
- DELETE `/api/v1/articles/{id}/comments/{cid}` (auth: reader/writer/admin)
- POST `/api/v1/articles/{id}/report` (auth: reader)
- GET `/api/v1/articles/{id}/related`

## Categories & tags (public)
- GET `/api/v1/categories`
- GET `/api/v1/categories/{id}`
- GET `/api/v1/categories/{id}/articles`
- GET `/api/v1/tags`
- GET `/api/v1/tags/{slug}`

## Search
- GET `/api/v1/search?q=...&type=article|book|writer|category|tag&filters=...&sort=...&page=...`

## Books & marketplace (public + auth)
- GET `/api/v1/books`
- GET `/api/v1/books/{id}`
- POST `/api/v1/books` (auth: writer/publisher)
- PUT `/api/v1/books/{id}` (auth: writer/publisher/admin)
- DELETE `/api/v1/books/{id}` (auth: writer/publisher/admin)
- POST `/api/v1/cart/items` (auth: reader)
- GET `/api/v1/cart` (auth: reader)
- DELETE `/api/v1/cart/items/{id}` (auth: reader)
- POST `/api/v1/checkout` (auth: reader)
- GET `/api/v1/orders` (auth: reader)
- GET `/api/v1/orders/{id}` (auth: reader)
- GET `/api/v1/library/download/{ebook}` (auth: reader, signed URL)
- POST `/api/v1/library/purchases` (auth: reader)

## Admin (auth: admin + role permission)
- GET `/api/v1/admin/dashboard/stats`
- GET `/api/v1/admin/readers`
- GET `/api/v1/admin/writers`
- PUT `/api/v1/admin/users/{id}/suspend`
- PUT `/api/v1/admin/users/{id}/reinstate`
- GET `/api/v1/admin/articles`
- PUT `/api/v1/admin/articles/{id}/feature`
- PUT `/api/v1/admin/articles/{id}/unfeature`
- DELETE `/api/v1/admin/articles/{id}`
- GET `/api/v1/admin/categories`
- POST `/api/v1/admin/categories`
- PUT `/api/v1/admin/categories/{id}`
- DELETE `/api/v1/admin/categories/{id}`
- GET `/api/v1/admin/comments`
- DELETE `/api/v1/admin/comments/{id}`
- GET `/api/v1/admin/media`
- DELETE `/api/v1/admin/media/{id}`
- GET `/api/v1/admin/books`
- GET `/api/v1/admin/orders`
- GET `/api/v1/admin/finance/overview`
- GET `/api/v1/admin/finance/transactions`
- GET `/api/v1/admin/finance/earnings`
- GET `/api/v1/admin/finance/payouts`
- POST `/api/v1/admin/finance/payouts/{id}/approve`
- POST `/api/v1/admin/finance/payouts/{id}/reject`
- GET `/api/v1/admin/finance/refunds`
- POST `/api/v1/admin/finance/refunds/{id}/approve`
- POST `/api/v1/admin/finance/refunds/{id}/reject`
- GET `/api/v1/admin/payment-gateways`
- PUT `/api/v1/admin/payment-gateways/{id}`
- GET `/api/v1/admin/homepage/sections`
- POST `/api/v1/admin/homepage/sections`
- PUT `/api/v1/admin/homepage/sections/{id}`
- GET `/api/v1/admin/audit-logs`
- GET `/api/v1/admin/roles`
- GET `/api/v1/admin/permissions`
- GET `/api/v1/admin/config`
- PUT `/api/v1/admin/config`

## Webhooks (public, verified)
- POST `/api/v1/webhooks/payment/{provider}`
- POST `/api/v1/webhooks/mpesa`

---

## Authorization summary
- `reader` guard: reader routes
- `writer` guard: writer routes
- `admin` guard: admin routes + role-based policy checks
- All mutating endpoints validate input via FormRequest
- Financial endpoints require idempotency key header `Idempotency-Key`
