---
paths:
  - 'resources/views/admin/**'
---

# Admin

## Keep admin module views under admin
Place admin pages in resources/views/admin/. Give each major module its own folder there; keep dashboard.blade.php, login.blade.php, and change-password.blade.php directly under admin. Do not create alternative backend, dashboard/admin, admin/layouts, or components/admin structures unless explicitly requested.

## Build admin UI with Bootstrap 5
Build the separate admin interface with Bootstrap 5, Blade, HTML5, CSS, and focused JavaScript. Use a clean editorial style with light backgrounds, white cards, charcoal sidebar, deep-red accents, and readable forms, tables, and reports; do not introduce Tailwind, AdminLTE, Metronic, Vue, React, or an Alpine-heavy architecture unless explicitly requested.

## Use common admin UI plugins
Use the locally bundled admin standards: Select2 (`.select2`) for admin dropdowns, DataTables (`[data-admin-datatable]`) for suitable listing tables, SweetAlert2 (`[data-delete-confirm]`) before DELETE form submission, and local Font Awesome icons. Do not add CDNs, browser confirm(), or competing icon/plugin libraries without explicit direction.
