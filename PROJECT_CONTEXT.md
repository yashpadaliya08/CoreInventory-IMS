# CoreInventory – Project Context & Technical Plan

> **Last Updated:** 2026-03-28  
> **Stack:** Laravel 12 · PHP 8.2+ · MySQL · Vite · Blade Templates

---

## 1. What Is This Project?
**CoreInventory** is a full-stack Inventory Management System (IMS) built with Laravel 12. It is an Odoo-inspired, warehouse-centric system designed to track physical stock through source documents (Receipts, Deliveries, Transfers, Adjustments). The stock balance is never stored directly on any product — it is always **computed on-the-fly** from the `stock_ledger` table (double-entry style).

---

## 2. Development History & Achievements

### ✅ Phase 1: Authentication & Role-Based Access Control (RBAC)
- Expanded the `users` table to support `admin` and `staff` roles alongside `manager`.
- Created robust `RoleMiddleware.php` to securely gate HTTP requests based on hierarchy:
  - **Admin:** Full CRUD, User Management, Infrastructure / Warehouse Settings.
  - **Manager:** Can read, create, edit, and validate stock movements. Cannot delete or access settings.
  - **Staff:** Read-only access and ability to draft documents.
- Split `routes/web.php` into strictly defined tiered middleware groups.
- Transformed generic UI buttons into conditional Blade buttons using new User model helpers (`isAdmin()`, `isManagerOrAbove()`).
- Migrated password reset OTP from fragile PHP Session storage into a robust Database storage system with Rate Limiting.
- Created `UserManagementController` and an Admin-only dashboard for managing staff inside the IMS.

### ✅ Phase 2: Data Integrity & Hardening
- Implemented **SoftDeletes** across 7 core system tables (`products`, `warehouses`, `locations`, `receipts`, `deliveries`, `transfers`, `adjustments`). 
- If a product or location is deleted by an Admin, it is simply hidden from UI but retained in the DB. This guarantees the `stock_ledger` mathematical history is **never corrupted** by orphaned ledger pointers.
- Added `created_at` and `updated_at` timestamps to all models to natively support Eloquent's advanced tracking features.

### ✅ Phase 3: Database Seeding & Demo Data 
- Created an organized set of dependency-aware Database Seeders:
  1. `UserSeeder`: Creates standard mock users for Admin, Manager, and Staff.
  2. `WarehouseSeeder`: Creates 2 dynamic warehouses with Input/Output/Internal zones.
  3. `ProductSeeder`: Creates 11 varied products (Raw Mats, Finished Goods, Consumables).
  4. `ReceiptSeeder` & `MovementSeeder`: Simulates realistic historical operations (validated stock entering, leaving, and moving within the system to populate the stock ledger correctly).

---

## 3. Project Roadmap / What To Build Next

If you are picking this project back up, these are the recommended immediate next steps:

### 🚀 Phase 4: Production Readiness (High Priority)
1. **Disable Global Debugging:** Turn `APP_DEBUG=false` in the `.env` file to prevent raw stack traces from exposing system architecture.
2. **Setup Asynchronous Queues:** The current OTP mailing process is synchronous (making password resets slow). Set up Laravel Queues (`php artisan queue:work`) to handle outbound emails in the background.
3. **Connect Real SMTP Mailer:** Switch mailers from the local `.log` file to a real production SMTP service (SendGrid, Mailgun) for password resets.
4. **Optimize Boot Times:** Run `php artisan optimize` to compress routes, config, and views for production speeds.

### 🛠️ Phase 5: Reporting Engine & Settings
1. **Advanced PDF Generation:** Introduce a library like `barryvdh/laravel-dompdf` so managers can click "Export" on any Delivery/Receipt to generate printable physical manifests for truck drivers and receivers.
2. **Live Charting:** Add Chart.js to the Dashboard so admins can see visual throughput trends (e.g., total items dispatched over the last 30 days).
3. **Deep Settings UI:** Wire up the backend settings controller so Admins can freely define custom product categories and internal company data (Logo, Company Tax ID).

### 🤖 Phase 6: Model Context Protocol (MCP) Integration
Integrate standard AI dev tools seamlessly into the CoreInventory ecosystem by linking the local repository with `Claude Desktop` via MCP:
- **MySQL Database Server:** Connect live DB to standard LLMs to run complex natural language stock queries.
- **Filesystem Server:** Grant the AI full local file manipulation for immediate codebase upgrades without copy/pasting payload files.

---

## 4. Key Quick Reference

*   **Database Setup:** `php artisan migrate:fresh --seed`
*   **Routing File:** `routes/web.php`
*   **Role Logic:** `app/Models/User.php` and `app/Http/Middleware/RoleMiddleware.php`

**Demo Login Credentials:**
*   **Admin:** `admin@coreinventory.local` / `Admin@12345`
*   **Manager:** `manager@coreinventory.local` / `Manager@12345`
*   **Staff:** `staff@coreinventory.local` / `Staff@12345`