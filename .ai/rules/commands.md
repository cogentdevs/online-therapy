---
paths:
  - 'app/Console/Commands/ExpireUserSubscriptions.php,routes/console.php'
---

# Commands

## Expire subscriptions after inclusive end date
Persist expiry with subscriptions:expire only for rows still marked is_active=true and status=active whose non-null DATE end_date is before today. Set status=expired and is_active=false, preserve user_sub_types/history, and schedule daily with withoutOverlapping().
