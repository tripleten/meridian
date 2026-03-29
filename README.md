# Meridian

> A high-end, open-source ecommerce platform built on Laravel — DDD architecture, Magento-class features, modern React frontend.

[![PHP](https://img.shields.io/badge/PHP-8.5%2B-blue?logo=php)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-red?logo=laravel)](https://laravel.com)
[![React](https://img.shields.io/badge/React-19.x-61DAFB?logo=react)](https://react.dev)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Maintained by](https://img.shields.io/badge/maintained%20by-Bytics%20Lab-purple)](https://github.com/byticslab)

---

## What is Meridian?

Meridian is a production-grade ecommerce platform for businesses that need Magento-level functionality without Magento's complexity. It is built as a **DDD-inspired modular monolith** on Laravel 13, with a React + Inertia.js admin panel and storefront.

It is designed to be extended, self-hosted, and owned — not a SaaS.

---

## Feature Highlights

### Catalog
- Nested category tree with per-channel visibility
- Configurable products with variants (Size × Colour × Pack Quantity)
- Attribute sets and dynamic product attributes
- Brand management, media gallery, related/upsell/cross-sell
- Bulk import / export (CSV)

### Commerce
- Multi-currency with automatic exchange rate updates
- Full price waterfall: base price → special price → tier price → group price → catalog rules
- Cart price rules, coupon engine, flash sales, gift cards
- Guest and customer checkout
- Stripe, PayPal, and Bank Transfer payment gateways
- Configurable tax engine: UK VAT, EU VAT (OSS), inclusive/exclusive pricing, VAT number validation
- Multi-source inventory with reservations

### Orders
- Full order lifecycle with state machine (pending → processing → shipped → delivered → completed)
- Order notes and admin/customer timeline
- Full and partial refunds with credit memo generation
- Return / RMA workflow
- PDF invoices and credit memos (VAT-compliant, gapless sequential numbering)

### Admin Panel
- Role-based access control (Spatie Permission)
- Fine-grained permissions per bounded context
- Activity log and audit trail on all critical models
- Async report generation (sales, revenue, customer cohorts, inventory, promotion effectiveness)
- Background job monitoring via Laravel Horizon

### Marketing & SEO
- GTM DataLayer architecture with GA4 ecommerce event mapping
- Server-side GA4 Measurement Protocol for purchase events
- Google Merchant Center feed generation
- UTM attribution persisted from session to order
- XML sitemap, URL rewrites, Schema.org structured data
- Loyalty / reward points, wishlist, product comparison

### CMS & Settings
- Simple CMS: pages, reusable content blocks
- SEO fields on every product, category, and CMS page
- Global settings: header/footer scripts, social share toggle, social links
- EU/UK GDPR cookie consent with Google Consent Mode v2
- Per-channel configuration for multi-storefront

---

## Architecture

Meridian follows **Domain-Driven Design** with a modular monolith structure.

```
src/
├── Shared/           Value objects, outbox, channel context
├── IdentityAccess/   Auth, roles, permissions
├── Catalog/          Products, categories, attributes, brands
├── Pricing/          Price waterfall, tier prices, currency
├── Inventory/        Multi-source stock, reservations
├── Cart/             Guest + customer carts
├── Checkout/         Checkout orchestration
├── Orders/           Order lifecycle, state machine, snapshots
├── Payments/         Stripe, PayPal, Bank Transfer
├── Fulfillment/      Shipments, tracking, RMA
├── Promotions/       Cart rules, coupons, catalog rules
├── Tax/              Tax classes, zones, rates, VAT/GST
├── Customers/        Profiles, address book, loyalty
├── CmsSeo/           CMS pages, URL rewrites, sitemap
├── Marketing/        GTM DataLayer, feeds, UTM attribution
├── Reporting/        Async read models, report generation
├── Search/           Meilisearch integration, faceted nav
└── Integrations/     Webhooks, ERP/PIM/WMS connectors
```

Each module is split into four layers:

| Layer | Responsibility |
|---|---|
| **Domain** | Pure PHP business rules — zero framework dependencies |
| **Application** | Use cases, commands, queries, DTOs |
| **Infrastructure** | Eloquent, jobs, external API adapters |
| **Presentation** | Controllers, React pages, API resources |

Full architecture documentation: [`docs/architecture/ecommerce-platform-blueprint-v2.md`](docs/architecture/ecommerce-platform-blueprint-v2.md)

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP 8.5 |
| Frontend | React 19, Inertia.js v3, TypeScript |
| Styling | Tailwind CSS v4, Radix UI |
| Database | MySQL 8.x |
| Cache / Queue | Redis + Laravel Horizon |
| Search | Meilisearch via Laravel Scout |
| Media | Spatie Media Library, Intervention Image |
| Auth | Laravel Fortify, Spatie Permission |
| Payments | Stripe, PayPal |
| PDF | DomPDF |
| Testing | Pest |

---

## Requirements

- PHP 8.5+
- Composer 2.x
- Node.js 20+ and npm
- MySQL 8.x
- Redis 7+
- Meilisearch 1.x

---

## Installation

```bash
# Clone
git clone https://github.com/byticslab/meridian.git
cd meridian

# Install dependencies
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Configure your .env — set DB_*, REDIS_*, MEILISEARCH_HOST

# Database
php artisan migrate
php artisan db:seed

# Search index
php artisan scout:import "Meridian\Catalog\Infrastructure\Persistence\EloquentProduct"

# Run
composer run dev
```

Open [http://localhost:8000](http://localhost:8000) for the storefront.
Open [http://localhost:8000/admin](http://localhost:8000/admin) for the admin panel.

Default admin credentials are set in `database/seeders/AdminUserSeeder.php`.

---

## Documentation

| Document | Description |
|---|---|
| [Architecture Blueprint](docs/architecture/ecommerce-platform-blueprint-v2.md) | Full DDD architecture, bounded contexts, schema decisions |
| [Implementation Guide](docs/development/implementation-guide.md) | Build order, entity checklist, layer rules, environment setup |
| [Coding Standards](docs/development/coding-standards.md) | PHPDoc standards, naming conventions, Money rules, testing rules |

---

## Development

```bash
# Run all tests
php artisan test

# Run only domain unit tests (fast — no DB)
php artisan test --testsuite=Domain

# Code style
composer run lint

# Type check (TypeScript)
npm run types:check
```

See [docs/development/coding-standards.md](docs/development/coding-standards.md) for contribution guidelines.

---

## Contributing

Contributions are welcome. Please read the [implementation guide](docs/development/implementation-guide.md) and [coding standards](docs/development/coding-standards.md) before submitting a PR.

1. Fork the repository
2. Create a feature branch: `git checkout -b feat/catalog-bundle-products`
3. Follow the entity checklist in the implementation guide
4. Ensure all tests pass: `php artisan test`
5. Submit a pull request with a clear description

---

## Roadmap

- [ ] REST API v1 for headless / mobile
- [ ] Multi-store UI (schema is already channel-aware)
- [ ] B2B features (company accounts, quote requests, bulk pricing)
- [ ] Subscription / recurring orders
- [ ] AI-powered product recommendations
- [ ] GraphQL API

---

## License

Meridian is open-source software licensed under the [MIT licence](LICENSE).

---

## Credits

**Author:** L K Lalitesh <lalitesh@live.com>
**Company:** [Bytics Lab](https://github.com/byticslab)

Built with [Laravel](https://laravel.com), [React](https://react.dev), and a lot of coffee.
