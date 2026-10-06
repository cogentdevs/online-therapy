---
paths:
  - routes/web.php
---

# Routes

## Keep admin routes in the protected admin group
Keep every admin-panel route inside the existing `prefix('admin')->name('admin.')->middleware(['auth', 'super-admin'])` group. Only pre-authentication routes such as admin login, login submit, and future password reset routes may remain outside it.

## Use explicit admin CRUD routes
For Admin CRUD modules, define individual GET/POST/DELETE routes with explicit paths and names. Do not use Route::resource() unless the user explicitly requests it.

## Use explicit admin CRUD routes
For Admin CRUD modules, define individual GET/POST/DELETE routes with explicit paths and names. Do not use Route::resource() unless the user explicitly requests it.

## Protect Admin routes with RBAC middleware
Admin routes belong inside the auth + admin-access group and each functional action must use its config/admin_modules.php permission (module.action). Keep explicit GET/POST/DELETE routes. Super Admin access comes from Gate::before; do not revert routes to super-admin-only middleware.
