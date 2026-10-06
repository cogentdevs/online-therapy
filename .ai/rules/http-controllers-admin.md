---
paths:
  - 'app/Providers/AppServiceProvider.php,app/Services/Authorization/AdminUserPermissionService.php,app/Http/Controllers/Admin/UserController.php'
---

# Http Controllers Admin

## Custom Admin permissions are a role-constrained allow-list
Managed Admin users use permission_mode=role for normal role inheritance or permission_mode=custom for a direct-permission allow-list. In custom mode, effective access requires the permission to exist both on the current role and in the user's direct permissions; admin.access remains system-controlled. Super Admin always bypasses this through Gate. Keep this enforcement centralized so route middleware, @can, and user->can agree.
