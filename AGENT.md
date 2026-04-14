# AGENT.md — Mini E-Commerce Platform

> This file is used to guide AI coding assistants (Claude, Cursor, Copilot).  
> **All generated code MUST follow this structure, conventions, and rules.**

---

## Project Overview

- **Name:** Mini E-Commerce Platform
- **Stack:** Laravel 13.x, Livewire 4 (Volt), Tailwind CSS 3.x, Alpine.js 3.x, MySQL
- **Architecture:** Domain-Driven Design (DDD)
- **PHP Version:** 8.3
- **Testing:** Pest 4 / PHPUnit

---

## Goal

Build a **scalable mini e-commerce system** with:

- Vendor marketplace
- Cart system
- Checkout flow
- Order management
- Stock validation
- Payment simulation

---

## Domain Map

### IdentityAndAccess
- **Responsibility:** Authentication & roles
- **Models:** `User`

### ProductCatalog
- **Responsibility:** Products & vendors
- **Models:** `Vendor`, `Product`

### Cart
- **Responsibility:** Cart logic
- **Models:** `Cart`, `CartItem`

### OrderManagement
- **Responsibility:** Orders & checkout
- **Models:** `Order`, `OrderItem`

---

## Relationships

- User → has one → Cart
- User → has one → Vendor (if role = vendor)
- Vendor → has many → Products
- Cart → has many → CartItems → belongs to → Product
- Order → belongs to → User
- Order → has many → OrderItems → belongs to → Product
- OrderItem → belongs to → Vendor

---

## Directory Structure

app/  
├── Domain/  
├── Http/  
resources/views/livewire/  
database/  
tests/

---

## Naming Conventions

- **Models:** Singular PascalCase → `Product`, `OrderItem`
- **Tables:** Plural snake_case → `products`, `order_items`
- **Actions:** Verb + Noun + Action → `CreateProductAction`
- **DTOs:** Noun + DTO → `CreateOrderDTO`
- **Enums:** `UserRole`, `OrderStatus`
- **Services:** `CheckoutService`, `PaymentSimulatorService`

---

## Architecture Rules

- **Controllers MUST be thin**  
  → Only validation + call Actions/Services

- **Actions MUST do ONE thing**  
  → Example: `CreateOrderAction`

- **Services orchestrate logic**  
  → Example: `CheckoutService`

- **Models handle relationships & scopes**  
  → Example: `Product::scopeActive()`

- **NEVER pass `$request` into Actions**  
  → Always use DTOs

- **NEVER use magic strings**  
  → Always use Enums

---

## Database Rules

## Primary Keys
- Use **ULIDs**
- Example: `$table->ulid('id')->primary();`

## Foreign Keys
- Always use **constraints + cascade**
- Example: `$table->foreignUlid('user_id')->constrained()->cascadeOnDelete();`

## Money
- Use: **decimal(10, 2)**
- ❌ Never use float

## Soft Deletes
Enable soft deletes on:
- products
- orders

## Indexes
Add indexes on:
- WHERE
- ORDER BY
- JOIN

---

## Business Rules

## Payment Simulation
- Orders **over $999 must fail**
- Implemented in: `PaymentSimulatorService`

## Stock Validation
Stock must be checked:

- **Add to cart** → warn/block
- **Checkout** → strict validation

## Checkout

`CheckoutService` MUST:

- Ensure cart is not empty
- Validate stock
- Create Order
- Create OrderItems
- Call PaymentSimulatorService
- Decrement stock
- Clear cart
- Return Order

Wrap everything in:
DB::transaction(function () {});

Must be **atomic**:
- order creation
- order items
- payment
- stock decrement
- cart clear

On failure → rollback + throw exception

---

## Order Status Flow

Allowed:
pending → paid → shipped → delivered

Rules:
- forward only
- no backwards
- invalid transitions rejected

Handled by:
`OrderStatus::canTransitionTo()`

---

## Edge Cases

## Concurrent purchase (last item)
- Status: TODO
- Needs: SELECT FOR UPDATE

## Deleted product in cart
- Handled → fails at checkout

## Empty cart
- Handled → RuntimeException

## Stock mismatch
- Handled → double validation

---

## Test Coverage

Includes:

- Checkout success
- Payment failure
- Stock failure
- Auth protection
- Role authorization
- Order transitions
- Cart validation
- Payment simulation

## Example Tests
- Feature/CheckoutTest
- Feature/RoleAccessTest
- Feature/OrderStatusTest
- Feature/CartStockValidationTest
- Unit/PaymentSimulatorServiceTest

---

## Common Commands

php artisan serve  
php artisan migrate:fresh --seed  
php artisan test  
php artisan test --filter=CheckoutTest  
php artisan optimize:clear

---

## AI Output Requirements

AI MUST:

- follow DDD structure
- use correct namespaces
- keep controllers thin
- use Actions & Services properly
- use DTOs (no request)
- use Enums (no strings)
- use ULIDs
- use decimal(10,2)
- respect order flow
- enforce payment rule (>999 fail)
- validate stock twice
- use DB transactions
- throw clear exceptions

---

## Final Rule

**If code does not follow this file → it is WRONG.**
