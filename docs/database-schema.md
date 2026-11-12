# DATABASE SCHEMA & ERD

## Entity relationship overview

```
IDENTITY
  readers (id, email, password, email_verified_at, ...)
  reader_profiles (id, reader_id, display_name, bio, avatar, preferences_json, ...)
  writers (id, email, password, email_verified_at, ...)
  writer_profiles (id, writer_id, display_name, bio, avatar, earnings_balance, ...)
  administrators (id, email, password, ...)
  admin_profiles (id, administrator_id, display_name, ...)
  admin_roles (id, name, slug, description)
  admin_permissions (id, name, slug, description)
  admin_role_permissions (role_id, permission_id)
  admin_user_roles (administrator_id, role_id)
  personal_access_tokens (Sanctum)

PUBLISHING
  articles (id, writer_id, category_id, subcategory_id, title, slug, excerpt,
            content_json (blocks), featured_image, status (draft/published/scheduled),
            seo_title, seo_description, reading_time, scheduled_at, published_at, ...)
  article_revisions (id, article_id, content_json, changed_by, change_summary, created_at)
  article_tags (article_id, tag_id)
  categories (id, parent_id, name, slug, description, image, is_featured, sort_order)
  subcategories (id, category_id, name, slug)
  tags (id, name, slug)
  media_assets (id, owner_type, owner_id, uploader_type, uploader_id, file_path,
                file_type, file_size, width, height, alt_text, is_reported)
  scheduled_publications (id, article_id, publish_at, status)

ENGAGEMENT
  article_likes (id, article_id, reader_id)
  comments (id, article_id, writer_id, reader_id, parent_id, content, is_approved, ...)
  comment_replies (id, comment_id, writer_id, reader_id, content, ...)
  bookmarks (id, reader_id, article_id)
  reading_history (id, reader_id, article_id, read_at, duration_seconds)
  reader_preferences (id, reader_id, category_id)  -- or JSON on profile
  notifications (id, recipient_type, recipient_id, type, data, read_at, ...)
  reports (id, reportable_type, reportable_id, reporter_type, reporter_id, reason, status)

MARKETPLACE
  books (id, writer_id, product_category_id, title, slug, description, author,
         cover_image, price, product_type (physical/ebook), is_available,
         preview_file, digital_file, inventory_count, ...)
  product_categories (id, name, slug)
  product_images (id, book_id, file_path, alt_text, sort_order)
  inventory (id, book_id, quantity, reserved, ...)
  carts (id, reader_id, ...)
  cart_items (id, cart_id, book_id, quantity, ...)
  orders (id, reader_id, status, total, shipping_address, ...)
  order_items (id, order_id, book_id, quantity, price, ...)
  digital_access (id, order_item_id, reader_id, book_id, access_token, expires_at, ...)

FINANCE
  contributions (id, writer_id, reader_id, amount, currency, type (one-time/recurring),
                 status, transaction_reference, ...)
  recurring_support (id, writer_id, reader_id, amount, currency, interval, next_due,
                     status, ...)
  transactions (id, reference, customer_type, customer_id, writer_id, type, amount,
                currency, payment_method, provider, provider_reference, status, idempotency_key, ...)
  payment_attempts (id, transaction_id, provider, reference, raw_response, ...)
  commissions (id, transaction_id, amount, rate, ...)
  writer_earnings (id, writer_id, contributions, book_sales, deductions,
                   available_balance, pending_balance, withdrawn, ...)
  payout_requests (id, writer_id, amount, status, processed_at, ...)
  payout_records (id, payout_request_id, amount, provider, reference, ...)
  refunds (id, transaction_id, amount, reason, status, processed_at, ...)
  financial_ledger (id, entry_type, reference, amount, currency, balance_before,
                    balance_after, description, created_by, created_at)

ADMINISTRATION
  audit_logs (id, administrator_id, action, subject_type, subject_id,
              old_values, new_values, ip, user_agent, created_at)
  moderation_records (id, moderator_id, subject_type, subject_id, action, reason, ...)
  editorial_selections (id, article_id, section, sort_order, starts_at, ends_at)
  homepage_sections (id, name, type, sort_order, is_active, ...)
  platform_config (id, key, value, type, ...)
```

## Indexes (key)
- `articles(status, published_at)`, `articles(slug)`, `articles(writer_id)`, `articles(category_id)`
- `article_likes(reader_id, article_id)` unique
- `bookmarks(reader_id, article_id)` unique
- `comments(article_id, parent_id)`, `comments(reader_id)`
- `reading_history(reader_id, article_id)` unique
- `transactions(idempotency_key)` unique, `transactions(reference)` unique
- `financial_ledger(created_at)`, `financial_ledger(reference)`
- `orders(reader_id)`, `orders(status)`
- `digital_access(reader_id, book_id)` unique
- `admin_user_roles(administrator_id)`, `admin_role_permissions(role_id)`
- `audit_logs(administrator_id, created_at)`

## Soft deletes
- readers, writers, administrators, articles, books, comments, media_assets

## Constraints
- Financial transactions: idempotency key + reference uniqueness (prevent duplicates)
- Ledger: append-only; balance_before + amount = balance_after enforced by service layer
- Reader/writer separation: no shared auth table; no auto-conversion

## ERD note
A visual ERD (dbdiagram / Mermaid / PostgreSQL diagram) should be generated from migrations during Stage 2 and committed to `docs/erd.png` or `docs/schema.sql`.
