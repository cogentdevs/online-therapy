---
paths:
  - 'app/Http/Controllers/Frontend/AccountAskQuestionController.php,resources/views/frontend/user-account/question*.blade.php'
---

# User Account

## Keep My Questions owner-scoped and read-only
My Questions is a standalone Ask Question account area. Scope both list and detail queries by the authenticated user at the database level, expose only GET routes, display persisted submission snapshots and admin_response, and never trigger email or status changes from account pages.
