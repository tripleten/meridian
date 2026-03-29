# Meridian — Ecommerce Platform Blueprint v2

> Merges the original Coex blueprint with DDD refinements, concrete implementation patterns, and full-stack decisions for Laravel 13 + React 19 + Inertia.js.

---

## 1. Executive Summary

| Decision | Choice | Rationale |
|---|---|---|
| Architecture | DDD-inspired Modular Monolith | Magento-class complexity, small team, clean boundaries |
| Backend | Laravel 13 / PHP 8.5 | Ecosystem, expressiveness, package quality |
| Frontend | React 19 + Inertia.js v3 + TypeScript | SPA feel, no separate API needed for admin/storefront |
| Public API | REST v1 (JSON:API-ish) | Headless/mobile/integration support |
| Auth | Laravel Fortify + Spatie Permission | 2FA built-in, fine-grained permissions |
| Database | MySQL 8.x (primary) + Redis 7 | Transactions + cache/queue/sessions |
| Search | Meilisearch via Laravel Scout | Fast faceted search, easy Docker setup |
| Queue | Laravel Horizon (Redis) | Job monitoring, retries, priority queues |
| Media | Spatie Media Library + Intervention Image | Responsive images, WebP conversion, S3/R2 |
| State Machines | Pure PHP enums (domain) + `spatie/laravel-model-states` (infrastructure only) | Transition rules in domain, persistence mapping in Eloquent |
| DTOs | `spatie/laravel-data` | Typed, validated, serializable data objects |
| PDF | DomPDF | Invoices, credit memos, packing slips |
| Reports | Maatwebsite Excel + async read models | Heavy reports offloaded to queue |
| Testing | Pest | Expressive, fast, Laravel-native |

**Do not start with microservices.** A modular monolith with clean internal boundaries is the right call. If traffic or team demands extraction later, the bounded contexts are already portable.

---

## 2. Architecture Principles

1. **Business rules live in domain code** — not controllers, not Eloquent models.
2. **Eloquent is a persistence tool** — domain entities are not Eloquent models. Infrastructure contains Eloquent; domain does not depend on it.
3. **Controllers are thin** — accept a Request, delegate to an Action/Handler, return a Response. Nothing else.
4. **DTOs cross layer boundaries** — no raw arrays, no Request objects in domain/application layers.
5. **Domain Events decouple modules** — `OrderPlaced` triggers inventory reservation, activity log, email, and marketing events without direct coupling.
6. **State machines enforce transitions** — Order, Payment, and Return statuses must transition through defined, tested paths.
7. **Async by default for side effects** — reports, emails, indexing, feed generation, webhooks run in queues.
8. **Auditability everywhere** — every price change, stock change, order status change is logged.
9. **Build for extension** — pricing, promotions, and shipping always become more complex. Abstract them from day one.
10. **No live catalog queries in historical views** — order snapshots preserve the price, address, tax, and product data that existed at placement time.

---

## 3. Top-Level Directory Structure

```
meridian/
├── app/
│   └── Support/              ← Laravel-specific glue (middleware, providers, exceptions)
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── docs/
│   └── architecture/
├── public/
├── routes/
│   ├── web.php               ← Shop + Admin Inertia routes
│   ├── api.php               ← REST API v1 routes
│   └── console.php
├── src/                      ← ALL domain/application/infrastructure code
│   ├── Shared/               ← Cross-cutting value objects and contracts
│   ├── IdentityAccess/
│   ├── Catalog/
│   ├── Pricing/
│   ├── Inventory/
│   ├── Cart/
│   ├── Checkout/
│   ├── Orders/
│   ├── Payments/
│   ├── Fulfillment/
│   ├── Promotions/
│   ├── Customers/
│   ├── CmsSeo/
│   ├── Marketing/
│   ├── Reporting/
│   ├── Search/
│   └── Integrations/
├── resources/
│   └── js/
│       ├── pages/
│       │   ├── Admin/
│       │   └── Shop/
│       ├── components/
│       │   ├── admin/
│       │   └── shop/
│       └── layouts/
└── tests/
    ├── Domain/
    ├── Application/
    ├── Integration/
    └── Feature/
```

> **Composer autoload mapping:**
> ```json
> "autoload": {
>   "psr-4": {
>     "App\\": "app/",
>     "Meridian\\": "src/"
>   }
> }
> ```

---

## 4. Internal Module Structure

Every module under `src/` follows the same four-layer structure:

```
src/Orders/
├── Domain/
│   ├── Order/
│   │   ├── Order.php                    ← Aggregate root (pure PHP, no Eloquent)
│   │   ├── OrderItem.php                ← Entity
│   │   ├── OrderStatus.php              ← State machine states
│   │   ├── OrderStatusTransition.php    ← Transition rules
│   │   └── Specifications/
│   │       └── OrderCanBeCancelled.php
│   ├── ValueObjects/
│   │   ├── OrderSnapshot.php            ← Immutable snapshot at placement time
│   │   ├── ShippingAddressSnapshot.php
│   │   └── LineItemPrice.php
│   ├── Events/
│   │   ├── OrderPlaced.php
│   │   ├── OrderStatusChanged.php
│   │   ├── OrderCancelled.php
│   │   └── OrderShipped.php
│   ├── Repositories/
│   │   └── OrderRepository.php          ← Interface (contract only)
│   └── Policies/
│       └── OrderPolicy.php
│
├── Application/
│   ├── Commands/
│   │   ├── PlaceOrderCommand.php
│   │   ├── PlaceOrderHandler.php
│   │   ├── CancelOrderCommand.php
│   │   └── CancelOrderHandler.php
│   ├── Queries/
│   │   ├── GetOrderQuery.php
│   │   ├── GetOrderHandler.php
│   │   └── ListOrdersQuery.php
│   ├── DTOs/
│   │   ├── OrderData.php               ← spatie/laravel-data
│   │   └── OrderItemData.php
│   └── Services/
│       └── InvoiceGeneratorService.php
│
├── Infrastructure/
│   ├── Persistence/
│   │   ├── EloquentOrder.php           ← Eloquent model
│   │   ├── EloquentOrderItem.php
│   │   └── EloquentOrderRepository.php ← Implements OrderRepository contract
│   ├── Jobs/
│   │   ├── GenerateInvoicePdfJob.php
│   │   └── SendOrderConfirmationJob.php
│   ├── Listeners/
│   │   ├── ReserveInventoryOnOrderPlaced.php
│   │   └── TriggerMarketingEventOnOrderPlaced.php
│   └── ReadModels/
│       └── OrderAdminGridReadModel.php  ← Optimized for admin grid queries
│
└── Presentation/
    ├── Admin/
    │   ├── OrderController.php
    │   └── Requests/
    │       └── UpdateOrderStatusRequest.php
    ├── Shop/
    │   └── AccountOrderController.php
    └── Api/
        └── V1/
            └── OrderApiController.php
```

This same pattern applies to every module. The `Domain` layer has **zero** Laravel/Eloquent imports.

---

## 5. Bounded Contexts

### 5.1 IdentityAccess

**Responsibilities:** Users, admin accounts, customer accounts, roles, permissions, 2FA, guards.

**Key decisions:**
- `spatie/laravel-permission` with granular permissions: `catalog.products.update`, `orders.cancel`, `promotions.manage`, etc.
- Separate `admin` and `storefront` guards (different session handling)
- Permission groups for display in the admin UI
- Admin actions require 2FA at sensitive operations (price changes, refunds)

**Predefined roles:**

| Role | Scope |
|---|---|
| `super-admin` | Everything, bypasses all gates |
| `admin` | All except system/infra settings |
| `catalog-manager` | Products, categories, brands, media, inventory |
| `order-manager` | Orders, fulfillment, returns, invoices |
| `marketing-manager` | Promotions, coupons, CMS, SEO, feeds |
| `customer-support` | Customers (read), orders (read + limited edit) |
| `reports-viewer` | Reports, dashboards (read-only) |
| `customer` | Storefront account only |

---

### 5.2 Catalog

**Responsibilities:** Products, categories, attributes, brands, media, relations, visibility.

**Product types (enum):**
- `simple` — single SKU, direct price and stock
- `configurable` — master product + variants (Size × Color combinations)
- `bundle` — grouping of simple products sold together
- `virtual` — no shipping (services, warranties)
- `downloadable` — file delivery on purchase

**Category model (nested sets via `kalnoy/nestedset`):**

> **Codex conflict resolved:** Nested-set positions (`_lft`, `_rgt`, `depth`) encode a single tree topology. A single row cannot hold multiple positions for different channel trees. The chosen design is: **one global category tree, per-channel visibility only**. Channels control which categories they expose; they do not get independent tree structures. If a future need arises for truly different trees per channel (e.g. a separate B2B channel with a completely different hierarchy), model that as a separate `category_trees` table with its own nested-set columns — but do not over-engineer for it now.

```
categories
  id, parent_id, _lft, _rgt, depth    ← global tree positions (one tree only)
  name, slug, url_key
  description, short_description
  hero_image_id, thumbnail_image_id
  is_active, sort_mode, position
  seo_title, seo_description, seo_robots, canonical_url
  og_title, og_description, og_image_url
  created_at, updated_at

channel_category                       ← per-channel VISIBILITY pivot (not a separate tree)
  channel_id, category_id
  is_visible     TINYINT(1) DEFAULT 1
  sort_order     SMALLINT   DEFAULT 0
  UNIQUE KEY (channel_id, category_id)
```

A category exists once in the global tree. The `channel_category` pivot controls whether it appears in a given channel's navigation. Tree queries (`getDescendants()`, breadcrumbs) always run against the global tree; the channel filter is applied as a join after tree resolution, not during it.

`SeoMeta` is a **value object** embedded directly in Category, Product, and CMS Page. Not a separate polymorphic table. This avoids N+1 joins and simplifies reads. For Category, Product, and Page, the SEO columns live on the same table.

**Attribute strategy — JSON hybrid (not EAV):**

Magento's EAV is notoriously slow. Instead:
- Core filterable attributes (color, size, brand, material) → dedicated normalized columns or a `product_attribute_values` join table keyed to `attribute_id`
- Arbitrary/flexible extra attributes → `extra_attributes` JSON column via `spatie/laravel-schemaless-attributes`
- Search attributes are synced to Meilisearch index on change

**Key aggregates:** `Product`, `Category`, `AttributeSet`, `Brand`

---

### 5.3 Pricing

**Responsibilities:** All price computation. Catalog owns the base price; Pricing owns everything else.

**Price resolution order (waterfall):**
1. Customer group special price
2. Tier price (quantity break)
3. Date-ranged special price
4. Catalog price rule (scheduled discount)
5. Base price

**Value object:**
```php
// Money is always integer cents + currency code. Never floats.
final class Money
{
    public function __construct(
        public readonly int $amount,      // in smallest unit (cents)
        public readonly string $currency, // ISO 4217
    ) {}
}
```

---

### 5.4 Inventory

**Responsibilities:** Stock items, sources/warehouses, reservations, backorders, low stock alerts.

**Multi-Source Inventory (MSI) from day one:**
```
inventory_sources     ← Warehouses, stores, dropship locations
inventory_items       ← product_id + source_id + qty_available + qty_reserved
inventory_reservations← cart_id/order_id + product_variant_id + qty + status
```

Reserve on cart checkout start. Commit on payment. Release on cart expiry or order cancellation.

---

### 5.5 Cart

**Responsibilities:** Guest + customer carts, coupon application, shipping/tax estimation.

```
carts
  id, session_id (guest) or customer_id (logged in)
  coupon_code, applied_rule_ids (JSON)
  currency, locale
  abandoned_at, recovered_at
  expires_at

cart_items
  id, cart_id, product_id, product_variant_id
  qty, unit_price_snapshot, name_snapshot, sku_snapshot
  custom_options (JSON)
```

Guest carts merge into customer cart on login.

---

### 5.6 Checkout

**Responsibilities:** Address capture, shipping selection, payment selection, risk checks, order placement orchestration.

Checkout does **not own rules** — it orchestrates:
- `PricingService::calculate()`
- `TaxService::calculate()`
- `ShippingCalculatorService::getRates()`
- `PromotionService::applyCartRules()`
- `PaymentService::createIntent()`

`PlaceOrderHandler` runs everything inside a DB transaction. It **writes `OrderPlaced` to the outbox table** (same transaction — see §9) and clears the cart. It does **not** call `event()` directly; the outbox relay dispatches `OrderPlaced` asynchronously after the transaction commits.

---

### 5.7 Orders

**State transitions (pure domain enum — no framework dependency):**

```
pending_payment
    → payment_failed
    → processing
        → on_hold
        → cancelled (if not shipped)
        → shipped
            → partially_delivered
            → delivered
                → completed
                → refund_requested
                    → refunded
                        → closed
```

**Critical:** Store snapshots at order time:
```
orders
  ...
  shipping_address_snapshot (JSON)
  billing_address_snapshot  (JSON)
  pricing_snapshot          (JSON)  ← unit prices, discounts, tax, totals at placement
  customer_snapshot         (JSON)  ← name, email at placement time
```

Never display historical order data from live catalog/customer tables.

---

### 5.8 Payments

**Provider abstraction:**
```php
interface PaymentGateway
{
    public function createIntent(Money $amount, Order $order): PaymentIntent;
    public function capture(string $intentId): Transaction;
    public function refund(string $transactionId, Money $amount): Refund;
}
```

Implementations: `StripeGateway`, `PayPalGateway`. Gateway swap never touches business code.

Webhook processing runs through a dedicated `ProcessPaymentWebhookJob` with idempotency checks on `transaction_reference`.

---

### 5.9 Fulfillment

**Responsibilities:** Shipments, tracking, RMA (returns), pick-pack workflow.

**Return state machine:**
```
requested → approved → items_received → inspected → refunded / rejected / exchange_dispatched
```

---

### 5.10 Promotions

**Separate from Pricing** — promotion rules grow complex fast.

| Type | Example |
|---|---|
| Cart Rule | Buy 2 get 1 free, Free shipping over $100, % off cart |
| Catalog Rule | 20% off all Nike shoes this weekend |
| Coupon | Single/multi-use codes, expiry, usage limits per customer |
| Tier Price | 1-9 units = $10, 10+ units = $8 |
| Flash Sale | Time-limited, countdown display |
| Gift Card | Balance-based redemption at checkout |

**Rule engine:** Condition groups (AND/OR) evaluate against `CartContext` DTO — never the raw Eloquent cart.

---

### 5.11 Customers

**Responsibilities:** Profiles, address book, groups, segments, wishlist, loyalty, compare list, recently viewed.

```
customers                 ← extends users (or separate table)
customer_addresses        ← address book (multiple per customer)
customer_groups           ← Retail, Wholesale, VIP
wishlists / wishlist_items
loyalty_accounts          ← points balance, tier
loyalty_transactions      ← earn/spend history
compare_lists             ← session or customer-bound, max 4 products
recently_viewed           ← session or customer-bound, last 20 products
```

---

### 5.12 CmsSeo

**Responsibilities:** CMS pages, content blocks, URL rewrites, redirects, sitemap, structured data.

**CMS Page:**
```
cms_pages
  id, title, slug, content (Tiptap/Quill JSON or HTML)
  layout (full-width, sidebar, landing)
  status, published_at
  seo_title, seo_description, seo_robots, canonical_url
  og_title, og_description, og_image_url
```

**URL Rewrites:**
```
url_rewrites
  id, source_path, target_path
  type (301, 302, rewrite)
  entity_type (product, category, cms_page, custom)
  entity_id
```

**Sitemap:** `spatie/laravel-sitemap` — generated async daily, split by entity type.

**Structured Data:** `SchemaOrg` value object for Product, BreadcrumbList, Organization — rendered in `<script type="application/ld+json">` by Inertia head.

---

### 5.13 Marketing

**Do not scatter tracking calls across templates.** Use a DataLayer architecture.

**Architecture:**

```
Domain Events (OrderPlaced, CartUpdated, etc.)
    ↓
MarketingEventMapper
    ↓
DataLayer (JSON passed to Inertia shared props)
    ↓
React: pushes to window.dataLayer → GTM → GA4/Google Ads/Meta Pixel
    ↓
(parallel) Server-side: Measurement Protocol → GA4 (bypasses ad blockers)
```

**Interface:**
```php
interface MarketingTracker
{
    public function track(MarketingEvent $event): void;
}

// Implementations:
// GtagServerTracker (Measurement Protocol)
// DataLayerCollector (for frontend push)
// NullTracker (tests)
```

**UTM attribution:** Persist UTM params from first landing to session to order.
```
orders
  utm_source, utm_medium, utm_campaign, utm_content, utm_term
```

**Google Merchant Center feed:** `GenerateGoogleFeedJob` → XML/CSV file uploaded to Cloud Storage, URL submitted to GMC.

---

### 5.14 Reporting

**Never run reports against live transactional tables.**

**Strategy:**
1. **Projection tables** (updated by event listeners) for fast dashboard widgets
2. **Precomputed daily snapshots** via scheduled jobs
3. **On-demand exports** dispatched to queue → file stored → user notified via broadcast

**Reporting tables:**
```
daily_sales_aggregates      ← date, revenue, orders, items, avg_order_value
product_performance         ← product_id, views, add_to_carts, purchases, revenue
customer_cohort_aggregates  ← cohort_month, ltv, retention
inventory_snapshots         ← product_id, source_id, qty, date
promotion_effectiveness     ← rule_id, uses, discount_total, revenue_influenced
abandoned_cart_aggregates   ← date, count, potential_revenue, recovered_revenue
search_term_analytics       ← term, results_count, clicks, no_results (bool)
```

**Report generation flow:**
```
Admin clicks "Generate Report"
    → CreateReportJob dispatched (Horizon)
    → Report written to Storage::disk('reports')
    → ReportGenerated event
    → Broadcast to admin via Laravel Echo
    → Admin gets download link
```

---

### 5.15 Search

**Meilisearch via Laravel Scout** — `laravel/scout` + `meilisearch/meilisearch-php`.

**Searchable index per entity:**
- `products` — name, description, sku, brand, category_names, attributes (JSON), price, in_stock
- `categories` — name, description, url_key
- `cms_pages` — title, content (stripped)

**Faceted navigation:**
```
Filterable attributes (configured in Meilisearch settings):
  brand, category_ids, price_range, color, size, rating, in_stock
```

**Merchandising:** Manual boost rules stored in DB, applied at search time via `sort` parameter.

**Synonyms:** Managed in admin → synced to Meilisearch settings on save.

**Autocomplete:** Dedicated lightweight `SearchController` calls Meilisearch directly, returns top 5 products + 3 categories.

---

### 5.16 Integrations

**Responsibilities:** ERP/PIM/WMS connectors, webhooks, feed exports, and third-party adapters that do not belong to any other bounded context.

> **Codex finding addressed:** Tax APIs (TaxJar, Avalara) are **not** an Integrations responsibility. They are external adapters owned by the `Tax` bounded context (`src/Tax/Infrastructure/`). Similarly, payment provider SDKs are owned by `Payments`, and shipping carrier APIs are owned by `Fulfillment`/`Shipping`. The Integrations module is a catch-all only for connectors that have no natural home in an existing bounded context.

**Outbound webhooks:**
```
webhook_endpoints   ← url, secret, events (JSON array), is_active
webhook_deliveries  ← endpoint_id, event, payload, response, attempts, status
```

Delivery via `DispatchWebhookJob` with exponential backoff, up to 5 retries.

**Shipping carrier abstraction:**
```php
interface ShippingCarrier
{
    public function getRates(ShipmentRequest $request): Collection; // of ShippingRate
    public function createShipment(ShipmentRequest $request): Shipment;
    public function track(string $trackingNumber): TrackingInfo;
}
```

---

### 5.17 Tax *(promoted from thin adapter — Codex finding)*

> **Codex finding addressed:** A single `TaxProvider` interface is too thin for Magento-class regional selling. Tax is a genuine business capability with its own domain model.

**Responsibilities:** Tax classes, zones, rates, rules, inclusive/exclusive pricing, VAT/GST compliance, customer exemptions, shipping taxability, order-time snapshots.

**Why a full bounded context:**
- Tax classes differ by product type (standard goods, food, digital, clothing)
- Tax zones differ by country, state, postcode
- Rates can be inclusive (EU VAT: price shown already includes tax) or exclusive (US sales tax: added at checkout)
- B2B customers can have tax exemption certificates
- Shipping itself may or may not be taxable depending on jurisdiction
- Tax values must be snapshotted on orders for audit/legal purposes (tax law can change)

**Domain model:**

```
tax_classes          ← e.g. "Standard", "Reduced", "Zero Rate", "Digital Services"
  id, name, code

tax_zones            ← geographical scope
  id, name
  countries (JSON)   ← ISO country codes
  regions  (JSON)    ← state/province codes, or '*' for all

tax_rates            ← the actual % for a zone
  id, tax_zone_id, name
  rate (DECIMAL 5,4) ← e.g. 0.2000 = 20%
  type ENUM('inclusive','exclusive')
  is_compound        ← stacked taxes (e.g. Canada: GST + PST)

tax_rules            ← ties classes to zones (many-to-many with priority)
  id, name, priority
  tax_class_ids (JSON)
  tax_zone_ids  (JSON)

customer_tax_exemptions
  id, customer_id, exemption_certificate, valid_from, valid_until, is_active

shipping_tax_rules   ← which zones tax shipping, at what class
  id, tax_zone_id, tax_class_id
```

**Domain service (pure PHP):**

```php
// src/Tax/Domain/Services/TaxCalculationService.php
final class TaxCalculationService
{
    // Pure domain: given a TaxRequest DTO containing line items, customer
    // address, customer group, and shipping amount — returns a TaxBreakdown.
    // No Eloquent. Accepts TaxRule value objects resolved by the repository.
    public function calculate(TaxRequest $request): TaxBreakdown { ... }
}
```

**Value objects:**

```php
final class TaxBreakdown              // returned from calculation
{
    public readonly Money $subtotalExclTax;
    public readonly Money $subtotalInclTax;
    public readonly Money $taxAmount;
    public readonly Money $shippingTaxAmount;
    /** @var TaxLine[] */
    public readonly array $lines;     // per-rate breakdown for invoice
}

final class TaxLine
{
    public readonly string $rateName; // e.g. "VAT 20%"
    public readonly string $rateCode;
    public readonly float  $rate;
    public readonly Money  $taxable;
    public readonly Money  $tax;
}
```

**Order snapshot — tax captured at placement:**

```
orders
  tax_snapshot (JSON)   ← full TaxBreakdown at placement time
                          (legal requirement — rates may change post-order)
```

**External provider abstraction (application layer, not domain):**

```php
// src/Tax/Application/Contracts/ExternalTaxProvider.php
interface ExternalTaxProvider
{
    public function calculate(TaxRequest $request): TaxBreakdown;
}
// Implementations: TaxJarProvider, AvalaraProvider
// Falls back to LocalTaxCalculationService when no external provider configured
```

**Module structure:**

```
src/Tax/
  Domain/
    TaxClass.php
    TaxZone.php
    TaxRate.php
    TaxRule.php
    ValueObjects/
      TaxBreakdown.php
      TaxLine.php
      TaxRequest.php
    Services/
      TaxCalculationService.php
    Repositories/
      TaxRuleRepository.php          ← interface
  Application/
    Contracts/
      ExternalTaxProvider.php
    Queries/
      GetApplicableTaxRulesHandler.php
  Infrastructure/
    LocalTaxCalculator.php
    TaxJarProvider.php
    AvalaraProvider.php
    Eloquent/
      EloquentTaxRule.php
      EloquentTaxRuleRepository.php
  Presentation/
    Admin/
      TaxRateController.php
      TaxZoneController.php
      TaxClassController.php
```

---

## 6. Data & Persistence Strategy

| Layer | Technology | Used For |
|---|---|---|
| Write model | MySQL 8.x | All transactional data |
| Read model | MySQL (separate read tables) + Redis | Admin grids, reporting projections |
| Cache | Redis | Full-page cache, object cache, rate limiting |
| Sessions | Redis | Scalable, shareable sessions |
| Queues | Redis + Horizon | Jobs, emails, indexing, webhooks |
| Search | Meilisearch | Product/category search + facets |
| Media | S3 / Cloudflare R2 | Images, files, generated reports |
| Locks | Redis | Inventory reservation concurrency |

**Key MySQL practices:**
- All prices in integer cents (never `DECIMAL` for Money in PHP; store as `BIGINT UNSIGNED`)
- Use `ulid()` as primary keys on high-write tables (no sequential ID enumeration)
- Proper composite indexes on `(status, created_at)` for order queries
- Soft deletes only where needed (products yes, orders never)

---

## 7. Action Classes & DTOs

### Actions (single-responsibility, injectable)

```php
// src/Orders/Application/Commands/PlaceOrderHandler.php
final class PlaceOrderHandler
{
    public function __construct(
        private readonly OrderRepository $orders,
        private readonly CartService $cart,
        private readonly PricingService $pricing,
        private readonly PaymentService $payment,
        private readonly EventDispatcher $events,
    ) {}

    public function handle(PlaceOrderCommand $command): Order
    {
        // All business logic here — no HTTP, no Eloquent at this level
    }
}
```

### DTOs via spatie/laravel-data

```php
// src/Orders/Application/DTOs/OrderData.php
use Spatie\LaravelData\Data;

final class OrderData extends Data
{
    public function __construct(
        public readonly string $customerId,
        public readonly Address $shippingAddress,
        public readonly Address $billingAddress,
        public readonly string $paymentMethod,
        public readonly ?string $couponCode,
    ) {}
}
```

DTOs are used between all layers. `Request` objects stop at the controller. Domain/Application never import `Illuminate\Http`.

---

## 8. State Machines

> **Codex finding addressed:** `spatie/laravel-model-states` extends `Spatie\ModelStates\State` which is an Eloquent-coupled package. Placing it in the Domain layer violates the "zero Laravel/Eloquent imports" principle. The fix: transition *rules* live in pure PHP domain enums; the package is used *only* on the Eloquent infrastructure model for persistence mapping.

### Two-Layer State Machine Pattern

**Layer 1 — Domain (pure PHP, no framework):**

```php
// src/Orders/Domain/Order/OrderStatus.php
enum OrderStatus: string
{
    case PendingPayment   = 'pending_payment';
    case PaymentFailed    = 'payment_failed';
    case Processing       = 'processing';
    case OnHold           = 'on_hold';
    case Shipped          = 'shipped';
    case PartiallyDelivered = 'partially_delivered';
    case Delivered        = 'delivered';
    case Completed        = 'completed';
    case Cancelled        = 'cancelled';
    case RefundRequested  = 'refund_requested';
    case Refunded         = 'refunded';
    case Closed           = 'closed';

    /** @return OrderStatus[] */
    public function allowedTransitions(): array
    {
        return match($this) {
            self::PendingPayment    => [self::Processing, self::PaymentFailed],
            self::PaymentFailed     => [self::PendingPayment],
            self::Processing        => [self::Shipped, self::OnHold, self::Cancelled],
            self::OnHold            => [self::Processing, self::Cancelled],
            self::Shipped           => [self::PartiallyDelivered, self::Delivered],
            self::PartiallyDelivered => [self::Delivered],
            self::Delivered         => [self::Completed, self::RefundRequested],
            self::RefundRequested   => [self::Refunded],
            self::Refunded          => [self::Closed],
            default                 => [],
        };
    }

    public function canTransitionTo(self $new): bool
    {
        return in_array($new, $this->allowedTransitions(), true);
    }
}
```

```php
// src/Orders/Domain/Order/Order.php  (aggregate root, pure PHP)
public function transitionStatus(OrderStatus $newStatus): void
{
    if (! $this->status->canTransitionTo($newStatus)) {
        throw new InvalidOrderTransition($this->status, $newStatus);
    }

    $previousStatus = $this->status;
    $this->status   = $newStatus;

    $this->recordEvent(new OrderStatusChanged($this->id, $previousStatus, $newStatus));
}
```

Domain unit tests for every valid and every invalid transition — no framework boot, no DB, instant.

**Layer 2 — Infrastructure (Eloquent model, can use spatie/laravel-model-states for persistence convenience):**

```php
// src/Orders/Infrastructure/Persistence/EloquentOrder.php
use Spatie\ModelStates\HasStates;

class EloquentOrder extends Model
{
    use HasStates;

    // spatie/laravel-model-states registers the column, casts, and
    // provides TransitionTo helper on the Eloquent model.
    // It does NOT own transition rules — those live in the domain enum above.
    protected function registerStates(): void
    {
        $this->addState('status', EloquentOrderStatus::class);
    }
}
```

The repository maps between the Eloquent `status` string and the domain `OrderStatus` enum when hydrating the aggregate. Transition logic is always invoked through the domain `Order::transitionStatus()`, never directly through `$eloquentOrder->status->transitionTo()`.

Same two-layer pattern for `PaymentStatus` and `ReturnRequestStatus`.

---

## 9. Transactional Outbox & Idempotency

> **Codex finding addressed:** The blueprint relied heavily on `OrderPlaced` and other domain events triggering indexing, emails, reports, and webhooks — but said nothing about what happens when those events are lost at a transaction boundary, or when a queue job is retried and executes twice. This section makes reliable event delivery first-class infrastructure.

### The Problem

```
DB::transaction(function () {
    $order->save();           // ✅ committed to DB
    event(new OrderPlaced()); // ❌ dispatched AFTER commit, but what if the process
                              //    dies between save() and event()?
                              //    Or the queue worker retries a job twice?
});
```

`event()` and `dispatch()` inside a transaction are not atomic with the DB write. If the process crashes between commit and dispatch, the event is silently lost. If a job fails and retries, side effects (email, webhook, stock deduction) run twice.

### Solution: Transactional Outbox Pattern

Write domain events to an `outbox_messages` table **in the same DB transaction** as the state change. A separate relay process reads committed outbox rows and dispatches them to the queue — exactly once.

**Schema:**
```sql
CREATE TABLE outbox_messages (
    id            CHAR(26)     NOT NULL,   -- ULID
    aggregate_type VARCHAR(100) NOT NULL,  -- e.g. 'Order'
    aggregate_id   CHAR(26)    NOT NULL,
    event_type     VARCHAR(200) NOT NULL,  -- FQCN of domain event
    payload        JSON        NOT NULL,
    occurred_at    TIMESTAMP   NOT NULL,
    dispatched_at  TIMESTAMP   NULL,       -- NULL = not yet relayed
    attempts       TINYINT     NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    INDEX idx_undispatched (dispatched_at, occurred_at)
);
```

**Write side (inside DB transaction):**
```php
// src/Shared/Infrastructure/Outbox/OutboxWriter.php
class OutboxWriter
{
    public function record(DomainEvent $event): void
    {
        // This INSERT is inside the same transaction as the aggregate save.
        // If the transaction rolls back, this row disappears too.
        DB::table('outbox_messages')->insert([
            'id'             => Str::ulid(),
            'aggregate_type' => $event->aggregateType(),
            'aggregate_id'   => $event->aggregateId(),
            'event_type'     => $event::class,
            'payload'        => json_encode($event),
            'occurred_at'    => now(),
        ]);
    }
}
```

**Relay process (scheduled every few seconds or run as a daemon):**
```php
// src/Shared/Infrastructure/Outbox/OutboxRelay.php
// Polls outbox_messages WHERE dispatched_at IS NULL
// Dispatches each to the appropriate queue job
// Marks dispatched_at = now() atomically with a row lock
// Runs via: php artisan outbox:relay (scheduled every 5s via Horizon)
```

**PlaceOrderHandler — correct pattern:**
```php
DB::transaction(function () use ($command) {
    $order = $this->orders->save($order);
    $this->outbox->record(new OrderPlaced($order->id));  // ← same transaction
    // NO event() call here
});
// Outbox relay dispatches OrderPlaced asynchronously after commit
```

### Idempotency on Job Consumers

Every job that produces an observable side effect must be idempotent.

```php
// src/Orders/Infrastructure/Jobs/SendOrderConfirmationJob.php
class SendOrderConfirmationJob implements ShouldBeUnique
{
    // Laravel's ShouldBeUnique prevents duplicate jobs in queue.
    // For cross-retry idempotency, use an idempotency key table:

    public function handle(): void
    {
        $key = "order_confirmation:{$this->orderId}";

        if (IdempotencyLog::alreadyProcessed($key)) {
            return; // Safely skip — was already sent
        }

        Mail::to($this->email)->send(new OrderConfirmationMail($this->order));
        IdempotencyLog::markProcessed($key, ttl: 7 * 24 * 60 * 60); // 7 days
    }
}
```

```sql
CREATE TABLE idempotency_log (
    idempotency_key VARCHAR(200) NOT NULL,
    processed_at    TIMESTAMP    NOT NULL,
    expires_at      TIMESTAMP    NOT NULL,
    PRIMARY KEY (idempotency_key),
    INDEX idx_expires (expires_at)
);
```

### Webhook Delivery Idempotency

Outbound webhooks use the same `outbox_messages` row's `id` as the idempotency key sent in the `X-Webhook-Idempotency-Key` header. Receivers can deduplicate on their end.

### Summary

| Risk | Mitigation |
|---|---|
| Event lost at transaction boundary | Transactional outbox — event write is atomic with state change |
| Job retried, side effect runs twice | `ShouldBeUnique` + `IdempotencyLog` check per job |
| Webhook delivered twice | Idempotency key in header; `webhook_deliveries` deduplication |
| Payment webhook processed twice | `transaction_reference` unique constraint on `transactions` table |
| Report generated twice | Job uniqueness keyed to `report_id` |

---

## 10. Google Tag Manager / Analytics Architecture

### DataLayer Flow

```
PHP (Domain Events)
    ↓
MarketingEventMapper::fromDomainEvent()
    ↓ returns MarketingEvent DTO
DataLayerCollector::push(MarketingEvent)
    ↓ added to Inertia shared props as `dataLayer[]`
React (app.tsx)
    ↓
useEffect: window.dataLayer.push(...) on each page load/navigation
    ↓
GTM handles routing to GA4, Google Ads, Meta Pixel etc.
```

### Measurement Protocol (Server-Side, improves purchase attribution resilience)

> **Codex finding addressed:** "100% purchase attribution" was an overstatement. Consent requirements (GDPR/CCPA), missing client identifiers, and GA4's own deduplication logic mean server-side tracking improves — but does not guarantee — attribution completeness. Set expectations accordingly with marketing teams.

For `purchase` event specifically:
```php
// src/Marketing/Infrastructure/GtagServerTracker.php
// Fires GA4 Measurement Protocol HTTP call from the server
// immediately after OrderPlaced event — not dependent on the browser.
// Requires: GA4 Measurement ID, API Secret, and a client_id (from cookie if available).
// Deduplication: send the same event_id on both client and server;
// GA4 will deduplicate if both arrive within the same session window.
```

This **significantly improves** purchase attribution for users with ad blockers or who close the browser before the thank-you page loads — but attribution is still subject to consent signals, cookie availability, and GA4's deduplication logic.

### Events to track:

| Event | Trigger |
|---|---|
| `page_view` | Every Inertia navigation |
| `view_item` | Product page load |
| `view_item_list` | Category page load |
| `add_to_cart` | Cart add action |
| `remove_from_cart` | Cart remove action |
| `begin_checkout` | Checkout step 1 |
| `add_shipping_info` | Shipping step |
| `add_payment_info` | Payment step |
| `purchase` | Order confirmed (server-side + client-side) |
| `refund` | Refund processed |

---

## 10. Frontend Architecture (React + Inertia.js)

```
resources/js/
├── app.tsx                       ← Inertia init, GTM DataLayer push
├── ssr.tsx                       ← SSR entry (optional, for SEO)
├── types/
│   ├── models.d.ts               ← Mirrored from backend DTOs (auto-generated ideal)
│   └── inertia.d.ts
│
├── layouts/
│   ├── AdminLayout.tsx           ← Sidebar nav, breadcrumbs, notifications
│   ├── ShopLayout.tsx            ← Header, footer, cart flyout
│   └── CheckoutLayout.tsx        ← Minimal, no distractions
│
├── components/
│   ├── ui/                       ← Base Radix/Tailwind components (shadcn style)
│   ├── admin/
│   │   ├── DataTable.tsx         ← Reusable admin grid (sort, filter, pagination)
│   │   ├── RichTextEditor.tsx    ← Tiptap editor for CMS/descriptions
│   │   └── MediaPicker.tsx
│   └── shop/
│       ├── ProductCard.tsx
│       ├── ProductGallery.tsx
│       ├── PriceDisplay.tsx
│       ├── StarRating.tsx
│       └── FilterSidebar.tsx     ← Faceted navigation
│
├── hooks/
│   ├── useCart.ts
│   ├── useWishlist.ts
│   └── useSearch.ts
│
└── pages/
    ├── Admin/
    │   ├── Dashboard.tsx
    │   ├── Catalog/
    │   │   ├── Products/
    │   │   │   ├── Index.tsx     ← Data table with bulk actions
    │   │   │   ├── Create.tsx
    │   │   │   └── Edit.tsx      ← Tabbed: General, Variants, SEO, Media, Relations
    │   │   ├── Categories/
    │   │   └── Brands/
    │   ├── Orders/
    │   │   ├── Index.tsx
    │   │   └── Show.tsx          ← Timeline, items, payment, shipping, actions
    │   ├── Customers/
    │   ├── Promotions/
    │   ├── CMS/
    │   ├── Marketing/
    │   ├── Reports/
    │   └── Settings/
    │
    └── Shop/
        ├── Home.tsx
        ├── Category/
        │   └── Show.tsx          ← Products grid + FilterSidebar
        ├── Product/
        │   └── Show.tsx          ← Gallery, variants, price, add to cart
        ├── Search/
        │   └── Results.tsx
        ├── Cart/
        │   └── Index.tsx
        ├── Checkout/
        │   ├── Index.tsx         ← Address
        │   ├── Shipping.tsx
        │   └── Payment.tsx
        ├── Account/
        │   ├── Dashboard.tsx
        │   ├── Orders/
        │   ├── Addresses.tsx
        │   ├── Wishlist.tsx
        │   └── Profile.tsx
        └── CMS/
            └── Page.tsx
```

---

## 11. Queue & Job Architecture

All side effects are async. Never block a web request for:
- Sending emails
- Generating PDFs
- Indexing to Meilisearch
- Firing webhooks
- Generating reports
- Syncing marketing feeds

**Horizon queue configuration:**

```
queues (priority order):
  critical     ← payment webhooks, inventory reservations
  high         ← order confirmation emails, PDF generation
  default      ← search indexing, activity log flushes
  low          ← report generation, feed exports, abandoned cart checks
  reporting    ← dedicated workers for heavy report jobs
```

**Scheduled jobs:**
```
daily:
  - GenerateDailySalesSnapshotJob
  - AbandonedCartScanJob (hourly actually)
  - GenerateGoogleMerchantFeedJob
  - GenerateSitemapJob
  - ExpireFlashSalesJob
  - LowStockAlertJob

weekly:
  - GenerateWeeklyReportJob
  - CleanupExpiredCartsJob
  - CleanupOldWebhookDeliveriesJob
```

---

## 12. Package Manifest

### Composer (PHP)

```json
{
  "require": {
    "spatie/laravel-permission": "^6.x",
    "spatie/laravel-activitylog": "^4.x",
    "spatie/laravel-medialibrary": "^11.x",
    "spatie/laravel-data": "^4.x",
    "spatie/laravel-model-states": "^2.x",
    "spatie/laravel-query-builder": "^6.x",
    "spatie/laravel-sitemap": "^7.x",
    "spatie/laravel-sluggable": "^3.x",
    "spatie/laravel-schemaless-attributes": "^2.x",
    "spatie/laravel-tags": "^4.x",
    "kalnoy/nestedset": "^6.x",
    "laravel/scout": "^10.x",
    "meilisearch/meilisearch-php": "^1.x",
    "laravel/horizon": "^5.x",
    "maatwebsite/excel": "^3.x",
    "barryvdh/laravel-dompdf": "^3.x",
    "intervention/image-laravel": "^1.x",
    "stripe/stripe-php": "^13.x",
    "laravel/socialite": "^5.x",
    "propaganistas/laravel-phone": "^5.x",
    "owen-it/laravel-auditing": "^13.x"
  }
}
```

> **Note on `owen-it/laravel-auditing`:** Use alongside `spatie/laravel-activitylog`. Auditing tracks field-level changes (who changed what value), activity log tracks narrative events (who did what action).

### NPM (Frontend)

```json
{
  "dependencies": {
    "@tiptap/react": "^2.x",
    "@tiptap/starter-kit": "^2.x",
    "@tanstack/react-table": "^8.x",
    "react-hook-form": "^7.x",
    "zod": "^3.x",
    "zustand": "^5.x",
    "recharts": "^2.x",
    "react-image-gallery": "^1.x",
    "react-select": "^5.x",
    "date-fns": "^3.x",
    "react-dnd": "^16.x"
  }
}
```

---

## 13. Security Hardening

| Concern | Implementation |
|---|---|
| Auth brute force | Rate limit `POST /login` (5/min per IP) |
| Checkout abuse | Rate limit `POST /checkout` (10/min per customer) |
| Coupon enumeration | Rate limit coupon validation |
| Admin 2FA | Enforce on first login, TOTP via Fortify |
| PII in logs | `$hidden` on User/Customer + custom `LogContext` that strips sensitive fields |
| SQL injection | Eloquent parameterized queries, no raw `whereRaw` with user input |
| XSS | Inertia escapes by default; sanitize CMS HTML through `HTMLPurifier` before storing |
| CSRF | Laravel default; Inertia sends X-XSRF-TOKEN header automatically |
| Payment data | Never store raw card numbers; Stripe Elements keeps card data off our servers |
| API auth | Laravel Sanctum (SPA cookies for Inertia, Bearer tokens for mobile API) |
| Secrets | Laravel encrypted env via `php artisan env:encrypt` for production |
| Admin access | IP whitelist option + separate subdomain (`admin.meridian.com`) |

---

## 14. Testing Strategy

```
tests/
├── Domain/
│   ├── Orders/
│   │   ├── OrderStatusTransitionTest.php    ← State machine edge cases
│   │   └── OrderSnapshotTest.php
│   ├── Pricing/
│   │   └── PriceWaterfallTest.php           ← All pricing scenarios
│   └── Promotions/
│       └── CartRuleEngineTest.php           ← Rule combinations
│
├── Application/
│   ├── PlaceOrderHandlerTest.php
│   ├── ApplyCouponHandlerTest.php
│   └── ReserveInventoryHandlerTest.php
│
├── Integration/
│   ├── EloquentOrderRepositoryTest.php      ← Real DB, no mocks
│   ├── StripeGatewayTest.php                ← Stripe test mode
│   └── MeilisearchIndexTest.php
│
└── Feature/
    ├── Admin/
    │   └── ProductManagementTest.php
    └── Shop/
        ├── CheckoutFlowTest.php
        └── CartTest.php
```

**Highest-ROI tests (write these first):**
1. Price waterfall calculation
2. Promotion rule engine (all condition combinations)
3. Inventory reservation + release
4. `PlaceOrderHandler` (happy path + payment failure)
5. Order state machine transitions (invalid transitions must throw)
6. Payment webhook idempotency

---

## 15. Development Phases

### Phase 1 — Foundation (Weeks 1–4)
- [ ] IdentityAccess: auth, roles, permissions, seeder
- [ ] Catalog: categories (nested sets), products (simple), media, SEO fields
- [ ] CmsSeo: pages, blocks, URL rewrites, sitemap
- [ ] Customers: profiles, address book
- [ ] Admin layout, navigation, DataTable component
- [ ] Activity log on all models
- [ ] Basic storefront: home, category, product pages

### Phase 2 — Commerce Core (Weeks 5–8)
- [ ] Pricing: price waterfall, special prices, customer groups
- [ ] Inventory: stock items, reservation/release
- [ ] Cart: guest + customer, merge on login
- [ ] Checkout: address → shipping → payment flow
- [ ] Orders: placement, state machine, email confirmation
- [ ] Payments: Stripe integration, webhook handling
- [ ] Invoices: PDF generation async
- [ ] Storefront checkout flow

### Phase 3 — Catalog Depth (Weeks 9–11)
- [ ] Configurable products (variants, attribute combinations)
- [ ] Bundle products
- [ ] Product reviews and moderation
- [ ] Wishlist, compare, recently viewed
- [ ] Layered navigation (faceted search in Meilisearch)
- [ ] Advanced product import/export (CSV)

### Phase 4 — Promotions & Marketing (Weeks 12–14)
- [ ] Coupon engine (single-use, multi-use, expiry)
- [ ] Cart rules (% off, fixed, free shipping, BOGO)
- [ ] Catalog price rules (scheduled)
- [ ] Flash sales + countdown
- [ ] Gift cards
- [ ] GTM DataLayer architecture
- [ ] GA4 Measurement Protocol for purchase events
- [ ] UTM attribution persistence
- [ ] Google Merchant Center feed generation

### Phase 5 — Reporting & Operations (Weeks 15–17)
- [ ] Daily snapshot jobs
- [ ] Sales, product, customer, inventory reports
- [ ] Abandoned cart scan + email recovery sequence
- [ ] Webhook system (outbound)
- [ ] Admin audit trail UI
- [ ] Return/RMA workflow

### Phase 6 — Growth Features (Weeks 18+)
- [ ] Multi-currency (exchange rates, currency switcher)
- [ ] Loyalty/reward points
- [ ] Social login (Google, Facebook via Socialite)
- [ ] REST API v1 for mobile
- [ ] B2B features (company accounts, tier pricing, quote requests)
- [ ] Multi-warehouse MSI
- [ ] TaxJar/Avalara integration
- [ ] Subscription/recurring orders

---

## 16. Key Architectural Decisions Summary

| Decision | Rationale |
|---|---|
| `src/` not `app/` for domain code | Keeps Laravel glue separate from business code; `app/` for framework wiring only |
| No Eloquent in domain layer | Domain is portable, testable without DB, swappable ORM |
| Integer cents for Money | Eliminates floating point rounding bugs entirely |
| ULID primary keys | Prevents sequential ID scraping; better distributed insert performance |
| JSON snapshots on orders | Historical accuracy without catalog coupling |
| Nested sets for categories | O(1) subtree queries vs O(n) recursive CTE |
| Meilisearch over MySQL LIKE | 10-100x faster, typo-tolerant, faceting built-in |
| Horizon for queues | Job monitoring, retry control, worker scaling |
| GTM DataLayer over direct gtag | Single source of truth; marketing changes without code deploys |
| Server-side Measurement Protocol | Improves purchase attribution resilience — not a guarantee; consent, cookie availability, and GA4 deduplication still apply (see §10) |
| spatie/laravel-data for DTOs | Typed, castable, validateable, JSON-serializable without boilerplate |
| spatie/laravel-model-states | Used **only** on Eloquent models for state persistence/casting convenience — transition rules live in pure domain enums (see §8) |
| Separate reporting tables | Avoids locking live order tables; reports are always fast |
| 4-layer test structure | Domain (pure), Application (use cases), Integration (real I/O), Feature (HTTP) |

---

## 17. Channel / Store Scoping — Future-Proof Now, Multi-Store Later

> **Codex finding addressed:** Full multi-store is deferred, but several schema choices are expensive to retrofit. Reserve channel/locale/currency scoping in these tables from day one — even if only one channel exists at launch.

### The `channels` Table (create in Phase 1, populate with one row)

```sql
CREATE TABLE channels (
    id                   CHAR(26)     NOT NULL,   -- ULID
    code                 VARCHAR(50)  NOT NULL UNIQUE,  -- 'default', 'us', 'uk'
    name                 VARCHAR(100) NOT NULL,
    domain               VARCHAR(200) NULL,
    default_locale       CHAR(5)      NOT NULL DEFAULT 'en_US',
    supported_locales    JSON         NOT NULL,   -- ["en_US","fr_FR"]
    default_currency     CHAR(3)      NOT NULL DEFAULT 'USD',
    supported_currencies JSON         NOT NULL,
    is_active            TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
);
```

### Tables That Need a `channel_id` Column From Day One

| Table | How |
|---|---|
| `products` | Pivot `channel_product` — visibility per channel |
| `categories` | Pivot `channel_category` — **visibility** per channel (one global tree, see §5.2) |
| `price_lists` | Each channel gets its own price list |
| `cms_pages` | Column `channel_id` + `locale` — different content per channel/locale |
| `promotions` / `cart_rules` | Column `channel_id` nullable — NULL = all channels |
| `orders` | Column `channel_id` — for channel-level reporting |
| Meilisearch indexes | One index per channel+locale: `products_en_us`, `products_fr_fr` |

### What to Keep Global (Don't Over-scope)

- `tax_classes`, `tax_zones`, `tax_rates` — zone-level specificity is enough
- `customers` / `addresses` — one identity across all channels
- `inventory_sources` — global stock, visible to all channels

### Channel Context Pattern (Injectable, Not Global)

```php
// src/Shared/Infrastructure/Channel/ChannelContext.php
// Set by middleware per request. Injected into repos/services — never a static global.
final class ChannelContext
{
    public function __construct(
        public readonly string $channelId,
        public readonly string $locale,
        public readonly string $currency,
    ) {}
}
```

---

## 18. Deferred (Post-MVP, Do Not Over-engineer Now)

- GraphQL API (REST is sufficient, add if mobile team needs it)
- Full Event Sourcing (CQRS-lite is enough; full ES adds significant overhead)
- Microservices extraction (modular monolith first)
- Elasticsearch (Meilisearch scales to millions of products)
- Real-time inventory across warehouses (queue-based reservations handle most cases)
- AI-powered recommendations (add as an Integrations module later)
- **Multi-store UI** — schema is already channel-aware (see §17); full multi-store admin is the deferred work

---

---

## 19. Multi-Currency

Multi-currency is a first-class concern, not a display-only feature. Users can browse in their local currency, see converted prices, and **complete checkout in that currency**.

### Core Principle: Store in Base Currency, Display in Any

All prices in the database are stored in the **base currency** (e.g. GBP or USD) as integer cents. Conversion to display currencies happens at read time, not write time. Orders are recorded in **both** the checkout currency (what the customer paid) and the base currency (for accounting).

### Schema

```sql
-- All enabled currencies and their rates relative to base
CREATE TABLE currencies (
    code            CHAR(3)         NOT NULL,   -- ISO 4217: GBP, USD, EUR, AED
    name            VARCHAR(50)     NOT NULL,
    symbol          VARCHAR(5)      NOT NULL,   -- £, $, €
    symbol_position ENUM('before','after') NOT NULL DEFAULT 'before',
    exchange_rate   DECIMAL(14,6)   NOT NULL,   -- relative to base currency
    is_base         TINYINT(1)      NOT NULL DEFAULT 0,  -- exactly one row = 1
    is_active       TINYINT(1)      NOT NULL DEFAULT 1,
    decimal_places  TINYINT         NOT NULL DEFAULT 2,  -- JPY = 0, KWD = 3
    updated_at      TIMESTAMP       NOT NULL,
    PRIMARY KEY (code)
);

-- orders store both currencies for accounting integrity
ALTER TABLE orders ADD COLUMN
    base_currency        CHAR(3) NOT NULL,       -- e.g. GBP
    order_currency       CHAR(3) NOT NULL,       -- what customer paid in
    base_grand_total     BIGINT  NOT NULL,        -- in base currency cents
    order_grand_total    BIGINT  NOT NULL,        -- in order currency smallest unit
    exchange_rate_snapshot DECIMAL(14,6) NOT NULL; -- rate at order time — never recalculate
```

### Exchange Rate Strategy

Two options — support both via a config toggle:

| Mode | How | When to use |
|---|---|---|
| Manual | Admin sets rates in backend | Small store, stable currency pairs |
| Automatic | Scheduled job calls exchange rate API (e.g. Open Exchange Rates, ECB feed) | Production recommended |

```
// Scheduled daily:
UpdateExchangeRatesJob → fetches rates → updates currencies table → clears price cache
```

**Never recalculate** historical order totals when rates change. The `exchange_rate_snapshot` on the order is immutable.

### Price Display Pipeline

```
Request arrives with ?currency=EUR (or currency cookie / geo-IP default)
    ↓
CurrencyContext (injected, like ChannelContext) — holds code + rate
    ↓
PriceFormatter::format(Money $basePrice, CurrencyContext $ctx): DisplayPrice
    ↓
Returns: { amount: 2399, display: "€23.99", currency: "EUR" }
    ↓
Passed to React via Inertia shared props → rendered by <PriceDisplay /> component
```

### Checkout in Local Currency

- Cart stores `currency_code` + `exchange_rate` at the time items were added
- If the rate changes significantly (>2% threshold — configurable) before checkout completes, warn the user and refresh the cart totals
- Payment gateway receives the **order currency amount** (Stripe and PayPal both support multi-currency natively)
- The `exchange_rate_snapshot` on the order is written at `PlaceOrder` time — immutable from that point

### Bank Transfer in Multi-Currency

Bank transfer orders store the expected payment amount in the order currency. Admin sees both base and order currency amounts. The "payment received" confirmation must be manually matched by an admin.

### Admin Considerations

- Base currency is set once in system settings — changing it later requires a migration (warn loudly)
- Currency switcher widget in storefront header (show active + all enabled currencies)
- Admin reports always show amounts in **base currency** for consistent accounting; order detail shows both

---

## 20. Payment Gateways & Settings

### Admin Payment Settings — `payment_methods` Table

Each gateway is a first-class record in the DB, not just a config file. This allows toggling, ordering, and per-gateway credential management through the admin UI.

```sql
CREATE TABLE payment_methods (
    id              CHAR(26)     NOT NULL,
    code            VARCHAR(50)  NOT NULL UNIQUE,  -- 'stripe', 'paypal', 'bank_transfer'
    name            VARCHAR(100) NOT NULL,          -- display name
    description     TEXT         NULL,              -- shown at checkout
    is_active       TINYINT(1)   NOT NULL DEFAULT 0,
    sort_order      SMALLINT     NOT NULL DEFAULT 0,
    min_order_total BIGINT       NULL,              -- cents, NULL = no minimum
    max_order_total BIGINT       NULL,
    allowed_currencies JSON      NULL,              -- NULL = all; ["GBP","EUR"] = restricted
    allowed_countries  JSON      NULL,
    config          JSON         NOT NULL,          -- encrypted gateway credentials
    PRIMARY KEY (id)
);
```

The `config` JSON column stores gateway-specific credentials. **It must be encrypted at rest** using Laravel's `encrypted` cast.

### Gateway Implementations

**Stripe:**
```
config keys: publishable_key, secret_key, webhook_secret
             capture_method (automatic|manual), statement_descriptor
Features:    Payment Intents, 3DS2, refunds, webhooks
Currencies:  135+ currencies supported natively
```

**PayPal:**
```
config keys: client_id, client_secret, mode (sandbox|live), webhook_id
Features:    Orders API v2, capture/authorize, refunds, IPN webhook
Currencies:  25 currencies supported
```

**Bank Transfer (manual gateway):**
```
config keys: bank_name, account_name, account_number, sort_code, iban, swift
             payment_instructions (HTML — shown after order placed)
             auto_cancel_days (order cancelled if payment not confirmed within N days)
Features:    No API — admin manually marks order as paid
             Pending orders held in 'pending_payment' state
             Scheduled job cancels stale bank transfer orders
Currencies:  Whatever the admin configures
```

### Gateway Abstraction (already in §5.8, reinforced here)

```php
// src/Payments/Application/Contracts/PaymentGateway.php
interface PaymentGateway
{
    public function code(): string;
    public function createIntent(PaymentRequest $request): PaymentIntent;
    public function capture(string $intentId): Transaction;
    public function refund(string $transactionId, Money $amount): Refund;
    public function handleWebhook(WebhookPayload $payload): void;
    public function supportsRefund(): bool;  // Bank Transfer = false initially
}
```

`PaymentGatewayRegistry` resolves the correct implementation by `code` at runtime — no switch statements in business code.

### Settings Stored Encrypted

```php
// src/Payments/Infrastructure/Persistence/EloquentPaymentMethod.php
protected $casts = [
    'config'             => 'encrypted:array',
    'allowed_currencies' => 'array',
    'allowed_countries'  => 'array',
];
```

Admin UI for gateway settings uses a per-gateway form schema (defined by each gateway class) so new gateways can self-describe their config fields.

---

## 21. Global Site Settings & Scripts

### Settings Architecture

A key-value settings table with type casting and admin UI management. Grouped into namespaces for clarity.

```sql
CREATE TABLE settings (
    id          CHAR(26)        NOT NULL,
    group       VARCHAR(50)     NOT NULL,   -- 'general', 'seo', 'scripts', 'social', 'gdpr'
    key         VARCHAR(100)    NOT NULL,
    value       LONGTEXT        NULL,
    type        VARCHAR(20)     NOT NULL DEFAULT 'string',  -- string|boolean|integer|json|encrypted
    is_public   TINYINT(1)      NOT NULL DEFAULT 0,  -- whether to expose to frontend via Inertia
    updated_by  CHAR(26)        NULL,       -- admin user id
    updated_at  TIMESTAMP       NOT NULL,
    UNIQUE KEY uq_group_key (group, key),
    PRIMARY KEY (id)
);
```

### Setting Groups and Keys

**`general`**
```
store_name, store_email, store_phone, store_address (JSON)
default_locale, default_currency, default_timezone
logo_image_id, favicon_image_id
maintenance_mode (boolean)
```

**`scripts`** *(type: string / HTML)*
```
header_scripts   ← injected into <head> on every storefront page (GTM snippet, fonts, etc.)
footer_scripts   ← injected before </body> (chat widgets, etc.)
checkout_scripts ← injected only on checkout pages (Stripe.js, PayPal SDK)
```

These are rendered in the Inertia `ShopLayout.tsx` using `dangerouslySetInnerHTML` — they are **admin-only fields**, not user-supplied. Log every change via activity log.

**`social`**
```
social_sharing_enabled (boolean)  ← global toggle
facebook_url, instagram_url, twitter_url, linkedin_url, pinterest_url, tiktok_url
og_default_image_id               ← fallback OG image when product/page has none
twitter_card_type                 ← 'summary' | 'summary_large_image'
```

Social share buttons on product and CMS pages check `social_sharing_enabled` before rendering. All social links in the footer come from these settings.

**`seo`**
```
meta_title_suffix         ← e.g. " | Meridian" — appended to all page titles
default_meta_description
robots_txt_content        ← managed via admin, served at /robots.txt
google_site_verification
bing_site_verification
```

**`gdpr`** *(see §22 for full cookie policy)*
```
cookie_consent_enabled (boolean)
cookie_policy_page_id   ← CMS page id
privacy_policy_page_id
cookie_banner_text
cookie_categories (JSON) ← { necessary: true, analytics: false, marketing: false }
```

### Settings Service

```php
// src/Settings/Application/Services/SettingsService.php
// Wraps DB reads with Redis cache (TTL: 1 hour, invalidated on any save)
// Provides typed accessors:
$settings->get('scripts.header_scripts');
$settings->getBoolean('social.social_sharing_enabled');
$settings->getJson('gdpr.cookie_categories');
```

Settings that are `is_public = true` are merged into Inertia's shared props on every request, making them available to React without additional API calls.

---

## 22. EU Cookie Policy & GDPR Consent

### Consent Categories

Following IAB TCF v2 and EU ePrivacy Directive, consent is captured per category:

| Category | Requires Consent | Examples |
|---|---|---|
| Necessary | No (always on) | Session, cart, CSRF, auth |
| Functional | Yes | Currency preference, language choice |
| Analytics | Yes | GA4, Hotjar |
| Marketing | Yes | Google Ads, Meta Pixel, GTM marketing tags |

### Backend Consent Storage

```sql
CREATE TABLE cookie_consents (
    id              CHAR(26)     NOT NULL,
    session_id      VARCHAR(100) NOT NULL,  -- for guests
    customer_id     CHAR(26)     NULL,       -- if logged in
    consent_given   JSON         NOT NULL,   -- { necessary: true, analytics: false, marketing: true }
    ip_address      VARCHAR(45)  NOT NULL,   -- for audit
    user_agent      VARCHAR(500) NOT NULL,
    consented_at    TIMESTAMP    NOT NULL,
    version         VARCHAR(20)  NOT NULL,   -- consent policy version, e.g. "2026-01"
    PRIMARY KEY (id),
    INDEX idx_session (session_id),
    INDEX idx_customer (customer_id)
);
```

Storing consent server-side is required for GDPR audit compliance. Never rely solely on a cookie for proof of consent.

### Frontend Architecture

**Cookie Consent Banner (React component):**
- Shown on first visit if `cookie_consent_enabled = true`
- Three buttons: "Accept All", "Reject All", "Manage Preferences"
- "Manage Preferences" opens a modal with per-category toggles
- Consent choice stored in `localStorage` (for display suppression) AND `POST /consent` (for server audit log)
- Banner suppressed on checkout (essential cookies only, no consent friction at payment time)

**Conditional Tag Loading:**
```typescript
// resources/js/hooks/useCookieConsent.ts
// Returns current consent state from localStorage / Inertia shared props
// GTM DataLayer push of consent_update happens immediately on consent change
// Google Consent Mode v2 signals sent to GTM before any tags fire

// app.tsx — before GTM initialises:
window.dataLayer.push({
  event: 'consent_default',
  analytics_storage: consent.analytics ? 'granted' : 'denied',
  ad_storage: consent.marketing ? 'granted' : 'denied',
  functionality_storage: consent.functional ? 'granted' : 'denied',
});
```

This uses **Google Consent Mode v2** — tags fire but data is modelled/anonymised when consent is denied, rather than blocked entirely, which preserves some analytics signal within legal bounds.

### Consent Mode for Measurement Protocol

Server-side GA4 hits (Measurement Protocol) should only fire if the customer has given analytics consent, or if the hit is being sent to a cookieless/consent-aware endpoint. Do not send identifying server-side events for users who have denied consent.

### UK vs EU

Post-Brexit, the UK follows UK GDPR (essentially identical to EU GDPR for consent purposes). The `version` column on `cookie_consents` allows you to track which policy version the user consented to — useful when the policy changes and re-consent is needed.

---

## 23. EU & UK VAT Architecture

This extends §5.17 (Tax bounded context) with the specific rules needed for EU/UK selling.

### VAT Models

| Scenario | Rule |
|---|---|
| UK B2C | Charge UK VAT (20% standard, 5% reduced, 0% zero-rated) at all times |
| EU B2C (OSS) | Charge VAT at buyer's country rate (EU One Stop Shop threshold: €10,000/year) |
| EU B2B with valid VAT number | Zero-rate (reverse charge applies — buyer accounts for VAT) |
| UK B2B with valid VAT number | Still charge UK VAT (B2B UK is not zero-rated by default) |
| Digital goods to EU consumers | Always charge VAT at buyer's country rate regardless of OSS threshold |
| Outside EU/UK | No VAT (export, zero-rated) |

### VAT Number Validation

```sql
ALTER TABLE customer_addresses ADD COLUMN
    vat_number      VARCHAR(30) NULL,
    vat_validated   TINYINT(1)  NULL,    -- NULL=not checked, 0=invalid, 1=valid
    vat_validated_at TIMESTAMP  NULL;

ALTER TABLE orders ADD COLUMN
    customer_vat_number VARCHAR(30) NULL,
    vat_number_valid    TINYINT(1)  NULL;
```

```php
// src/Tax/Infrastructure/VatValidation/EuViesValidator.php
// Calls the EU VIES SOAP API to validate VAT numbers in real time
// Results cached in Redis for 24 hours (VIES can be slow)
// If VIES is down, fail open (allow the VAT exemption claim) and flag for manual review

// src/Tax/Infrastructure/VatValidation/UkHmrcValidator.php
// Calls HMRC VAT number validation API for UK VAT numbers
```

**Checkout flow:**
1. Customer enters VAT number in billing address form (B2B only — hide for B2C)
2. On blur/submit, async validation call via `POST /checkout/validate-vat`
3. If valid EU VAT number + billing address is EU: zero-rate the order (reverse charge)
4. Store VAT number + validation status on the order for audit

### Inclusive vs Exclusive Pricing

```php
// Configured per tax zone in the Tax bounded context:
// UK + EU: prices are INCLUSIVE of VAT (customer sees £10.00, VAT is within that)
// US + ROW: prices are EXCLUSIVE of tax (customer sees $10.00 + tax added at checkout)

// TaxCalculationService handles this via TaxRate::$type = 'inclusive'|'exclusive'
// The price waterfall (§5.3 Pricing) must receive the inclusivity flag to strip/add correctly
```

### VAT Invoice Requirements (EU/UK Legal)

Every B2B order and every EU/UK order must generate a VAT-compliant invoice containing:
- Seller's VAT registration number
- Customer's VAT number (if B2B)
- Tax breakdown per rate (separate lines for 20%, 5%, 0%)
- Invoice sequential number (must be gapless — use a dedicated invoice number sequence, not order ID)
- Invoice date, supply date if different
- "Reverse charge applies" statement for zero-rated B2B

```sql
CREATE TABLE invoice_sequences (
    year        SMALLINT NOT NULL,
    next_number INT      NOT NULL DEFAULT 1,
    PRIMARY KEY (year)
);
-- Use SELECT ... FOR UPDATE to get the next invoice number atomically
```

### OSS Registration Tracking

If EU B2C sales exceed the €10,000 threshold, the store must register for OSS (One Stop Shop) and file quarterly returns. Track this in the reporting module:

```
-- Reporting table updated by event projection
eu_b2c_sales_by_country
  country_code, tax_year, quarter, gross_sales, vat_collected, tax_rate
```

Admin dashboard widget shows current OSS threshold progress — useful to know when you're approaching the registration requirement.

---

## 24. Child / Variant Products — Architecture Clarification

> **Terminology note:** You called this a "grouped product" (Magento term). In Magento, a Grouped Product is actually different — it groups *separate* independent products on one page (e.g. a lamp + 3 bulb options, each separately purchasable). What you're describing — children with Size/Pack Quantity variations, same parent URL, own SKU/price/stock, not listed independently — is a **Configurable Product** (Magento) or **Product with Variants** in modern commerce. The blueprint uses this correct model below.

### What You Want

- Parent product: "Vitamin C 1000mg" (one URL, one product page)
- Child variants: "Pack of 30", "Pack of 60", "Pack of 90" — each with own SKU, own price, own stock
- Children do **not** appear in category listings or search results as separate products
- Children do **not** have their own `/products/{slug}` URLs
- On the product page, the user selects a child via a dropdown/swatches and the price updates
- Adding to cart adds a specific child (by `product_variant_id`), not the parent

### Product Type: `configurable`

The existing `configurable` product type in the blueprint covers this. Here is the precise model:

```sql
-- Parent product
products
  id, type = 'configurable', name, slug (has URL), sku (optional on parent), ...
  is_purchasable = 0   ← parent itself cannot be added to cart
  visibility = 'catalog_search'  ← appears in listings and search

-- Child variants (no independent URL, not visible in catalog)
product_variants
  id, product_id (FK → products)
  name          VARCHAR(200) NULL    -- NULL inherits parent name; set if different (e.g. "Blue Widget")
  sku           VARCHAR(100) NOT NULL UNIQUE
  price         BIGINT       NULL    -- NULL = inherit parent price; set to override
  compare_price BIGINT       NULL    -- NULL = inherit
  is_active     TINYINT(1)   NOT NULL DEFAULT 1
  sort_order    SMALLINT     NOT NULL DEFAULT 0
  visibility    ENUM('hidden') NOT NULL DEFAULT 'hidden'  -- NEVER in catalog/search

-- Variant-specific attribute values (what differentiates children)
-- e.g. Size=L, PackQty=30, Color=Red
product_variant_attributes
  variant_id    FK → product_variants
  attribute_id  FK → attributes
  value         VARCHAR(500)  -- 'L', '30', 'Red'

-- Per-variant stock (links to Inventory bounded context)
inventory_items
  product_variant_id  FK → product_variants   -- stock tracked at variant level
  source_id           FK → inventory_sources
  qty_available, qty_reserved, ...
```

### Variant Dimension Examples

| Parent Name | Variant Axis 1 | Variant Axis 2 | Each Variant Has |
|---|---|---|---|
| Cotton T-Shirt | Size: S/M/L/XL | Colour: Red/Blue | Own SKU, own stock, price override optional |
| Vitamin C | Pack Size: 30/60/90 caps | — | Own SKU, own stock, own price |
| Dog Food | Weight: 1kg/5kg/15kg | — | Own SKU, own stock, own price |
| Notebook | Cover: Softback/Hardback | Pages: Lined/Blank | Own SKU, own stock, price override optional |

### What "Same Name as Parent" Means

When `product_variants.name IS NULL`, the variant inherits the parent's name. The product page displays the parent name. The cart and order line items show the parent name + the selected variant attributes:

```
Cart line item display:
  "Cotton T-Shirt — Size: L, Colour: Red"    (parent name + attribute summary)
  SKU: TSHIRT-L-RED
  Price: £24.99
```

If a variant has its own name set (non-null), use that name in cart/order instead of the parent + attributes summary.

### What Is NOT a Configurable Product

| Scenario | Correct Type |
|---|---|
| A laptop sold with optional accessories (monitor, bag) where each can also be bought standalone | `bundle` — Magento-style grouped |
| A service or warranty with no shipping | `virtual` |
| A PDF guide or software download | `downloadable` |
| A single item with no variations | `simple` |

### Configurable Product on the Frontend

```typescript
// pages/Shop/Product/Show.tsx
// 1. Product page receives parent product + all variants + attribute options
// 2. User selects attribute values via swatches/dropdowns
// 3. Selected combination resolves to a specific variant_id
// 4. Price, stock status, and SKU update reactively (no page reload)
// 5. "Add to Cart" sends { product_id, variant_id, qty }
// 6. If a combination is out of stock, disable that option and show "Out of stock"
// 7. If a combination doesn't exist, show "Not available"

// Variant resolution matrix (computed from variant attribute values):
// { Size: 'L', Color: 'Red' } → variant_id: 'abc123'
// Pre-built as a JSON map when the page is server-rendered, no API call needed
```

### Inventory at Variant Level

Stock is **always tracked at the variant level**, never at the parent level. The parent product's "in stock" status is derived: it is in stock if at least one active variant has available stock.

```php
// src/Catalog/Domain/Product/Product.php
public function isInStock(): bool
{
    return $this->variants->some(fn($v) => $v->isActive() && $v->hasStock());
}
```

The parent `products` table has **no stock column**. Any report showing "product stock" is an aggregate of variant stock.

---

---

## 25. Refunds, Credit Memos & Order Notes

### 25.1 Refund Types

| Type | Description | Gateway call? |
|---|---|---|
| `full` | Entire order amount refunded | Yes (if paid online) |
| `partial_items` | Specific line items × qty refunded | Yes |
| `partial_amount` | Custom monetary amount (e.g. goodwill gesture) | Yes |
| `store_credit` | Refund issued as store credit / loyalty points, not money back | No |
| `manual` | Bank transfer orders — admin marks "refund sent" externally | No |

A single order can have **multiple** partial refunds, as long as the sum does not exceed the original `grand_total`. The system enforces this in the `CreateRefundHandler`.

---

### 25.2 Refund Schema

```sql
CREATE TABLE order_refunds (
    id                      CHAR(26)        NOT NULL,
    order_id                CHAR(26)        NOT NULL,    -- FK → orders
    created_by              CHAR(26)        NOT NULL,    -- FK → users (admin)
    type                    VARCHAR(30)     NOT NULL,    -- full|partial_items|partial_amount|store_credit|manual
    status                  VARCHAR(30)     NOT NULL DEFAULT 'pending',
    -- pending → processing → completed
    --                      ↘ failed

    reason                  VARCHAR(200)    NULL,        -- admin-entered reason
    internal_note           TEXT            NULL,        -- not shown to customer

    -- Line items being refunded (snapshot — not live joins)
    items_snapshot          JSON            NOT NULL,
    -- [{ order_item_id, name, sku, qty_refunded, unit_price, row_total, tax_amount }]

    -- Monetary breakdown (all in order currency, base currency stored alongside)
    currency                CHAR(3)         NOT NULL,
    exchange_rate_snapshot  DECIMAL(14,6)   NOT NULL,
    subtotal_refunded       BIGINT          NOT NULL DEFAULT 0,
    tax_refunded            BIGINT          NOT NULL DEFAULT 0,
    shipping_refunded       BIGINT          NOT NULL DEFAULT 0,
    adjustment_refund       BIGINT          NOT NULL DEFAULT 0,  -- goodwill/custom amount
    grand_total_refunded    BIGINT          NOT NULL,
    base_grand_total_refunded BIGINT        NOT NULL,            -- in base currency

    restock_items           TINYINT(1)      NOT NULL DEFAULT 0,  -- return to inventory?

    -- Gateway
    transaction_id          CHAR(26)        NULL,    -- FK → transactions
    gateway_refund_id       VARCHAR(200)    NULL,    -- Stripe refund_xxx / PayPal refund ID
    gateway_response        JSON            NULL,

    -- Credit Memo
    credit_memo_number      VARCHAR(50)     NULL,    -- sequential, linked to invoice
    credit_memo_generated_at TIMESTAMP      NULL,

    -- Lifecycle
    completed_at            TIMESTAMP       NULL,
    failed_at               TIMESTAMP       NULL,
    failure_reason          TEXT            NULL,
    created_at              TIMESTAMP       NOT NULL,
    updated_at              TIMESTAMP       NOT NULL,

    PRIMARY KEY (id),
    INDEX idx_order (order_id),
    INDEX idx_status (status, created_at)
);
```

---

### 25.3 Refund State Machine

```
pending
    → processing   (gateway call initiated)
        → completed  (gateway confirmed / manual marked)
        → failed     (gateway declined / timeout)
    → completed    (store_credit and manual types skip 'processing')
```

A `RefundStatus` enum lives in the `Orders` domain alongside `OrderStatus`. Same two-layer pattern — pure PHP enum, `spatie/laravel-model-states` only on the Eloquent model.

On `RefundCompleted`:
- Fire `RefundProcessed` domain event → outbox
- `ReinstateInventoryListener` (if `restock_items = true`)
- `UpdateOrderStatusListener` (if sum of all refunds = order total → transition order to `refunded`)
- `IssueLoyaltyRefundListener` (if payment was partly covered by points)
- `GenerateCreditMemoJob` dispatched to queue
- `SendRefundConfirmationEmailJob` dispatched to queue
- `RecordRefundInReportingJob` dispatched to queue
- GA4 `refund` event sent via Measurement Protocol

---

### 25.4 Admin Refund Flow (UI)

1. Admin opens Order > clicks **"Create Refund"**
2. Form shows all refundable line items with qty spinners (pre-filled with remaining refundable qty)
3. Checkboxes: refund shipping, refund adjustment amount (custom £ field)
4. Toggle: **"Return items to stock"**
5. Dropdown: **Refund method** — original payment / store credit *(bank transfer orders only show store credit or manual)*
6. Text field: **Reason** (required, for internal record and credit memo)
7. Text field: **Internal note** (optional, shown only to admin in order timeline)
8. Running total updates live as items/amounts are selected
9. Submit → `POST /admin/orders/{id}/refunds`
10. `CreateRefundHandler` validates:
    - Sum of this refund + all previous refunds ≤ order grand total
    - Each item qty refunded ≤ qty ordered − qty already refunded
    - Order status must be `processing`, `shipped`, `delivered`, `completed` (cannot refund a cancelled order)
11. Calls `PaymentGateway::refund()` for online orders
12. On success: redirect back to order with success notice; credit memo download link appears

---

### 25.5 Credit Memo

For every completed refund a **Credit Memo** PDF is generated. This is legally required for EU/UK VAT-registered sellers.

**Credit Memo must contain:**
- Credit memo number *(sequential, separate sequence from invoices — `credit_memo_sequences` table)*
- Reference: **"In respect of Invoice #INV-2026-00042"**
- Date issued
- Seller name, address, VAT registration number
- Customer name, address, VAT number (if B2B)
- Line-by-line breakdown of refunded items with quantity, unit price, row total
- Shipping credit (if applicable)
- VAT breakdown per rate (e.g. 20% VAT on £x, 0% on £y)
- **Grand total refunded**
- Statement: *"This credit memo cancels/reduces the VAT charged on the above invoice"*

```sql
CREATE TABLE credit_memo_sequences (
    year        SMALLINT NOT NULL,
    next_number INT      NOT NULL DEFAULT 1,
    PRIMARY KEY (year)
);
-- SELECT next_number FROM credit_memo_sequences WHERE year = ? FOR UPDATE
-- then UPDATE next_number = next_number + 1
```

Credit memo numbers format: `CM-2026-00001`. Stored on `order_refunds.credit_memo_number`. Gapless sequence (same `SELECT FOR UPDATE` pattern as invoice numbers from §23).

---

### 25.6 Order Notes & Comments

Every significant event on an order creates a **comment** in a unified order timeline. There is one table, three author types, and two visibility levels.

```sql
CREATE TABLE order_comments (
    id                      CHAR(26)        NOT NULL,
    order_id                CHAR(26)        NOT NULL,   -- FK → orders
    author_id               CHAR(26)        NULL,       -- NULL = system-generated
    author_type             VARCHAR(20)     NOT NULL,   -- 'admin' | 'system' | 'customer'
    comment                 TEXT            NOT NULL,
    is_visible_to_customer  TINYINT(1)      NOT NULL DEFAULT 0,
    is_customer_notified    TINYINT(1)      NOT NULL DEFAULT 0,  -- was email sent?
    status_changed_to       VARCHAR(50)     NULL,   -- populated for system status-change entries
    created_at              TIMESTAMP       NOT NULL,

    PRIMARY KEY (id),
    INDEX idx_order_timeline (order_id, created_at)
);
```

**Comment types and their defaults:**

| Who creates it | `author_type` | `is_visible_to_customer` | `is_customer_notified` | Example |
|---|---|---|---|---|
| Status change event | `system` | `1` | `1` | "Order shipped. Tracking: DHL 1Z999…" |
| Payment received | `system` | `1` | `1` | "Payment of £49.99 received via Stripe." |
| Payment failed | `system` | `0` | `0` | "Payment attempt failed: card declined." |
| Refund issued | `system` | `1` | `1` | "Refund of £12.00 processed to original card." |
| Tracking added | `system` | `1` | `1` | "Shipment created. Carrier: Royal Mail. Tracking: AB123456789GB" |
| Admin internal note | `admin` | `0` | `0` | "Customer called — agreed to exchange, awaiting return." |
| Admin customer note | `admin` | `1` | optional | "Your order has been delayed due to high demand. Expected dispatch: Monday." |
| Customer message | `customer` | `1` | N/A | "Can I change the delivery address?" |

**Admin UI — Order Timeline:**

```
Order #ORD-2026-00123                                       [Create Refund] [Add Note]
─────────────────────────────────────────────────────────────────
⬤ COMPLETED                                               29 Mar 2026, 14:32
  System: "Order marked as completed."

⬤ DELIVERED                                               27 Mar 2026, 09:15
  System: "Delivery confirmed by carrier."

✉ Admin note (visible to customer — email sent)           25 Mar 2026, 11:02
  "Your order has been dispatched. Tracking: AB123456789GB"

⬤ SHIPPED                                                 24 Mar 2026, 16:45
  System: "Shipment created. Royal Mail. Tracking: AB123456789GB"

🔒 Internal admin note                                     24 Mar 2026, 14:00
  "Wrapped in gift packaging as requested. Double-checked address."

⬤ PROCESSING                                               22 Mar 2026, 10:12
  System: "Payment of £49.99 received via Stripe. Intent: pi_3Px…"

⬤ PENDING PAYMENT                                          22 Mar 2026, 10:10
  System: "Order placed."
─────────────────────────────────────────────────────────────────
Add Note:  [ text area ]  [ ] Notify customer by email    [Save Note]
```

**Customer Account — Order Detail:**
Shows only `is_visible_to_customer = true` entries, in the same chronological order. Internal admin notes (`is_visible_to_customer = false`) are completely invisible.

---

### 25.7 Partial Refund on Variant Products

When a configurable product order is partially refunded, the refund references the `product_variant_id` (not just the product). Inventory reinstatement on `restock_items = true` returns stock to the specific variant, not the parent.

```php
// src/Orders/Application/Commands/CreateRefundHandler.php
// After refund completed + restock_items = true:
foreach ($refund->items as $item) {
    $this->inventory->release(
        variantId: $item->product_variant_id,
        sourceId:  $order->warehouse_id,
        qty:       $item->qty_refunded,
    );
}
```

---

### 25.8 Permissions

| Permission | Who |
|---|---|
| `orders.refund.full` | `admin`, `order-manager` |
| `orders.refund.partial` | `admin`, `order-manager`, `customer-support` |
| `orders.notes.create_internal` | `admin`, `order-manager`, `customer-support` |
| `orders.notes.create_customer_visible` | `admin`, `order-manager` |
| `orders.notes.view_internal` | `admin`, `order-manager`, `customer-support` |

`customer-support` can issue partial refunds and add internal notes but cannot issue full refunds — requires `order-manager` or `admin` approval.

---

*Blueprint v2.2 — Requirements update: multi-currency, payment gateways, global settings, EU cookie policy, EU/UK VAT, child/variant product architecture.*
*v2.3 — Added §25: Refunds, Credit Memos & Order Notes.*
*Last updated: 2026-03-29*
