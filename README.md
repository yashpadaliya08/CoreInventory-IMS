# 📦 CoreInventory — Warehouse Management System (IMS)

CoreInventory is a full-stack, enterprise-grade Inventory Management System built on Laravel 12. It features an Odoo-inspired architecture where stock levels are completely dynamic — continuously calculated via an immutable double-entry stock ledger, ensuring rigorous mathematical tracking of parts, products, and movements.

---

## ✨ System Features

### Core Architecture
- **Dynamic Ledger Formulation:** Product inventory is not stored as static integers. It is dynamically summed up from source tracking documents (Receipts, Deliveries, Transfers, Adjustments).
- **Multi-Warehouse Support:** Track goods across numerous internal warehouses mapping, dispatch bays, and specific aisles.
- **Glassmorphism UI:** Modern, premium aesthetic dashboard equipped with responsive grid elements and FeatherIcons.

### Application Modules
- **Procurement & Vendors:** Robust supplier directory linked directly with Purchase Orders (Draft → Approved routing).
- **Goods Movement:** Validated physical movements for Inbound (Receipts), Outbound (Deliveries), and Internal (Transfers/Adjustments).
- **Barcode & QR Integration:** Built-in PDF/PNG label generation for warehouse shelf and product tagging.
- **Excel & CSV Exporting:** Instant one-click spreadsheet extraction for deep accounting pivots and audits.
- **Activity Audit Logging:** System-wide Spatie activity tracking that silently logs user alterations, old vs. new values, and deletion events.

---

## 🔐 Login Credentials

> **Login URL:** `http://127.0.0.1:8001/login`

The application uses **Role-Based Access Control (RBAC)** with three predefined roles.
Use any of the accounts below to access the system:

### 👤 Admin Account
| Field    | Value                          |
|----------|--------------------------------|
| **Email**    | `admin@coreinventory.local`    |
| **Password** | `Admin@12345`                  |
| **Role**     | Administrator                  |
| **Access**   | Full system control — user management, settings, all CRUD operations |

### 👤 Manager Account
| Field    | Value                             |
|----------|-----------------------------------|
| **Email**    | `manager@coreinventory.local`     |
| **Password** | `Manager@12345`                   |
| **Role**     | Manager                           |
| **Access**   | Operational supervision — approve POs, execute stock movements, no delete/settings |

### 👤 Staff Account
| Field    | Value                          |
|----------|--------------------------------|
| **Email**    | `staff@coreinventory.local`    |
| **Password** | `Staff@12345`                  |
| **Role**     | Staff                          |
| **Access**   | View-only + draft document creation |

#### Quick Reference Table
| Role | Email | Password |
|------|-------|----------|
| **Admin**   | `admin@coreinventory.local`   | `Admin@12345`   |
| **Manager** | `manager@coreinventory.local` | `Manager@12345` |
| **Staff**   | `staff@coreinventory.local`   | `Staff@12345`   |

---

## 🚀 Installation & Local Environment

1. **Clone & Install Dependencies**
```bash
composer install
npm install
npm run build
```

2. **Environment Configuration**
```bash
cp .env.example .env
php artisan key:generate
```
*Ensure your `.env` contains valid MySQL database credentials.*

3. **Migrate & Seed Live Data**
```bash
php artisan migrate:fresh
php artisan db:seed --class=FreshDataSeeder --force
```

4. **Serve the Application**
```bash
php artisan serve --port=8001
```
Application will be active at `http://127.0.0.1:8001`.
Login at: `http://127.0.0.1:8001/login`

---

## 🛠️ Stack & Technologies

- **Backend:** Laravel 12.x, PHP 8.2+
- **Database:** Standard SQL / MySQL compliant
- **Styling:** Bootstrap 5.3, custom Vanilla CSS (`.glass-panel` architecture)
- **Tooling:** Inter & Outfit Fonts, Feather Icons, Vite
- **Key Packages:** 
  - `spatie/laravel-activitylog`
  - `maatwebsite/excel`
  - `picqer/php-barcode-generator`

*Built dynamically as a next-generation approach to modern supply chain asset control.*