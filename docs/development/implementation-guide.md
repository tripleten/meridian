# Meridian — Developer Implementation Guide

**Project:** Meridian Ecommerce Platform
**Author:** L K Lalitesh <lalitesh@live.com>
**Company:** Bytics Lab
**Copyright:** 2026 Bytics Lab. All rights reserved.
**Architecture Reference:** [../architecture/ecommerce-platform-blueprint-v2.md](../architecture/ecommerce-platform-blueprint-v2.md)

---

## 1. Architect's Recommended Build Order

> **Why this order matters:** The most common mistake is building a feature top-to-bottom (migration → controller → React) in isolation. This creates schema drift, forces rework, and produces untestable domain logic. The order below is designed to catch design problems early and keep each layer independently testable.

### The Four Stages

```
Stage 0: Foundation          ← Do once, before any feature work
Stage 1: Full Schema First   ← All migrations + seeds before any application code
Stage 2: Domain Layer First  ← Per entity: pure PHP first, tests green, then build outward
Stage 3: Admin Before Shop   ← Admin panel CRUD before storefront display
```

---

### Stage 0 — Project Foundation (Do Once)

Sequence:

1. Install all Composer packages (see blueprint §12)
2. Install all NPM packages
3. Configure `composer.json` PSR-4 to map `Meridian\\` → `src/`
4. Create the full `src/` folder skeleton (empty dirs, `.gitkeep`)
5. Create shared value objects (`Money`, `Address`, `SeoMeta`, `UrlKey`)
6. Create base exception classes (`DomainException`, `InvalidStateTransition`)
7. Create `OutboxWriter` and `OutboxRelay` infrastructure (§9)
8. Create `ChannelContext` and `CurrencyContext` (§17, §19)
9. Register all module service providers
10. Set up Horizon queues and queue configuration
11. Configure Laravel Scout + Meilisearch connection
12. Set up Spatie Media Library disk configuration

> Do **not** write any domain entity, migration, or controller until Stage 0 is complete.

---

### Stage 1 — Full Schema First (All Migrations Before Feature Code)

**Run all migrations in dependency order before writing a single handler or controller.**

Why: If you build migrations alongside feature code, you constantly discover missing columns and cascade changes across files you already wrote. One full pass through the schema while the blueprint is fresh catches design problems cheaply.

**Migration dependency order:**

```
Group 1 — Infrastructure (no FK dependencies)
  channels
  currencies
  settings
  outbox_messages
  idempotency_log
  invoice_sequences
  credit_memo_sequences

Group 2 — Identity
  users  (already exists from starter kit)
  roles / permissions  (Spatie)

Group 3 — Catalog foundation
  brands
  attribute_sets
  attributes
  categories
  channel_category           ← pivot
  tax_classes                ← needed by products

Group 4 — Catalog entities
  products
  product_variants
  product_attribute_values
  product_variant_attributes
  channel_product            ← pivot
  media  (Spatie Media Library auto-migrates)

Group 5 — Customers & Addresses
  customers
  customer_addresses
  customer_groups
  customer_tax_exemptions

Group 6 — Inventory
  inventory_sources
  inventory_items
  inventory_reservations

Group 7 — Pricing
  price_lists
  price_list_items
  tier_prices

Group 8 — Tax
  tax_zones
  tax_rates
  tax_rules

Group 9 — Cart
  carts
  cart_items

Group 10 — Orders
  orders
  order_items
  order_comments
  order_refunds

Group 11 — Payments
  payment_methods
  transactions

Group 12 — Promotions
  coupons
  cart_rules
  catalog_price_rules
  gift_cards

Group 13 — Fulfillment
  shipments
  shipment_items
  return_requests

Group 14 — CMS & SEO
  cms_pages
  cms_blocks
  url_rewrites

Group 15 — Marketing & Reporting
  cookie_consents
  daily_sales_aggregates
  product_performance
  webhook_endpoints
  webhook_deliveries

Group 16 — Loyalty & Social
  wishlist_items
  compare_list_items
  loyalty_accounts
  loyalty_transactions
```

**After all migrations pass:** run seeders in this order:

```
1. ChannelSeeder           ← one default channel row
2. CurrencySeeder          ← GBP as base + common currencies
3. RolesAndPermissionsSeeder  ← all Spatie roles and permissions
4. AdminUserSeeder         ← super-admin user
5. TaxClassSeeder          ← Standard, Reduced, Zero
6. TaxZoneSeeder           ← UK + EU zones
7. TaxRateSeeder           ← UK 20%, UK 5%, UK 0%, EU country rates
8. SettingsSeeder          ← all default settings values
9. PaymentMethodSeeder     ← Stripe, PayPal, Bank Transfer (inactive by default)
```

---

### Stage 2 — One Bounded Context at a Time

Build each context in this strict layer order:

```
Step 1: Domain  →  Step 2: Infrastructure  →  Step 3: Application  →  Step 4: Presentation
```

#### Step 1 — Domain (Pure PHP, no framework)

Write and test these before touching Eloquent or Laravel:

- Enums (e.g. `OrderStatus` with `allowedTransitions()`)
- Value objects (e.g. `Money`, `Sku`, `SeoMeta`)
- Entities and aggregate roots (pure `__construct`, business methods)
- Domain events (plain PHP classes, no framework)
- Repository interfaces (contracts only — no implementation)
- Domain service interfaces
- Specifications (e.g. `OrderCanBeCancelledSpec`)

> **Test target:** 100% of domain logic should be testable with `new MyClass()` — no Laravel boot, no DB, runs in milliseconds.

#### Step 2 — Infrastructure

- Eloquent models (no business logic, only `$fillable`, `$casts`, relationships)
- Repository implementations (EloquentXxxRepository implementing domain contract)
- Queue jobs
- Event listeners
- External API adapters (payment gateways, tax APIs, shipping carriers)
- Read models for admin grids

#### Step 3 — Application

- Command and Query DTOs (`spatie/laravel-data`)
- Command handlers (write operations)
- Query handlers (read operations — optimised for screen, not domain)
- Application services (coordinating multiple domain objects)

#### Step 4 — Presentation

- Admin controllers (thin — delegate to handler immediately)
- Shop controllers
- API controllers (v1)
- Form Request classes
- Inertia React pages

---

### Stage 3 — Admin Panel Before Storefront

Build in this order within each bounded context:

```
Admin CRUD → Admin reads/reports → Storefront display → Storefront actions
```

Why: You cannot display products on the storefront until an admin has created them. The admin panel also exposes simpler, predictable CRUD patterns that warm you up to the domain before writing the more complex checkout/cart flows.

---

### Bounded Context Build Order

| # | Context | Depends on | Notes |
|---|---|---|---|
| 1 | IdentityAccess | — | Auth, roles, permissions. All other contexts need this. |
| 2 | Settings | IdentityAccess | Global config needed by almost everything |
| 3 | Catalog — Categories | Settings | Nested sets, SEO, channel visibility |
| 4 | Catalog — Products (simple) | Categories, IdentityAccess | Basic product CRUD first |
| 5 | Catalog — Variants | Products | Configurable product logic |
| 6 | Catalog — Attributes | Products | EAV-lite attribute management |
| 7 | Catalog — Brands & Media | Products | Brand assignment, image gallery |
| 8 | Inventory | Catalog | Stock per variant per source |
| 9 | Tax | Settings | Tax classes, zones, rates |
| 10 | Pricing | Catalog, Tax, Customers | Price waterfall, tier prices |
| 11 | CmsSeo | Settings, IdentityAccess | Pages, blocks, URL rewrites |
| 12 | Customers | IdentityAccess | Profiles, address book, groups |
| 13 | Cart | Catalog, Pricing, Promotions | Guest + customer carts |
| 14 | Promotions | Catalog, Pricing, Customers | Coupons, cart rules |
| 15 | Checkout | Cart, Tax, Shipping, Payments | Orchestration only |
| 16 | Payments | — | Gateway abstraction + Stripe first |
| 17 | Orders | Checkout, Payments, Inventory | State machine, snapshots |
| 18 | Fulfillment | Orders, Inventory | Shipments, tracking, RMA |
| 19 | Refunds | Orders, Payments, Inventory | Credit memos, partial refunds |
| 20 | Marketing | Orders, Catalog, CmsSeo | GTM DataLayer, feeds |
| 21 | Reporting | Orders, Catalog, Customers | Read models, async reports |
| 22 | Search | Catalog, Inventory, Pricing | Meilisearch indexing |
| 23 | Integrations | All | Webhooks, ERP connectors |

---

## 2. Starting a New Entity — Checklist

Use this checklist every time you add a new entity to any bounded context.

### 2.1 Domain

- [ ] Create enum or value object for status/type (if applicable)
- [ ] Write the aggregate or entity class (`final class`, typed properties)
- [ ] Add `allowedTransitions()` to status enum (if state machine applies)
- [ ] Create domain events (`XxxCreated`, `XxxStatusChanged`, etc.)
- [ ] Add repository **interface** to `Domain/Repositories/`
- [ ] Write domain unit tests — must run with zero Laravel boot

### 2.2 Infrastructure

- [ ] Create Eloquent model (`Infrastructure/Persistence/EloquentXxx.php`)
  - No business logic
  - Correct `$casts` (ULID, encrypted, enum, JSON)
  - `HasStates` trait if state machine applies
- [ ] Create migration (`database/migrations/`)
- [ ] Create repository implementation (`EloquentXxxRepository`)
- [ ] Create factory (`database/factories/`)
- [ ] Register binding in service provider: `$this->app->bind(XxxRepository::class, EloquentXxxRepository::class)`
- [ ] Create outbox listener if entity emits domain events

### 2.3 Application

- [ ] Create Command DTO + Handler for each write operation
- [ ] Create Query DTO + Handler for each read screen
- [ ] Create `XxxData` via `spatie/laravel-data` for request/response contracts
- [ ] Write application-layer tests (mock repository, assert events)

### 2.4 Presentation (Admin)

- [ ] Create admin controller (`Http/Controllers/Admin/XxxController.php`)
  - `index()` — delegates to list query handler
  - `create()` — returns Inertia page with form shape
  - `store()` — validates Request → DTO → Command → Handler
  - `edit()` — delegates to get query handler
  - `update()` — same as store
  - `destroy()` — delegates to delete command handler
- [ ] Add routes to `routes/web.php` under `admin` middleware group
- [ ] Create React page: `resources/js/pages/Admin/Xxx/Index.tsx`
- [ ] Create React page: `resources/js/pages/Admin/Xxx/Form.tsx` (create + edit reuse)
- [ ] Add navigation item to `AdminLayout.tsx`
- [ ] Write feature test: `tests/Feature/Admin/XxxManagementTest.php`

### 2.5 Activity Log

- [ ] Add `LogsActivity` trait to Eloquent model (spatie/laravel-activitylog)
- [ ] Configure `$logAttributes` — only the fields that matter for audit
- [ ] Verify log entry appears in admin activity log UI

---

## 3. File & Class Naming Reference

### PHP Files

| Type | Location | Name example |
|---|---|---|
| Aggregate/Entity | `src/{Context}/Domain/{Entity}/` | `Order.php`, `Product.php` |
| Value Object | `src/{Context}/Domain/ValueObjects/` | `Money.php`, `OrderSnapshot.php` |
| Enum | `src/{Context}/Domain/{Entity}/` | `OrderStatus.php` |
| Domain Event | `src/{Context}/Domain/Events/` | `OrderPlaced.php` |
| Domain Service | `src/{Context}/Domain/Services/` | `TaxCalculationService.php` |
| Repository Interface | `src/{Context}/Domain/Repositories/` | `OrderRepository.php` |
| Command | `src/{Context}/Application/Commands/` | `PlaceOrderCommand.php` |
| Command Handler | `src/{Context}/Application/Commands/` | `PlaceOrderHandler.php` |
| Query | `src/{Context}/Application/Queries/` | `GetOrderQuery.php` |
| Query Handler | `src/{Context}/Application/Queries/` | `GetOrderHandler.php` |
| DTO | `src/{Context}/Application/DTOs/` | `OrderData.php` |
| Eloquent Model | `src/{Context}/Infrastructure/Persistence/` | `EloquentOrder.php` |
| Repository Impl | `src/{Context}/Infrastructure/Persistence/` | `EloquentOrderRepository.php` |
| Job | `src/{Context}/Infrastructure/Jobs/` | `SendOrderConfirmationJob.php` |
| Listener | `src/{Context}/Infrastructure/Listeners/` | `ReserveInventoryOnOrderPlaced.php` |
| Admin Controller | `app/Http/Controllers/Admin/{Context}/` | `OrderController.php` |
| Shop Controller | `app/Http/Controllers/Shop/` | `ProductController.php` |
| API Controller | `app/Http/Controllers/Api/V1/` | `OrderApiController.php` |
| Form Request | `app/Http/Requests/Admin/{Context}/` | `UpdateOrderStatusRequest.php` |

### React/TypeScript Files

| Type | Location | Name example |
|---|---|---|
| Admin page | `resources/js/pages/Admin/{Context}/` | `Index.tsx`, `Form.tsx`, `Show.tsx` |
| Shop page | `resources/js/pages/Shop/` | `Category/Show.tsx`, `Product/Show.tsx` |
| Layout | `resources/js/layouts/` | `AdminLayout.tsx`, `ShopLayout.tsx` |
| Reusable component | `resources/js/components/{admin\|shop}/` | `DataTable.tsx`, `ProductCard.tsx` |
| Custom hook | `resources/js/hooks/` | `useCart.ts`, `useCookieConsent.ts` |

---

## 4. Layer Dependency Rules

These rules are enforced by code review. A PR that violates them is rejected.

```
Domain
  ✅ Can import:   other domain classes in same context, Shared value objects
  ❌ Cannot import: Illuminate\, Eloquent, HTTP, Spatie packages (except laravel-data in Application)

Application
  ✅ Can import:   Domain, spatie/laravel-data, domain repository contracts
  ❌ Cannot import: Illuminate\Http, Eloquent models, infrastructure classes

Infrastructure
  ✅ Can import:   Domain contracts, Application DTOs, Laravel/Eloquent freely
  ❌ Cannot import: Presentation (controllers, requests)

Presentation
  ✅ Can import:   Application Commands/Queries, Form Requests, Inertia
  ❌ Cannot import: Domain aggregates directly (go through Application handlers)
```

Quick rule of thumb: **if you're writing `use Illuminate\` inside `src/{Context}/Domain/`, stop.**

---

## 5. PHPDoc & Comment Standards

See [coding-standards.md](./coding-standards.md) for the full standard with examples.

### Required on every file

```php
<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\{Context}\{Layer}
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */
```

### Required on every class

```php
/**
 * One-line summary of what this class is.
 *
 * Longer explanation if needed. Describe the business responsibility,
 * not the technical implementation.
 *
 * @package Meridian\{Context}\{Layer}
 */
final class MyClass
```

### Required on public methods with non-obvious behaviour

```php
/**
 * Transition the order to a new status.
 *
 * @throws InvalidOrderTransition if the transition is not permitted
 */
public function transitionStatus(OrderStatus $newStatus): void
```

Methods with obvious names and typed signatures (e.g. `getId(): string`) do not need PHPDoc — the signature is sufficient.

---

## 6. Git Commit Convention

```
feat(catalog): add configurable product variant resolution
fix(orders): correct state transition from on_hold to cancelled
refactor(pricing): extract price waterfall into dedicated service
test(promotions): add cart rule combination edge cases
chore(deps): update spatie/laravel-data to ^4.2
docs(architecture): update blueprint with channel scoping
```

Format: `type(context): description`

Types: `feat`, `fix`, `refactor`, `test`, `chore`, `docs`, `perf`

---

## 7. Testing Rules

- **Domain tests** must use zero Laravel features. `beforeEach` = `new SomeClass()`.
- **Application tests** may mock repository contracts. Never mock domain objects.
- **Integration tests** use real DB (SQLite in-memory for CI). Never mock repositories here.
- **Feature tests** use `RefreshDatabase`, hit real HTTP routes.
- Test files mirror source structure: `tests/Domain/Orders/OrderStatusTest.php` mirrors `src/Orders/Domain/Order/OrderStatus.php`
- A PR must not reduce test coverage on domain and application layers.

---

## 8. Environment Setup for a New Developer

```bash
# 1. Clone and install
git clone <repo> meridian && cd meridian
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Database
php artisan migrate
php artisan db:seed

# 4. Services (Docker recommended)
docker-compose up -d  # starts MySQL, Redis, Meilisearch

# 5. Search index
php artisan scout:import "Meridian\Catalog\Infrastructure\Persistence\EloquentProduct"

# 6. Run
composer run dev  # starts server, queue, logs, vite concurrently
```

---

*Implementation Guide v1.0*
*L K Lalitesh — Bytics Lab — 2026*
