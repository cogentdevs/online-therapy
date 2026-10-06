import 'bootstrap';
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'datatables.net-bs5/css/dataTables.bootstrap5.css';
import 'datatables.net-responsive-bs5/css/responsive.bootstrap5.css';
import 'select2/dist/css/select2.css';
import 'sweetalert2/dist/sweetalert2.min.css';
import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import select2 from 'select2';
import Swal from 'sweetalert2';
import initializeBannerImageFields from './banner';
import initializeArticleEditor from './article';
import initializeCorePageSlug from './meta-tags';
import initializeMagazineLanguageOptions from './magazine';
import './newsletter-campaign';
import './subscription-trend';

window.$ = window.jQuery = $;

if (typeof $.isArray !== 'function') {
    $.isArray = Array.isArray;
}

if (typeof $.trim !== 'function') {
    $.trim = (value) => String(value ?? '').trim();
}

select2(window, $);
initializeBannerImageFields($);
initializeArticleEditor();
initializeCorePageSlug($);
initializeMagazineLanguageOptions($);

document.querySelectorAll('select[name="language"], select[name="default_language_id"]').forEach((field) => {
    field.required = false;
    field.removeAttribute('required');
    field.disabled = true;
    field.closest('.col-lg-4, .col-lg-5, .col-lg-6, .col-lg-8, .col-12, .mb-3, .row')?.classList.add('d-none');
    field.nextElementSibling?.classList.add('d-none');
});

const initializeFaqCategoryFields = () => document.querySelectorAll('[data-faq-category-form]').forEach((form) => {
    const category = form.querySelector('[data-faq-category-select]');
    const emptyMessage = form.querySelector('[data-faq-category-empty]');

    if (!category) {
        return;
    }

    const options = [...category.querySelectorAll('option[data-language]')];
    const filterCategories = () => {
        const selectedLanguage = 'en';
        let available = 0;

        options.forEach((option) => {
            const visible = selectedLanguage !== '' && option.dataset.language === selectedLanguage;
            option.hidden = !visible;
            option.disabled = false;
            available += visible ? 1 : 0;
        });

        category.disabled = false;
        emptyMessage?.classList.toggle('d-none', available > 0);
        $(category).trigger('change.select2');
    };

    filterCategories();
});

initializeFaqCategoryFields();

const initializeFreeUntilFields = () => document.querySelectorAll('form').forEach((form) => {
    const freeToggles = form.querySelectorAll('[data-free-access-toggle]');
    const field = form.querySelector('[data-free-until-field]');
    const input = field?.querySelector('[data-free-until-input]');

    if (!freeToggles.length || !field || !input) {
        return;
    }

    const sync = () => {
        const isFree = form.querySelector('[data-free-access-toggle]:checked')?.value === '1';
        field.classList.toggle('d-none', !isFree);
        input.disabled = !isFree;

        if (!isFree) {
            input.value = '';
        }
    };

    freeToggles.forEach((toggle) => toggle.addEventListener('change', sync));
    sync();
});

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-permission-action]');

    if (!button) {
        return;
    }

    const matrix = button.closest('[data-permission-matrix]');

    if (!matrix) {
        return;
    }

    const scope = button.dataset.permissionModule
        ? matrix.querySelector(`[data-permission-module="${button.dataset.permissionModule}"]`)
        : matrix;

    if (!scope) {
        return;
    }

    const isChecked = button.dataset.permissionAction === 'select';
    scope.querySelectorAll('input[type="checkbox"][name="permissions[]"]').forEach((checkbox) => {
        checkbox.checked = isChecked;
    });
});

const createRolePreviewElement = (tagName, className, textContent) => {
    const element = document.createElement(tagName);
    element.className = className;
    element.textContent = textContent;

    return element;
};

const initializeAdminUserPermissionEditors = () => document.querySelectorAll('[data-role-permission-editor]').forEach((editor) => {
    const form = editor.closest('form');
    const roleSelect = form?.querySelector('[data-role-permission-select]');
    const emptyState = editor.querySelector('[data-role-preview-empty]');
    const loadingState = editor.querySelector('[data-role-preview-loading]');
    const errorState = editor.querySelector('[data-role-preview-error]');
    const content = editor.querySelector('[data-role-preview-content]');
    const roleName = editor.querySelector('[data-role-preview-name]');
    const groupsContainer = editor.querySelector('[data-role-preview-groups]');
    const modeHelp = editor.querySelector('[data-role-preview-mode-help]');
    const customControls = editor.querySelector('[data-custom-permission-controls]');
    const initialRole = editor.dataset.initialRole;
    let selectedPermissions = new Set(JSON.parse(editor.dataset.selectedPermissions || '[]'));
    let loadedRole;
    let requestController;

    if (!roleSelect || !editor.dataset.rolePermissionsUrl) {
        return;
    }

    const setState = (state) => {
        emptyState?.classList.toggle('d-none', state !== 'empty');
        loadingState?.classList.toggle('d-none', state !== 'loading');
        errorState?.classList.toggle('d-none', state !== 'error');
        content?.classList.toggle('d-none', state !== 'content');
    };

    const mode = () => form.querySelector('[data-permission-mode]:checked')?.value || 'role';
    const applyMode = () => {
        const isCustom = mode() === 'custom';
        customControls?.classList.toggle('d-none', !isCustom);
        modeHelp.textContent = isCustom ? 'Select the allowed subset below.' : 'All listed permissions are inherited.';
        groupsContainer.querySelectorAll('input[name="permissions[]"]').forEach((checkbox) => {
            checkbox.disabled = !isCustom;
            checkbox.checked = isCustom ? selectedPermissions.has(checkbox.value) : true;
        });
        groupsContainer.querySelectorAll('[data-user-permission-module]').forEach((button) => {
            button.disabled = !isCustom;
        });
    };

    const render = (payload, roleId) => {
        roleName.textContent = payload.role.name;
        groupsContainer.replaceChildren();
        const availablePermissions = new Set(payload.groups.flatMap((group) => group.modules.flatMap((module) => module.actions.map((action) => action.name))));

        if (loadedRole !== roleId) {
            selectedPermissions = roleId === initialRole
                ? new Set([...selectedPermissions].filter((permission) => availablePermissions.has(permission)))
                : new Set(availablePermissions);
        }
        loadedRole = roleId;

        payload.groups.forEach((group) => {
            const groupColumn = createRolePreviewElement('div', 'col-12', '');
            groupColumn.append(createRolePreviewElement('h4', 'h6 text-uppercase text-muted mb-3', group.label));
            const moduleRow = createRolePreviewElement('div', 'row g-3', '');

            group.modules.forEach((module) => {
                const moduleColumn = createRolePreviewElement('div', 'col-xl-6', '');
                const moduleCard = createRolePreviewElement('div', 'border rounded p-3 h-100', '');
                const heading = createRolePreviewElement('div', 'd-flex justify-content-between align-items-center gap-2 mb-2', '');
                heading.append(createRolePreviewElement('strong', '', module.label));
                const moduleButton = createRolePreviewElement('button', 'btn btn-sm btn-outline-secondary', 'Select Module');
                moduleButton.type = 'button';
                moduleButton.dataset.userPermissionModule = module.name;
                heading.append(moduleButton);
                moduleCard.append(heading);
                const actions = createRolePreviewElement('div', 'd-flex flex-wrap gap-3', '');

                module.actions.forEach((action) => {
                    const wrapper = createRolePreviewElement('div', 'form-check', '');
                    const checkbox = createRolePreviewElement('input', 'form-check-input', '');
                    const checkboxId = `user_permission_${action.name.replaceAll('.', '_')}`;
                    checkbox.type = 'checkbox';
                    checkbox.name = 'permissions[]';
                    checkbox.value = action.name;
                    checkbox.id = checkboxId;
                    checkbox.dataset.permissionModule = module.name;
                    const label = createRolePreviewElement('label', 'form-check-label', action.label);
                    label.htmlFor = checkboxId;
                    wrapper.append(checkbox, label);
                    actions.append(wrapper);
                });

                moduleCard.append(actions);
                moduleColumn.append(moduleCard);
                moduleRow.append(moduleColumn);
            });

            groupColumn.append(moduleRow);
            groupsContainer.append(groupColumn);
        });

        setState('content');
        applyMode();
    };

    const load = async () => {
        const roleId = roleSelect.value;
        requestController?.abort();

        if (!roleId) {
            groupsContainer.replaceChildren();
            setState('empty');
            return;
        }

        requestController = new AbortController();
        setState('loading');

        try {
            const url = editor.dataset.rolePermissionsUrl.replace('__ROLE__', encodeURIComponent(roleId));
            const response = await fetch(url, { headers: { Accept: 'application/json' }, signal: requestController.signal });
            if (!response.ok) {
                throw new Error('Unable to load permissions for the selected role.');
            }
            render(await response.json(), roleId);
        } catch (error) {
            if (error.name !== 'AbortError') {
                errorState.textContent = error.message;
                setState('error');
            }
        }
    };

    $(roleSelect).off('change.adminUserPermissions').on('change.adminUserPermissions', load);
    $(form).find('[data-permission-mode]').off('change.adminUserPermissions').on('change.adminUserPermissions', applyMode);
    $(editor).on('change', 'input[name="permissions[]"]', function rememberPermission() {
        this.checked ? selectedPermissions.add(this.value) : selectedPermissions.delete(this.value);
    });
    $(editor).on('click', '[data-user-permission-action], [data-user-permission-module]', function updatePermissions() {
        if (mode() !== 'custom') {
            return;
        }
        const moduleName = this.dataset.userPermissionModule;
        const shouldCheck = this.dataset.userPermissionAction !== 'clear';
        const selector = moduleName ? `input[name="permissions[]"][data-permission-module="${moduleName}"]` : 'input[name="permissions[]"]';
        groupsContainer.querySelectorAll(selector).forEach((checkbox) => {
            checkbox.checked = shouldCheck;
            shouldCheck ? selectedPermissions.add(checkbox.value) : selectedPermissions.delete(checkbox.value);
        });
    });
    load();
});

$(document).on('click', '[data-repeatable-add]', function addRepeatableRow() {
    const repeatableList = $(this).closest('[data-repeatable-list]');
    const template = repeatableList.find('template[data-repeatable-template]').get(0);
    const rows = repeatableList.find('[data-repeatable-rows]');

    if (!template || rows.length === 0) {
        return;
    }

    const nextIndex = Number(repeatableList.attr('data-next-index') || rows.children().length);
    rows.append(template.innerHTML.replaceAll('__INDEX__', String(nextIndex)));
    repeatableList.attr('data-next-index', String(nextIndex + 1));
});

$(document).on('click', '[data-repeatable-remove]', function removeRepeatableRow() {
    const rows = $(this).closest('[data-repeatable-list]').find('[data-repeatable-rows]');

    if (rows.children('[data-repeatable-row]').length > 1) {
        $(this).closest('[data-repeatable-row]').remove();
    }
});

const sidebarOpenClass = 'admin-sidebar-open';
const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
const sidebarCloseButtons = document.querySelectorAll('[data-sidebar-close]');

const setSidebarState = (isOpen) => {
    document.body.classList.toggle(sidebarOpenClass, isOpen);
    sidebarToggle?.setAttribute('aria-expanded', String(isOpen));
};

sidebarToggle?.addEventListener('click', () => {
    setSidebarState(!document.body.classList.contains(sidebarOpenClass));
});

sidebarCloseButtons.forEach((button) => {
    button.addEventListener('click', () => setSidebarState(false));
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        setSidebarState(false);
    }
});

window.addEventListener('resize', () => {
    if (window.innerWidth >= 992) {
        setSidebarState(false);
    }
});

$('.select2').each(function initializeSelect2() {
    const select = $(this);

    if (!select.hasClass('select2-hidden-accessible')) {
        select.select2({ width: '100%' });
    }
});

document.querySelectorAll('[data-ad-placement-form]').forEach((form) => {
    const page = form.querySelector('[data-ad-page]');
    const place = form.querySelector('[data-ad-place]');
    const mapping = JSON.parse(form.dataset.pagePlacements || '{}');

    if (!page || !place) {
        return;
    }

    const populatePlaces = (preserveSelection = false) => {
        const selected = preserveSelection ? place.dataset.selected : '';
        $(place).empty().append(new Option('Select Place', '', true, false));
        Object.entries(mapping[page.value]?.places || {}).forEach(([value, label]) => place.add(new Option(label, value, false, value === selected)));
        $(place).prop('disabled', !page.value);
        $(place).trigger('change');
    };

    $(page).on('change', () => populatePlaces(false));
    populatePlaces(true);

    const requestPlacement = form.querySelector('[data-ad-request-placement]');
    const startDate = form.querySelector('[name="start_date"]');
    const expiryDate = form.querySelector('[name="expiry_date"]');

    const applyRequestPlacement = () => {
        const selected = requestPlacement?.selectedOptions[0];
        const linked = Boolean(form.dataset.adLinked !== undefined || selected?.value);

        if (selected?.value) {
            $(page).val(selected.dataset.page).trigger('change');
            $(place).val(selected.dataset.place).trigger('change');
            startDate.value = selected.dataset.from;
            expiryDate.value = selected.dataset.to;
        }

        $(page).prop('disabled', linked);
        $(place).prop('disabled', linked || !page.value);
        startDate.readOnly = linked;
        expiryDate.readOnly = linked;
    };

    $(requestPlacement).on('change', applyRequestPlacement);
    applyRequestPlacement();
});

document.querySelectorAll('[data-taza-articles-form]').forEach((form) => {
    const rows = form.querySelector('[data-taza-article-rows]');
    const template = form.querySelector('[data-taza-article-template]');
    const addButton = form.querySelector('[data-taza-add-article]');

    addButton?.addEventListener('click', () => {
        const index = Number(form.dataset.nextIndex || 0);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = template.innerHTML.replaceAll('__INDEX__', String(index)).trim();
        const row = wrapper.firstElementChild;

        if (!row) {
            return;
        }

        rows.append(row);
        form.dataset.nextIndex = String(index + 1);
        $(row).find('.select2').select2({ width: '100%' });
    });

    form.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-taza-remove-article]');

        if (!removeButton) {
            return;
        }

        const row = removeButton.closest('[data-taza-article-row]');
        $(row).find('.select2-hidden-accessible').select2('destroy');
        row?.remove();
    });
});

initializeAdminUserPermissionEditors();
initializeFreeUntilFields();

const initializeAboutForms = () => {
    const visibleFields = {
        1: ['title', 'description'],
        2: ['image'],
        3: ['title', 'description', 'image'],
        4: ['image', 'title', 'description'],
        5: ['title', 'description', 'title_2', 'description_2'],
        6: ['image', 'image_2'],
    };

    document.querySelectorAll('[data-about-form]').forEach((form) => {
        const condition = form.querySelector('[data-about-condition]');
        const updateFields = () => {
            const selectedFields = visibleFields[Number(condition?.value)] || [];

            form.querySelectorAll('[data-about-field]').forEach((container) => {
                const fieldName = container.dataset.aboutField;
                const input = container.querySelector(`[name="${fieldName}"]`);
                const isVisible = selectedFields.includes(fieldName);
                const isRequired = isVisible
                    && fieldName.startsWith('image')
                    && input?.dataset.hasExistingImage !== 'true';

                container.classList.toggle('d-none', !isVisible);
                input?.toggleAttribute('disabled', !isVisible);
                input?.toggleAttribute('required', isRequired);
                container.querySelector('[data-about-required-marker]')?.classList.toggle('d-none', !isRequired);
            });
        };

        $(condition).off('change.aboutForm').on('change.aboutForm', updateFields);
        updateFields();
    });
};

initializeAboutForms();

const initializeHomeSectionForms = () => {
    const visibleFields = {
        1: ['title', 'description'],
        2: ['image'],
        3: ['title', 'description', 'image'],
        4: ['image', 'title', 'description'],
        5: ['title', 'description', 'title_2', 'description_2'],
        6: ['image', 'image_2'],
    };

    document.querySelectorAll('[data-home-section-form]').forEach((form) => {
        const condition = form.querySelector('[data-home-section-condition]');
        const updateFields = () => {
            const selectedFields = visibleFields[Number(condition?.value)] || [];

            form.querySelectorAll('[data-home-section-field]').forEach((container) => {
                const fieldName = container.dataset.homeSectionField;
                const input = container.querySelector(`[name="${fieldName}"]`);
                const isVisible = selectedFields.includes(fieldName);
                const isRequired = isVisible
                    && fieldName.startsWith('image')
                    && input?.dataset.hasExistingImage !== 'true';

                container.classList.toggle('d-none', !isVisible);
                input?.toggleAttribute('disabled', !isVisible);
                input?.toggleAttribute('required', isRequired);
                container.querySelector('[data-home-section-required-marker]')?.classList.toggle('d-none', !isRequired);
            });
        };

        $(condition).off('change.homeSectionForm').on('change.homeSectionForm', updateFields);
        updateFields();
    });
};

initializeHomeSectionForms();

document.querySelectorAll('[data-admin-datatable]').forEach((table) => {
    if (!DataTable.isDataTable(table)) {
        const usesDisplaySerials = table.hasAttribute('data-admin-serials');

        const dataTable = new DataTable(table, {
            responsive: true,
            pageLength: 10,
            order: usesDisplaySerials ? [] : [[0, 'desc']],
            columnDefs: usesDisplaySerials ? [{ targets: 0, orderable: false, searchable: false }] : [],
            drawCallback: usesDisplaySerials ? function updateDisplaySerials() {
                const dataTable = this.api();
                const pageStart = dataTable.page.info().start;

                dataTable.column(0, { page: 'current', search: 'applied', order: 'applied' })
                    .nodes()
                    .each((cell, index) => {
                        cell.textContent = pageStart + index + 1;
                    });
            } : undefined,
            language: {
                search: 'Search:',
            },
        });

        const filters = document.querySelector(`[data-admin-datatable-filters="${table.id}"]`);
        filters?.querySelectorAll('[data-admin-column-filter]').forEach((filter) => {
            filter.addEventListener('change', () => {
                const value = filter.value;
                const search = value === '' ? '' : `^${DataTable.util.escapeRegex(value)}$`;
                dataTable.column(Number(filter.dataset.adminColumnFilter)).search(search, true, false).draw();
            });
        });
        filters?.querySelector('[data-admin-filters-reset]')?.addEventListener('click', () => {
            filters.querySelectorAll('[data-admin-column-filter]').forEach((filter) => {
                filter.value = '';
                dataTable.column(Number(filter.dataset.adminColumnFilter)).search('');
            });
            dataTable.draw();
        });
    }
});

const escapeAnalyticsValue = (value) => String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

document.querySelectorAll('[data-site-analytics-summary]').forEach((table) => {
    if (!DataTable.isDataTable(table)) {
        new DataTable(table, {
            ajax: table.dataset.sourceUrl,
            serverSide: true,
            processing: true,
            responsive: true,
            pageLength: 10,
            order: [[4, 'desc']],
            columns: [
                { data: 'serial', orderable: false, searchable: false },
                { data: 'name', render: (value) => escapeAnalyticsValue(value) },
                { data: 'visit_count' },
                { data: 'last_location', render: (value) => escapeAnalyticsValue(value) },
                { data: 'last_visited_at', render: (value) => escapeAnalyticsValue(value) },
                {
                    data: 'detail_url',
                    orderable: false,
                    searchable: false,
                    render: (value) => `<a class="btn btn-sm btn-outline-primary" href="${escapeAnalyticsValue(value)}"><i class="fa-solid fa-eye me-1"></i>View Detail</a>`,
                },
            ],
            language: { search: 'Search:' },
        });
    }
});

document.querySelectorAll('[data-site-analytics-detail]').forEach((table) => {
    if (!DataTable.isDataTable(table)) {
        new DataTable(table, {
            ajax: table.dataset.sourceUrl,
            serverSide: true,
            processing: true,
            responsive: true,
            pageLength: 10,
            order: [[9, 'desc']],
            columns: [
                { data: 'serial', orderable: false, searchable: false },
                ...['visitor', 'country', 'city', 'region', 'device', 'browser', 'os', 'duration', 'started_at']
                    .map((column) => ({ data: column, render: (value) => escapeAnalyticsValue(value) })),
            ],
            language: { search: 'Search:' },
        });
    }
});

document.querySelectorAll('[data-admin-contact-table]').forEach((table) => {
    if (!DataTable.isDataTable(table)) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        new DataTable(table, {
            ajax: table.dataset.sourceUrl,
            serverSide: true,
            processing: true,
            responsive: true,
            pageLength: 10,
            order: [[5, 'desc']],
            columns: [
                { data: 'serial', orderable: false, searchable: false },
                ...['name', 'email', 'phone', 'subject', 'received_at']
                    .map((column) => ({ data: column, render: (value) => escapeAnalyticsValue(value) })),
                {
                    data: 'is_read',
                    render: (value) => value
                        ? '<span class="badge text-bg-success">Read</span>'
                        : '<span class="badge text-bg-warning">Unread</span>',
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: (value, type, row) => {
                        const showUrl = escapeAnalyticsValue(row.show_url);
                        const viewButton = `<a class="btn btn-sm btn-outline-primary" href="${showUrl}" aria-label="View Contact"><i class="fa-solid fa-eye" aria-hidden="true"></i></a>`;

                        if (!row.delete_url) {
                            return `<div class="d-flex gap-2">${viewButton}</div>`;
                        }

                        const deleteUrl = escapeAnalyticsValue(row.delete_url);
                        const token = escapeAnalyticsValue(csrfToken);
                        const deleteForm = `<form method="POST" action="${deleteUrl}"><input type="hidden" name="_token" value="${token}"><input type="hidden" name="_method" value="DELETE"><button class="btn btn-sm btn-outline-danger" type="submit" data-delete-confirm data-delete-title="Delete this Contact message?" data-delete-text="This Contact message will be permanently deleted." aria-label="Delete Contact"><i class="fa-solid fa-trash" aria-hidden="true"></i></button></form>`;

                        return `<div class="d-flex gap-2">${viewButton}${deleteForm}</div>`;
                    },
                },
            ],
            language: { search: 'Search:' },
        });
    }
});

document.addEventListener('click', async (event) => {
    const deleteButton = event.target.closest('[data-delete-confirm]');

    if (!deleteButton) {
        return;
    }

    event.preventDefault();

    const form = deleteButton.form instanceof HTMLFormElement
        ? deleteButton.form
        : deleteButton.closest('form');

    if (!form) {
        return;
    }

    const result = await Swal.fire({
        title: deleteButton.dataset.deleteTitle || 'Delete this item?',
        text: deleteButton.dataset.deleteText || 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3f6ca1',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it',
    });

    if (result.isConfirmed) {
        form.submit();
    }
});

document.addEventListener('submit', async (event) => {
    const form = event.target.closest('[data-ownership-transfer-confirm]');

    if (!form || form.dataset.confirmed === 'true') {
        return;
    }

    event.preventDefault();

    const newOwner = form.querySelector('[name="new_owner_id"] option:checked')?.textContent?.trim() || 'Not selected';
    const contentTypes = [...form.querySelectorAll('[name="content_types[]"]:checked')]
        .map((input) => {
            const label = input.closest('.form-check')?.querySelector('strong')?.textContent?.trim() || input.value;

            return `${label}: ${input.dataset.transferCount || '0'}`;
        });
    const reassignsChildAdmins = form.querySelector('[name="content_types[]"][value="child_admins"]:checked') !== null;
    const deactivateSource = form.querySelector('[name="deactivate_source"]:checked')?.value === '1';
    const result = await Swal.fire({
        title: 'Transfer content ownership?',
        text: `Current Owner: ${form.dataset.currentOwner}. New Owner: ${newOwner}. Selected: ${contentTypes.join(', ') || 'None selected'}. Deactivate Current Owner: ${deactivateSource ? 'Yes' : 'No'}.${reassignsChildAdmins ? ' Direct Child Admins will move under the New Owner without changing their content ownership.' : ''}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3f6ca1',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, transfer ownership',
    });

    if (result.isConfirmed) {
        form.dataset.confirmed = 'true';
        form.submit();
    }
});

document.addEventListener('click', async (event) => {
    const publishButton = event.target.closest('[data-publish-confirm], [data-action-confirm]');

    if (!publishButton) {
        return;
    }

    event.preventDefault();

    const form = publishButton.closest('form');

    if (!form) {
        return;
    }

    const result = await Swal.fire({
        title: publishButton.dataset.actionTitle || publishButton.dataset.publishTitle || 'Publish this magazine?',
        text: publishButton.dataset.actionText || publishButton.dataset.publishText || 'Once published it will be eligible for frontend display.',
        icon: publishButton.dataset.actionIcon || 'question',
        showCancelButton: true,
        confirmButtonColor: '#3f6ca1',
        cancelButtonColor: '#6c757d',
        confirmButtonText: publishButton.dataset.actionConfirmText || 'Yes, publish it',
    });

    if (result.isConfirmed) {
        form.submit();
    }
});
