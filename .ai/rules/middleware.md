---
paths:
  - 'app/Http/Middleware/**'
---

# Middleware

## Enforce Super Admin access server-side
Protect the admin area with the `super-admin` middleware alias. It must require an authenticated active user with the Spatie `super-admin` role; never replace this with Blade-only checks or a hard-coded numeric role ID.
