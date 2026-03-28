# CoreInventory – Project Context & Technical Plan

> **Last Updated:** 2026-03-28
> **Stack:** Laravel 12 · PHP 8.2+ · MySQL (migrated from PostgreSQL) · Vite · Blade Templates

---

## 1. What Is This Project?

**CoreInventory** is a full-stack Inventory Management System (IMS) built with Laravel 12. It is an
Odoo-inspired, warehouse-centric system designed to track physical stock through source documents
(Receipts, Deliveries, Transfers, Adjustments). The stock balance is never stored directly on any
product — it is always **computed on-the-fly** from the `stock_ledger` table (double-entry style).

---

## 2. Core Architecture

```
User (Browser)
    |
    v
Laravel Router (routes/web.php)
    |
    v
Middleware (auth / guest)
    |
    v
Controllers (app/Http/Controllers/)
    |
    v
Eloquent Models (app/Models/)
    |
    v
MySQL Database (via PDO / pdo_mysql)
```

### Key Design Principle — Stock Ledger Pattern
Stock is **NEVER** stored on products. Instead, every inventory movement (receipt, delivery,
transfer, adjustment) writes a signed `quantity_change` row to `stock_ledger`.
Current stock = `SUM(quantity_change)` for a given product + location.

---

## 3. Module Breakdown

| Module       | Controller               | Description                                                                   |
|--------------|--------------------------|-------------------------------------------------------------------------------|
| Auth         | AuthController           | Login, Register, OTP-based password reset (session OTP, logged to file)       |
| Dashboard    | DashboardController      | KPI cards: total products, low-stock count, pending receipts/deliveries, etc. |
| Products     | ProductController        | CRUD for products (name, SKU, category, unit of measure, reorder level)       |
| Receipts     | ReceiptController        | Goods-in documents (vendor to internal location). Validate = post to ledger   |
| Deliveries   | DeliveryController       | Goods-out documents (internal to customer). Validate = post to ledger         |
| Transfers    | TransferController       | Internal moves between locations. Validate = post to ledger                   |
| Adjustments  | AdjustmentController     | Physical count vs recorded stock. Validate = post +/- difference to ledger    |
| Ledger       | LedgerController         | Read-only view of all stock_ledger rows                                       |
| Settings     | SettingController        | Warehouse CRUD + Location CRUD                                                |

---

## 4. Database Schema (12 core tables)

| #  | Table          | Purpose                                                              |
|----|----------------|----------------------------------------------------------------------|
| 1  | users          | Authenticated users with roles: manager or staff                     |
| 2  | warehouses     | Physical warehouses (name, code, address)                            |
| 3  | locations      | Sub-locations within warehouses (internal/vendor/customer/inv_loss)  |
| 4  | products       | Product catalogue (name, SKU, category, UoM, reorder_level)         |
| 5  | receipts       | Goods-in header (reference, vendor, status, expected_date)           |
| 6  | receipt_items  | Lines of a receipt (receipt_id, product_id, quantity)                |
| 7  | deliveries     | Goods-out header (reference, customer, status, scheduled_date)       |
| 8  | delivery_items | Lines of a delivery (delivery_id, product_id, quantity)              |
| 9  | transfers      | Internal transfer header (from_location, to_location, status)        |
| 10 | transfer_items | Lines of a transfer (transfer_id, product_id, quantity)              |
| 11 | adjustments    | Physical count vs recorded stock (diff auto-calculated server-side)  |
| 12 | stock_ledger   | SOURCE OF TRUTH — every stock movement as a signed quantity_change   |

### Laravel System Tables
migrations, cache, cache_locks, sessions, jobs, job_batches, failed_jobs, password_reset_tokens

---

## 5. Models & Relationships

```
Warehouse --< Location --< Transfer (from_location_id / to_location_id)
                       --< Adjustment (location_id)
                       --< StockLedger (location_id)

Product --< ReceiptItem  --> Receipt
        --< DeliveryItem --> Delivery
        --< TransferItem --> Transfer
        --< Adjustment
        --< StockLedger

StockLedger (manual polymorphic: reference_type STRING + reference_id BIGINT)
    |-- App\Models\Receipt
    |-- App\Models\Delivery
    |-- App\Models\Transfer
    |-- App\Models\Adjustment
```

Note: StockLedger uses a MANUAL polymorphic (not Laravel's standard morphTo) because
reference_type stores the full class name as a plain string + reference_id as unsignedBigInteger.

---

## 6. Authentication Flow

- Login:   Email + Password -> Auth::attempt() -> session regenerated -> redirect to dashboard
- Register: Name, Email, Password -> role defaults to "manager"
- OTP Reset: Email -> 6-digit OTP stored in PHP session (not DB) -> verified -> password updated
- Session driver: database (sessions table) — NOT file-based

---

## 7. Stock Movement Flow (Example: Receipt Validate)

```
1. Manager creates Receipt (status: Draft)
2. Adds ReceiptItems (product_id, quantity)
3. Status progression: Draft -> Waiting -> Ready (manual)
4. Manager clicks "Validate"
5. ReceiptController::validateReceipt() runs DB::transaction():
       FOR EACH receipt_item:
           StockLedger::create([
               product_id      => item.product_id,
               location_id     => receipt.destination_location_id,
               reference_type  => 'App\Models\Receipt',
               reference_id    => receipt.id,
               quantity_change => +item.quantity   (POSITIVE = stock IN)
           ])
       receipt.update(status => 'Done')
```

Same pattern for:
- Delivery   -> quantity_change = NEGATIVE (stock OUT)
- Transfer   -> TWO entries: negative from source, positive at destination
- Adjustment -> quantity_change = (physical_count - recorded_stock), can be + or -

---

## 8. Database Change: PostgreSQL -> MySQL

### Files Modified

| File                                                               | Change Made                                   |
|--------------------------------------------------------------------|-----------------------------------------------|
| .env                                                               | DB_CONNECTION=mysql, port=3306, user/pass set |
| database/migrations/2026_03_14_000001_create_users_table.php       | timestampTz() changed to timestamp()          |

### Why Only 2 Files?
All other migrations use 100% database-agnostic Blueprint methods:
  id(), string(), integer(), enum(), foreignId(), timestamps(), text(), date()
These compile identically for SQLite, MySQL, and PostgreSQL.

config/database.php already has a complete [mysql] connection block — no changes needed.
The .env DB_CONNECTION value is the single switch that picks the active driver.

### MySQL Prerequisites (one-time setup)
```sql
CREATE DATABASE coreinventory CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Grant access to your MySQL user
```
Then run:  php artisan migrate:fresh

---

## 9. Key Files Quick Reference

| File                          | What it does                                          |
|-------------------------------|-------------------------------------------------------|
| .env                          | Active config: DB, app key, mail, session, cache      |
| config/database.php           | Defines sqlite/mysql/mariadb/pgsql/sqlsrv connections |
| routes/web.php                | All web HTTP routes (auth + protected)                |
| database/migrations/ (15)     | Full DB schema history, Laravel-managed               |
| DATABASE_SCHEMA.sql           | Raw PostgreSQL DDL (docs only, not used by artisan)   |
| app/Models/ (12 files)        | Eloquent models + relationships                       |
| app/Http/Controllers/ (10)    | Business logic per module                             |
| composer.json                 | PHP deps: Laravel 12, Tinker                          |
| package.json / vite.config.js | Frontend build: Vite                                  |

---

## 10. Open Issues / Future Work

- [ ] DATABASE_SCHEMA.sql is still PostgreSQL DDL — keep as reference or rewrite for MySQL
- [ ] OTP is in PHP session only (not persisted). Under load-balanced servers this will break.
      Consider storing otp_code + otp_expires_at in the users table (columns exist in schema)
- [ ] No database seeders — add sample Warehouse + Location + Product data for development
- [ ] Role-based authorization (manager vs staff) exists in User model but middleware gates
      are NOT applied on most routes — enforce with a Policy or middleware
- [ ] The "Canceled" status appears in Controller queries but is missing from some migration enums
      (e.g. transfers only has Draft/Ready/Done). Audit and align enum values across all tables.

---

*Auto-generated by full code analysis on 2026-03-28.*
Email: admin@coreinventory.local
Password: Admin@12345