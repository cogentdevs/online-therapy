---
paths:
  - 'app/Models/AskQuestion.php,app/Http/Controllers/Admin/AskQuestionController.php,app/Http/Requests/Admin/UpdateAskQuestionResponseRequest.php,app/Services/AskQuestionResponseMailService.php,resources/views/admin/ask-questions/**,routes/web.php,config/admin_modules.php'
---

# Ask Questions

## Keep Ask Question workflow independent
Ask Question is a standalone module with ask-questions.view/edit permissions and only pending, answered, and closed statuses. Admins may change only admin_response and status; answered requires a response, and only a new or materially changed answered response emails the persisted snapshot address. Do not add delete actions or couple this workflow to Contacts, FAQs, or Advertising Requests.
