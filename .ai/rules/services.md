---
paths:
  - 'app/Http/Controllers/FrontController.php,app/Services/PaidContentLoginIntentService.php'
---

# Services

## Preserve paid content through purchase
When an authenticated user lacks a paid-content entitlement, store the server-requested URL in front_subscription_return_url. Successful checkout redirects to the validated URL without consuming it; clear it only when that exact protected URL is successfully authorized. A wrong-module purchase therefore retries, is denied, and preserves the journey.
