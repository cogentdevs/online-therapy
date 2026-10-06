---
paths:
  - app/Http/Controllers/Admin/RoleController.php
---

# Controllers Admin

## Protect system roles and enforce Admin access
Role Management uses Spatie web-guard roles only. super-admin, user, and consultant are protected names from config/admin_modules.php and cannot be edited/deleted. Every custom Admin role created or updated here must retain admin.access, and submitted permissions must be registered/synchronized.
