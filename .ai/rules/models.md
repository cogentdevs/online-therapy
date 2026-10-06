---
paths:
  - 'app/Models/**'
---

# Models

## Audit ownership is model-event driven
Admin-managed primary models use HasAuditOwnership to set created_by and updated_by from the authenticated web user. Keep actor FKs nullable with nullOnDelete; publishable Magazine/Article actions set published_by explicitly. Do not add these columns to pivot, storage, language, RBAC, or system tables.
