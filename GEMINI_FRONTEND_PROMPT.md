# Gemini Frontend UI Update Prompt - Backend Phase 1 (RBAC)

## Context
CoreInventory now has full Role-Based Access Control (RBAC) with three roles:
- admin   : Full access - CRUD + Settings + User Management
- manager : Create/edit/validate documents only. No delete, no settings
- staff   : Read-only. Can view but not create or validate

The auth()->user() object has these helpers in ALL Blade templates:
- auth()->user()->isAdmin()            -> true if role is admin
- auth()->user()->isManagerOrAbove()   -> true if role is admin OR manager
- auth()->user()->hasRole('staff')     -> true for exact role match

---

## REQUIRED BLADE CHANGES

### 1. resources/views/layouts/app.blade.php (Sidebar)
- Wrap the Settings nav link: @if(auth()->user()->isAdmin()) ... @endif
- Add new Users nav link (route: users.index) also inside @if(auth()->user()->isAdmin())
- Show the role badge next to the user name in the sidebar footer using CSS classes below

### 2. All Document Show Pages (products, receipts, deliveries, transfers, adjustments show.blade.php)
Replace old: @if(auth()->check() && in_array(strtolower(auth()->user()->role), ['manager', 'admin']))
With:
  - Edit buttons  -> @if(auth()->user()->isManagerOrAbove())
  - Delete forms  -> @if(auth()->user()->isAdmin())
  - Validate btns -> @if(auth()->user()->isManagerOrAbove())

### 3. All Index Pages (products, receipts, deliveries, transfers, adjustments index.blade.php)
Wrap the header "Create" button: @if(auth()->user()->isManagerOrAbove()) ... @endif

### 4. Dashboard (resources/views/dashboard/index.blade.php)
Add a role badge chip near the user greeting:
  admin   -> red pill with shield icon
  manager -> amber pill with briefcase icon
  staff   -> slate pill with user icon, label: Staff (Read-Only)

---

## CSS CLASSES (add to public/css/index.css global section)

.role-badge {
    padding: 5px 12px; border-radius: 20px; font-size: 0.72rem;
    font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase;
    display: inline-flex; align-items: center; gap: 5px;
}
.role-admin   { background: rgba(239,68,68,0.12); color: #dc2626; border: 1px solid rgba(239,68,68,0.25); }
.role-manager { background: rgba(245,158,11,0.12); color: #d97706; border: 1px solid rgba(245,158,11,0.25); }
.role-staff   { background: rgba(148,163,184,0.15); color: #475569; border: 1px solid rgba(148,163,184,0.25); }

Feather icons to use: shield (admin), briefcase (manager), user (staff)
ALWAYS use the helper methods - NEVER raw string comparisons like $user->role === 'admin' in Blade.
