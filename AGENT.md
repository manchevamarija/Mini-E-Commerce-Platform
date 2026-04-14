# AGENT.md — Mini E-Commerce Platform

> This file guides AI coding assistants (Claude, Cursor, Copilot) on this project.
> **All generated code MUST follow this structure, conventions, and rules.**

---

## Project Overview

| | |
|---|---|
| **Name** | Mini E-Commerce Platform |
| **Stack** | Laravel 13.x, Livewire 4 (Volt), Tailwind CSS 3.x, Alpine.js 3.x, MySQL |
| **Architecture** | Domain-Driven Design (DDD) |
| **PHP Version** | 8.3 |
| **Testing** | Pest 4 / PHPUnit |

---

## How I Used AI in This Project

I used Claude (claude.ai) as my primary AI assistant throughout this project.
Rather than writing vague prompts, I wrote specific, architecture-aware prompts like:

> "Create a `CheckoutService` in the `OrderManagement` domain. It should validate all cart items
> have sufficient stock, create an Order with OrderItems, call `PaymentSimulatorService`
> (fail if total > $999), decrement stock on success, clear the cart, and return the Order.
> Wrap everything in a DB transaction. Throw a descriptive exception on failure."

All AI-generated code was reviewed, tested, and committed with clear git messages.

---

## Domain Map

| Domain | Responsibility | Models |
|---|---|---|
| **IdentityAndAccess** | Auth, roles | `User` |
| **ProductCatalog** | Vendors, products, marketplace | `Vendor`, `Product` |
| **Cart** | Cart logic, stock validation | `Cart`, `CartItem` |
| **OrderManagement** | Checkout, orders, status | `Order`, `OrderItem` |

### Relationships
User ──has one──▶ Vendor        (if role = vendor)
User ──has one──▶ Cart
Vendor ──has many──▶ Product
Cart ──has many──▶ CartItem ──belongs to──▶ Product
Order ──belongs to──▶ User
Order ──has many──▶ OrderItem ──belongs to──▶ Product
OrderItem ──belongs to──▶ Vendor

---

## Directory Structure
app/
├── Domain/
│   ├── IdentityAndAccess/
│   │   ├── Enums/                # UserRole (buyer, vendor, admin)
│   │   └── Models/               # User
│   ├── ProductCatalog/
│   │   ├── Actions/              # CreateProductAction, UpdateProductAction
│   │   ├── DTOs/                 # CreateProductDTO
│   │   ├── Enums/                # ProductStatus
│   │   └── Models/               # Vendor, Product
│   ├── Cart/
│   │   ├── Actions/              # AddToCartAction, RemoveFromCartAction
│   │   ├── Models/               # Cart, CartItem
│   │   └── Services/             # CartStockValidationService
│   └── OrderManagement/
│       ├── Actions/              # CreateOrderAction, UpdateOrderStatusAction
│       ├── DTOs/                 # CreateOrderDTO
│       ├── Enums/                # OrderStatus, PaymentMethod
│       ├── Models/               # Order, OrderItem
│       └── Services/             # CheckoutService, PaymentSimulatorService
├── Http/
│   └── Middleware/               # RoleMiddleware
resources/views/livewire/
├── market/                       # index, show, vendor (public profile)
├── cart/                         # index
├── checkout/                     # index
├── vendor/                       # products/index, products/create, products/edit
│                                 # orders/index, dashboard, profile
└── buyer/orders/                 # index, show, confirmation
database/
├── migrations/
├── seeders/                      # VendorSeeder, ProductSeeder
└── factories/                    # One per model
tests/
├── Feature/                      # CheckoutTest, RoleAccessTest, OrderStatusTest,
│                                 # CartStockValidationServiceTest, Auth tests
└── Unit/                         # PaymentSimulatorServiceTest

---

## Naming Conventions

| Type | Pattern | Example |
|---|---|---|
| Model | Singular PascalCase | `Product`, `CartItem` |
| Table | Plural snake_case | `products`, `cart_items` |
| Action | Verb + Noun + Action | `CreateProductAction` |
| DTO | Noun + DTO | `CreateProductDTO` |
| Enum | Noun | `OrderStatus`, `UserRole` |
| Service | Noun + Service | `CheckoutService` |

---

## Architecture Rules

1. **Controllers are thin** — validate, call Action/Service, return response
2. **Actions do one thing** — `CreateOrderAction` only creates an order
3. **Services orchestrate** — `CheckoutService` coordinates multiple actions in a transaction
4. **Models own relationships and scopes** — `Product::scopeActive()`, `scopeForVendor()`
5. **DTOs carry data** — never pass raw `$request` into an Action
6. **Enums always** — never use magic strings like `'pending'` or `'buyer'`

---

## Database Rules

- **Primary keys:** ULIDs → `$table->ulid('id')->primary()`
- **Foreign keys:** always constrained → `$table->foreignUlid('vendor_id')->constrained()->cascadeOnDelete()`
- **Money:** `decimal(10, 2)` — never `float`
- **Soft deletes:** on `products` and `orders` only
- **Indexes:** on every column used in `WHERE`, `ORDER BY`, or `JOIN`

---

## Business Rules

### Payment Simulation
Orders with total **over $999 fail**. Handled by `PaymentSimulatorService`.

### Stock Validation
Checked at **two points:**
- Add to cart → warns the user
- Checkout → rejects the order

### Checkout Flow (`CheckoutService`)
Must be atomic inside `DB::transaction()`:
1. Validate cart is not empty
2. Validate all items have sufficient stock
3. Create `Order` + `OrderItem` records
4. Call `PaymentSimulatorService` → throw on failure
5. Decrement product stock
6. Clear the cart
7. Return the Order

### Order Status Transitions
pending → paid → shipped → delivered
Forward only. No skipping. Enforced by `OrderStatus::canTransitionTo()`.

---

## Product Recommendations

### Related Products (same vendor)
- Same vendor as current product
- Exclude current product
- Active only
- Sorted by price ascending
- Max 4 items

### Almost Gone (low stock)
- Stock > 0 and stock <= 5
- Active only
- Exclude current product
- Sorted by price ascending
- Max 4 items
- Label: "Almost Gone — Grab Them Fast!"

---

## Vendor Public Profile
- Each vendor has a public profile page at `/vendors/{vendor}`
- Shows all active products from that vendor
- Vendor names on marketplace cards and product detail pages are clickable links

---

## Stock Badge Rules

| Condition | Badge |
|---|---|
| `stock = 0` | "Out of stock" (black) |
| `stock <= 5` | "Only X left!" (red) |

Shown on: marketplace cards, product detail page, recommendation cards.

---

## Edge Cases

| Scenario | Status | Solution |
|---|---|---|
| Two buyers purchase last item simultaneously | TODO | Use `SELECT ... FOR UPDATE` row locking |
| Vendor deletes product in buyer's cart | Handled | Checkout fails with stock error |
| Cart empty at checkout | Handled | `RuntimeException` thrown |
| Stock gone between add-to-cart and checkout | Handled | Double validation catches this |

---

## Test Coverage
42 tests, 95 assertions — all passing
Feature/CheckoutTest                  checkout success, payment fail, stock fail
Feature/RoleAccessTest                guest redirects, buyer/vendor 403s
Feature/OrderStatusTest               valid + invalid transitions
Feature/CartStockValidationServiceTest  pass + fail scenarios
Unit/PaymentSimulatorServiceTest      under, at, and over $999

Full Breeze Auth test suite


---

## Common Commands

```bash
php artisan serve                       # Start dev server
php artisan migrate:fresh --seed        # Reset DB with sample data
php artisan test                        # Run all 42 tests
php artisan test --filter=CheckoutTest  # Run specific test
php artisan optimize:clear              # Clear all caches
```

---

## Final Rule

> If generated code does not follow this file, it is considered incorrect for this project.
Зачувај па:
bashgit add .
git commit -m "Update AGENT.md with complete project documentation"
git push origin master:main --force
