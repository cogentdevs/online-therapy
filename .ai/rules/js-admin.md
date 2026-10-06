---
paths:
  - 'resources/js/admin/**'
---

# Js Admin

## Reuse centralized admin plugin initialization
Keep Select2, DataTables, and SweetAlert2 delete-confirmation initialization centralized in the common admin Vite bundle. Module views should opt in through the established data attributes/classes instead of adding per-row or duplicate inline scripts.
