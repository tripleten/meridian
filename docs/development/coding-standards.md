# Meridian — Coding Standards

**Project:** Meridian Ecommerce Platform
**Author:** L K Lalitesh <lalitesh@live.com>
**Company:** Bytics Lab
**Copyright:** 2026 Bytics Lab. All rights reserved.

---

## 1. PHP Standards

### Language Version & Declarations

Every PHP file must start with:

```php
<?php

declare(strict_types=1);
```

`declare(strict_types=1)` is mandatory on every file. No exceptions.
PHP 8.5+ features are permitted and encouraged: enums, readonly properties, union types, named arguments, first-class callables, `readonly` classes.

### General Rules

- Use `final` on every class unless inheritance is explicitly required
- Use `readonly` on value objects and DTOs
- No `public` properties on entities/aggregates — use typed accessors
- Prefer `match` over `switch`
- No `else` after a `return` or `throw`
- No functions outside classes (except helpers in `app/Support/helpers.php`)
- `strict_types` makes PHP type-check scalar arguments — lean on it

---

## 2. PHPDoc File Headers

Every PHP file in `src/` and `app/` requires this header, immediately after `declare(strict_types=1)`:

```php
<?php

declare(strict_types=1);

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Meridian\Orders\Domain
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

namespace Meridian\Orders\Domain;
```

The `@package` value mirrors the namespace exactly.

---

## 3. Class-Level PHPDoc

### Domain Aggregates & Entities

```php
/**
 * Order aggregate root.
 *
 * Represents a placed customer order and enforces all business rules
 * around state transitions, refunds, and cancellation eligibility.
 *
 * An Order is immutable once created except through explicit transition
 * methods that record domain events. The aggregate never reads from
 * external services — all required data is passed in.
 *
 * @package Meridian\Orders\Domain
 */
final class Order
{
```

### Value Objects

```php
/**
 * Immutable monetary amount with currency.
 *
 * Always stored as integer in the smallest currency unit (pence for GBP,
 * cents for USD). Never use floats for money. The currency code is ISO 4217.
 *
 * @package Meridian\Shared\Domain\ValueObjects
 */
final readonly class Money
{
```

### Domain Enums

```php
/**
 * Permitted states for an Order and the allowed transitions between them.
 *
 * Transition rules are enforced by the Order aggregate via canTransitionTo().
 * This enum must remain free of any framework dependencies.
 *
 * @package Meridian\Orders\Domain\Order
 */
enum OrderStatus: string
{
```

### Command Handlers

```php
/**
 * Handles the PlaceOrderCommand use case.
 *
 * Orchestrates: cart validation → pricing calculation → tax calculation →
 * inventory reservation → payment intent creation → order persistence →
 * outbox event recording → cart clearing.
 *
 * All side effects (emails, indexing, webhooks) are dispatched asynchronously
 * via the outbox after the transaction commits.
 *
 * @package Meridian\Orders\Application\Commands
 */
final class PlaceOrderHandler
{
```

### Eloquent Models (Infrastructure)

```php
/**
 * Eloquent persistence model for Orders.
 *
 * This is an infrastructure concern only. Business logic belongs in
 * Meridian\Orders\Domain\Order\Order, not here. This model handles
 * database reads/writes and relationship definitions.
 *
 * @package Meridian\Orders\Infrastructure\Persistence
 */
class EloquentOrder extends Model
{
```

### Controllers

```php
/**
 * Handles admin order management HTTP requests.
 *
 * Controllers are intentionally thin: validate → delegate to handler → respond.
 * No business logic here.
 *
 * @package App\Http\Controllers\Admin\Orders
 */
final class OrderController extends Controller
{
```

---

## 4. Method-Level PHPDoc

Only document methods where the signature alone is not self-explanatory.

### When PHPDoc is required on a method

- Non-obvious behaviour or side effects
- `@throws` for domain exceptions that callers must handle
- Complex parameter types that aren't obvious from type hints
- `@deprecated` with migration instructions

### When PHPDoc is NOT needed

- Simple getters/setters with typed signatures: `getId(): string`
- Factory methods with clear names: `public static function create(...): self`
- Standard CRUD controller actions with obvious names

### Examples

```php
/**
 * Transition the order to a new status.
 *
 * Records an OrderStatusChanged domain event. The event is not dispatched
 * here — it is written to the outbox by the repository on save.
 *
 * @throws InvalidOrderTransition if the transition is not permitted
 *         from the current status
 */
public function transitionStatus(OrderStatus $newStatus): void
{

/**
 * Calculate the final display price for a product in the given context.
 *
 * Applies the full price waterfall: customer group price → tier price →
 * special price → catalog rule → base price. Returns the lowest applicable price.
 *
 * @param  PriceContext $context  Holds customer group, qty, and date
 * @return Money                  In base currency; caller converts for display
 */
public function resolvePrice(PriceContext $context): Money
{

/**
 * @deprecated Use PlaceOrderHandler instead. Will be removed in v2.0.
 */
public function legacyPlaceOrder(): void
{
```

---

## 5. Inline Comments

Write comments that explain **why**, not **what**. The code already shows what.

```php
// ❌ Bad — describes what the code obviously does
$order->status = OrderStatus::Processing; // set status to processing

// ✅ Good — explains why
// Status transitions are enforced by the domain enum; direct assignment bypasses
// invariant checks. Always call transitionStatus() on the aggregate.
$order->transitionStatus(OrderStatus::Processing);

// ❌ Bad — restates the condition
if ($rate > 0.02) { // if rate is greater than 2 percent

// ✅ Good — explains the business rule
// Warn if the exchange rate has moved more than 2% since the customer
// added items to cart — per finance team's approved tolerance threshold.
if ($rateChange > 0.02) {
```

---

## 6. Naming Conventions

### PHP

| Construct | Convention | Example |
|---|---|---|
| Class | `PascalCase` | `OrderStatusChanged` |
| Interface | `PascalCase` (no `I` prefix) | `OrderRepository` |
| Enum | `PascalCase` | `OrderStatus` |
| Enum case | `PascalCase` | `OrderStatus::PendingPayment` |
| Method | `camelCase` | `transitionStatus()` |
| Property | `camelCase` | `$grandTotal` |
| Variable | `camelCase` | `$orderHandler` |
| Constant | `SCREAMING_SNAKE_CASE` | `MAX_REFUND_DAYS` |
| Migration | Snake, descriptive | `create_order_refunds_table` |
| DB table | `snake_case`, plural | `order_refunds` |
| DB column | `snake_case` | `grand_total_refunded` |
| DB pivot | Alphabetical singular pair | `channel_category`, `channel_product` |
| Event class | Past tense | `OrderPlaced`, `PaymentCaptured` |
| Command class | Imperative noun | `PlaceOrderCommand`, `CancelOrderCommand` |
| Handler class | Matches command | `PlaceOrderHandler`, `CancelOrderHandler` |
| Job class | Verb + noun + Job | `SendOrderConfirmationJob` |
| Listener | Present tense reaction | `ReserveInventoryOnOrderPlaced` |

### TypeScript / React

| Construct | Convention | Example |
|---|---|---|
| Component | `PascalCase` | `ProductCard.tsx` |
| Hook | `camelCase` with `use` prefix | `useCart.ts` |
| Type/Interface | `PascalCase` | `OrderData` |
| Prop interface | Component name + Props | `ProductCardProps` |
| Event handler | `handle` + event | `handleAddToCart` |
| CSS class | Tailwind utility only — no custom class names |  |

---

## 7. Money Handling Rules

These rules are non-negotiable and must be enforced in code review.

1. **Always integers.** Store and compute money as `int` (pence/cents). Never `float` or `string`.
2. **Never multiply prices directly.** Use `Money::multiply(int $qty): Money` helper.
3. **Division always rounds.** Use `Money::divide(int $divisor, int $roundingMode): Money`.
4. **Currency is always explicit.** No "naked" integer amounts — always wrapped in `Money`.
5. **Database columns are `BIGINT UNSIGNED`.** Never `DECIMAL` for PHP-side money logic.
6. **Display only.** Convert to float/string only at the final display/output boundary.

```php
// ❌ Wrong
$total = $unitPrice * $qty;
$discounted = $total * 0.9;

// ✅ Correct
$total = $unitPrice->multiply($qty);
$discounted = $total->applyPercentageDiscount(Percentage::of(10));
```

---

## 8. State Machine Rules

1. **Never set status directly** on an aggregate — always call `transitionStatus()`.
2. **Never bypass the domain enum** by calling `$eloquentModel->status->transitionTo()` — this skips domain invariants.
3. **Every transition must have a corresponding domain event** recorded in the aggregate.
4. **Test every invalid transition** explicitly — they must throw `InvalidOrderTransition`.

```php
// ❌ Wrong — bypasses domain invariants
$order->status = OrderStatus::Shipped;
$eloquentOrder->status->transitionTo(EloquentShipped::class);

// ✅ Correct
$order->transitionStatus(OrderStatus::Shipped);
```

---

## 9. Outbox / Event Dispatch Rules

1. **Never call `event()` inside a DB transaction.** Write to the outbox instead.
2. **Never call `dispatch()` inside a DB transaction** for side-effect jobs.
3. **The outbox write must be in the same transaction** as the state change it belongs to.
4. **Every side-effect job must implement idempotency.** Use `ShouldBeUnique` + `IdempotencyLog`.

```php
// ❌ Wrong — event can be lost if process dies after commit
DB::transaction(function () {
    $order->save();
    event(new OrderPlaced($order->id)); // not atomic
});

// ✅ Correct
DB::transaction(function () {
    $this->orderRepository->save($order);
    $this->outbox->record(new OrderPlaced($order->id)); // same transaction
});
```

---

## 10. Controller Rules

Controllers must contain no business logic. The full body of any action should read:

```
validate → build command/query DTO → delegate to handler → return response
```

```php
// ❌ Wrong — business logic in controller
public function store(Request $request): Response
{
    $product = Product::create($request->all());
    if ($product->stock < 0) {
        throw new \Exception('Invalid stock');
    }
    Cache::forget('products');
    event(new ProductCreated($product));
    return redirect()->route('admin.products.index');
}

// ✅ Correct — controller is a thin adapter
public function store(CreateProductRequest $request): Response
{
    $command = CreateProductCommand::fromRequest($request);
    $productId = $this->handler->handle($command);
    return redirect()->route('admin.catalog.products.edit', $productId);
}
```

---

## 11. Eloquent Model Rules

- No business logic in Eloquent models
- No `static::creating()` / `static::saving()` hooks with business rules — those belong in domain services
- `$guarded = []` is forbidden — always define `$fillable` explicitly
- All JSON columns must have an explicit `array` cast
- Encrypted columns must use `encrypted` or `encrypted:array` cast
- Relationships return typed `HasMany`, `BelongsTo` etc. — add `@return` PHPDoc for IDE support
- Soft deletes: only on `products` and `cms_pages` — not on `orders` (orders are never soft-deleted)

---

## 12. Laravel-Specific Rules

- **Route model binding** is permitted in controllers for read operations
- **Form Requests** handle HTTP-level validation only — business validation lives in domain/application
- **Policies** are registered for Spatie permission checks — use `$this->authorize()` in controllers
- **Queue jobs** extend `ShouldQueue`, implement `ShouldBeUnique` where idempotency is needed
- **Events** fired outside transactions use `event()` normally — only inside transactions must use outbox
- **Facades** are permitted in infrastructure and presentation layers — not in domain or application
- **Config files** are fine for infrastructure configuration — not for business rules

---

*Coding Standards v1.0*
*L K Lalitesh — Bytics Lab — 2026*
