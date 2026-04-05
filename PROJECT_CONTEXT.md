# CoreInventory – Project Context & Technical Plan

> **Last Updated:** 2026-04-04  
> **Stack:** Laravel 12 · PHP 8.2+ · MySQL · Vite · Blade Templates · Bootstrap 5.3

---

## 1. What Is This Project?
**CoreInventory** is a full-stack Inventory Management System (IMS) built with Laravel 12. It is an Odoo-inspired, warehouse-centric system designed to track physical stock through source documents (Receipts, Deliveries, Transfers, Adjustments). The stock balance is never stored directly on any product — it is always **computed on-the-fly** from the `stock_ledger` table (double-entry style).

---

## 2. Development History & Achievements

### ✅ Phase 1: Authentication & Role-Based Access Control (RBAC)
- Expanded the `users` table to support `admin` and `staff` roles alongside `manager`.
- Created robust `RoleMiddleware.php` to securely gate HTTP requests based on hierarchy.
- Admin-only dashboard for managing staff inside the IMS.

### ✅ Phase 2: Data Integrity & Hardening
- Implemented **SoftDeletes** across 7 core system tables.
- Added `created_at` and `updated_at` timestamps to all models.

### ✅ Phase 3: Database Seeding & Demo Data 
- Created an organized set of dependency-aware Database Seeders (`UserSeeder`, `WarehouseSeeder`, `ProductSeeder`, etc.).

### ✅ Phase 4: Production Readiness
- Fixed core validation issues during receipts/deliveries.
- Switched to MySQL compatibility (standard SQL `like` operations instead of Postgres-specific ones).
- Improved database exception handling so transactions don't result in silent 500 errors.

### ✅ Phase 6: Vendor & Purchase Order Management (Procurement)
- Full CRUD for Vendors with soft deletes.
- Purchase Order lifecycle (`Draft` → `Approved` → `Cancelled`).
- PO Approval automatically generates a Receipt draft and reflects accurate unit costs.

### ✅ Phase 7: Barcode & QR Code Integration
- Integrated `picqer/php-barcode-generator` and `bacon/bacon-qr-code`.
- Beautiful label generation view for single and bulk A4 printing with real-world formatting.

### ✅ Phase 8: Excel / CSV Exporting
- Integrated `maatwebsite/excel`.
- Build comprehensive export classes (`ProductsExport`, `LedgerExport`, `VendorsExport`).
- Added UX-friendly `.xlsx` and `.csv` download buttons strategically across data-heavy index views.

### ✅ Phase 9: Activity Audit Logging
- Implemented `spatie/laravel-activitylog` for automated accountability.
- Models (Product, Vendor, PO, Receipt, Delivery) now log creates, updates, and deletes with old vs new dirty values.
- Built a searchable, filterable Activity Log UI with color-coded event badges.

### ✅ Phase 10: Realistic Industrial Data Seeding
- Wrote `FreshDataSeeder` which wipes the database and inserts highly-accurate data.
- Includes Indian supply chain vendors, construction & electrical products, real POs, automated ledger calculation flows.

---

## 3. Project Roadmap / What To Build Next

These are the recommended immediate next steps:

### 📊 Phase 11: Dashboard Analytics & Charts (Up Next)
1. **Interactive Charts:** Integrate Chart.js to map out stock value, inbound/outbound monthly trends, and low-stock alerts visually.
2. **Recent Activity Feed:** Bring Spatie's activity log to the dashboard for an instant overview of operations.
3. **KPI Widgets:** Live dynamic calculations of Total Valuation, Pending Deliveries, Pending POs.

### 🔔 Phase 12: Low Stock Alert UI
Dedicated UI for products dropping below `reorder_level` with one-click PO draft generation per vendor.

### 🌙 Phase 13: Dark Mode Toggle
Implement a user-controlled CSS variable theme switch for dark mode functionality.

---

## 4. Key Quick Reference

*   **Database Setup:** `php artisan db:seed --class=FreshDataSeeder --force`
*   **Routing File:** `routes/web.php`
*   **Role Logic:** `app/Models/User.php` and `app/Http/Middleware/RoleMiddleware.php`

**Demo Login Credentials:**
*   **Admin:** `admin@coreinventory.local` / `Admin@12345`  _(Full Access)_
*   **Manager:** `manager@coreinventory.local` / `Manager@12345` _(Operations)_
*   **Staff:** `staff@coreinventory.local` / `Staff@12345` _(Read-only/Drafts)_