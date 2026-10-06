---
paths:
  - 'app/Services/AskQuestionResponseMailService.php,app/Mail/AskQuestionResponseMailTo*.php'
---

# Services Mail

## Notify user and admin for answered Ask Questions
When an Ask Question becomes answered with a new or materially changed admin_response, synchronously attempt both the snapshot-user response email and the admin response notification. Keep attempts independent so either mail failure cannot roll back the persisted response or prevent the other recipient attempt.
