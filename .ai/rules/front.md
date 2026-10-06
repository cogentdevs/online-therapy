---
paths:
  - 'app/Http/Controllers/FrontController.php,app/Http/Controllers/Frontend/UserController.php,app/Services/PaidContentLoginIntentService.php,resources/js/front/front.js'
---

# Front

## Keep paid-content and subscription login intents distinct
Guest paid-content login stores a server-generated URL in front_paid_content_intended_url and reuses the shared frontend modal. Login redirect precedence is valid subscription product intent, then validated paid-content intent, then normal frontend intended/account behavior. Modal cancellation clears only the active intent type.
