---
paths:
  - 'app/Mail/**'
---

# Mail

## Send email directly unless explicitly asked to queue
User requires all email to send synchronously by default. Do not implement ShouldQueue, use Mail::queue, or add queued mail delivery unless the user explicitly requests it. Keep unrelated application queue configuration unchanged.
