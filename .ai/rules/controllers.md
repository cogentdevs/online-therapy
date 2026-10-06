---
paths:
  - 'app/Http/Controllers/**'
---

# Controllers

## Store public uploads under public/images
For normal publicly accessible dynamic images, store files directly under public/images/{module-specific-folder}. Save only the portable relative path images/{module-specific-folder}/filename.ext in the database and render it with asset($path). Do not default to Storage::disk('public'), public/storage, or storage:link unless the user explicitly requests that for a module.
