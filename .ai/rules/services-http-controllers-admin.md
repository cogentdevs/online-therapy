---
paths:
  - 'app/Observers/**,app/Services/ActivityLogService.php,app/Http/Controllers/Admin/**'
---

# Services Http Controllers Admin

## Admin activity logs are immutable and Super Admin-only
Successful Admin Eloquent create/update/delete actions are logged centrally through AdminActivityObserver; compound Role/User Spatie changes are logged explicitly after their transactions. Never log secrets or failed/rolled-back actions. Activity Log listing/detail/exports are protected by the actual super-admin middleware and must not enter the configurable permission registry.
