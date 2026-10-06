---
paths:
  - 'app/Http/Controllers/Admin/UserController.php,app/Http/Requests/Admin/*AdminUserRequest.php,app/Rules/Admin/EligibleAdminRole.php'
---

# Rules Admin

## Managed Admin users use one eligible custom role
Admin User Management must only manage users assigned to custom web-guard roles that have admin.access. Exclude and protect super-admin, user, and consultant roles using config/admin_modules.php; assign exactly one eligible role with syncRoles(), and prevent self-delete, self-deactivation, and unsafe self role changes.
