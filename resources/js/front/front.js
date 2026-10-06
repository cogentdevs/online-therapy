import 'bootstrap';
import { Modal } from 'bootstrap';
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'datatables.net-bs5/css/dataTables.bootstrap5.css';
import 'datatables.net-responsive-bs5/css/responsive.bootstrap5.css';
import 'sweetalert2/dist/sweetalert2.min.css';
import DataTable from 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import Swal from 'sweetalert2';
import './pdf-viewer';
import './advertise';
import './ask-question';
import './newsletter';

document.querySelectorAll('[data-front-carousel]').forEach((carousel) => {
    carousel.addEventListener('slide.bs.carousel', () => {
        carousel.classList.add('is-sliding');
    });

    carousel.addEventListener('slid.bs.carousel', () => {
        carousel.classList.remove('is-sliding');
    });
});

document.querySelectorAll('[data-ranked-carousel]').forEach((carousel) => {
    const rankedContent = carousel.closest('[data-ranked-content]');
    const track = carousel.querySelector('[data-ranked-track]');
    const items = Array.from(carousel.querySelectorAll('[data-ranked-item]'));
    const mobileMedia = window.matchMedia('(max-width: 767.98px)');
    let activeIndex = 0;
    let autoplayTimer;

    const scrollToItem = (index, behavior = 'smooth') => {
        if (!track || items.length === 0) {
            return;
        }

        activeIndex = (index + items.length) % items.length;
        items[activeIndex].scrollIntoView({ behavior, block: 'nearest', inline: 'start' });
    };

    const stopAutoplay = () => window.clearInterval(autoplayTimer);
    const startAutoplay = () => {
        stopAutoplay();

        if (mobileMedia.matches && items.length > 1 && carousel.closest('.tab-pane')?.classList.contains('active')) {
            autoplayTimer = window.setInterval(() => scrollToItem(activeIndex + 1), 4500);
        }
    };

    carousel.querySelector('[data-ranked-previous]')?.addEventListener('click', () => {
        scrollToItem(activeIndex - 1);
        startAutoplay();
    });
    carousel.querySelector('[data-ranked-next]')?.addEventListener('click', () => {
        scrollToItem(activeIndex + 1);
        startAutoplay();
    });
    track?.addEventListener('pointerdown', stopAutoplay);
    track?.addEventListener('pointerup', startAutoplay);
    mobileMedia.addEventListener('change', startAutoplay);
    rankedContent?.querySelector('[data-ranked-tabs]')?.addEventListener('shown.bs.tab', () => {
        activeIndex = 0;
        scrollToItem(0, 'auto');
        startAutoplay();
    });
    startAutoplay();
});

document.querySelectorAll('[data-faq-search-root]').forEach((root) => {
    const input = root.querySelector('[data-faq-search-input]');
    const clearButton = root.querySelector('[data-faq-search-clear]');

    if (!input || !clearButton) {
        return;
    }

    const normalize = (value) => String(value ?? '')
        .normalize('NFKC')
        .toLocaleLowerCase()
        .trim()
        .replace(/\s+/gu, ' ');

    const filterActiveCategory = () => {
        const activePanel = root.querySelector('.faq-category-content .tab-pane.active');
        const query = normalize(input.value);
        const items = Array.from(activePanel?.querySelectorAll('[data-faq-search-item]') ?? []);
        let visibleItems = 0;

        items.forEach((item) => {
            const question = normalize(item.querySelector('.accordion-button')?.textContent);
            const isVisible = query === '' || question.includes(query);
            item.hidden = !isVisible;
            visibleItems += isVisible ? 1 : 0;
        });

        const emptyState = activePanel?.querySelector('[data-faq-search-empty]');
        if (emptyState) {
            emptyState.hidden = query === '' || visibleItems > 0;
        }

        clearButton.hidden = query === '';
    };

    input.addEventListener('input', filterActiveCategory);
    input.addEventListener('search', filterActiveCategory);
    clearButton.addEventListener('click', () => {
        input.value = '';
        filterActiveCategory();
        input.focus();
    });
    root.querySelector('[role="tablist"]')?.addEventListener('shown.bs.tab', () => {
        input.value = '';
        filterActiveCategory();
    });
    window.addEventListener('pageshow', filterActiveCategory);
    filterActiveCategory();
});

const mobileDropdowns = document.querySelectorAll('.front-nav-dropdown');

const closeMobileDropdown = (dropdown) => {
    dropdown.classList.remove('show');
    dropdown.querySelector('.dropdown-menu')?.classList.remove('show');
    dropdown.querySelector('[data-front-mobile-submenu-toggle]')?.setAttribute('aria-expanded', 'false');
};

mobileDropdowns.forEach((dropdown) => {
    const parentLink = dropdown.querySelector('[data-front-mobile-submenu]');
    const dropdownToggle = dropdown.querySelector('[data-front-mobile-submenu-toggle]');
    const dropdownMenu = dropdown.querySelector('.dropdown-menu');

    if (!parentLink || !dropdownToggle || !dropdownMenu) {
        return;
    }

    const toggleDropdown = (event) => {
        if (window.matchMedia('(min-width: 992px)').matches) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        const willOpen = !dropdownMenu.classList.contains('show');

        mobileDropdowns.forEach((otherDropdown) => {
            if (otherDropdown !== dropdown) {
                closeMobileDropdown(otherDropdown);
            }
        });

        dropdown.classList.toggle('show', willOpen);
        dropdownMenu.classList.toggle('show', willOpen);
        dropdownToggle.setAttribute('aria-expanded', String(willOpen));
    };

    parentLink.addEventListener('click', toggleDropdown);
    dropdownToggle.addEventListener('click', toggleDropdown);
});

document.addEventListener('click', (event) => {
    if (window.matchMedia('(min-width: 992px)').matches || event.target.closest('.front-nav-dropdown')) {
        return;
    }

    mobileDropdowns.forEach(closeMobileDropdown);
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        mobileDropdowns.forEach(closeMobileDropdown);
    }
});

document.querySelectorAll('[data-front-register-form]').forEach((form) => {
    const password = form.elements.password;
    const confirmation = form.elements.password_confirmation;
    const submit = form.querySelector('[type="submit"]');
    const update = () => {
        submit.disabled = !(
            Array.from(password.value).length >= 8
            && password.value === confirmation.value
            && window.frontRegisterCaptchaReady
            && window.grecaptcha?.getResponse()
        );
    };
    form.addEventListener('input', update);
    window.addEventListener('front-register-captcha', update);
    window.addEventListener('pageshow', update);
    form.addEventListener('submit', (event) => {
        update();
        if (submit.disabled) {
            event.preventDefault();
        } else {
            submit.disabled = true;
        }
    });
    update();
});

document.querySelectorAll('[data-front-password-toggle]').forEach((toggle) => {
    const input = document.getElementById(toggle.getAttribute('aria-controls'));

    if (!input) {
        return;
    }

    toggle.addEventListener('click', () => {
        const willShowPassword = input.type === 'password';
        input.type = willShowPassword ? 'text' : 'password';
        toggle.setAttribute('aria-pressed', String(willShowPassword));
        toggle.setAttribute('aria-label', willShowPassword ? toggle.dataset.hideLabel : toggle.dataset.showLabel);
        toggle.querySelector('i')?.classList.toggle('fa-eye', !willShowPassword);
        toggle.querySelector('i')?.classList.toggle('fa-eye-slash', willShowPassword);
    });
});

document.querySelectorAll('[data-front-reset-form]').forEach((form) => {
    const password = form.elements.password;
    const confirmation = form.elements.password_confirmation;
    const submit = form.querySelector('[type="submit"]');
    const update = () => {
        submit.disabled = !(Array.from(password.value).length >= 8 && password.value === confirmation.value);
    };

    form.addEventListener('input', update);
    window.addEventListener('pageshow', update);
    update();
});

document.querySelectorAll('[data-front-change-password-form]').forEach((form) => {
    const currentPassword = form.elements.current_password;
    const password = form.elements.password;
    const confirmation = form.elements.password_confirmation;
    const submit = form.querySelector('[type="submit"]');
    const update = () => {
        submit.disabled = !(
            currentPassword.value.length > 0
            && Array.from(password.value).length >= 8
            && password.value === confirmation.value
        );
    };

    form.addEventListener('input', update);
    window.addEventListener('pageshow', update);
    update();
});

document.querySelectorAll('[data-front-profile-form]').forEach((form) => {
    const input = form.querySelector('[data-front-profile-input]');
    const preview = form.querySelector('[data-front-profile-preview]');
    const remove = form.querySelector('[data-front-profile-remove]');
    const removeValue = form.querySelector('[data-front-profile-remove-value]');
    let previewUrl;

    input?.addEventListener('change', () => {
        const file = input.files?.[0];
        if (!file) {
            return;
        }
        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
        }
        previewUrl = URL.createObjectURL(file);
        preview.src = previewUrl;
        removeValue.value = '0';
    });

    remove?.addEventListener('click', async () => {
        const result = await Swal.fire({
            title: 'Remove profile picture?',
            text: 'Are you sure you want to remove your current profile picture?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3F6CA1',
            cancelButtonColor: '#737078',
            confirmButtonText: 'Yes, remove it',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
        });

        if (!result.isConfirmed) {
            return;
        }

        input.value = '';
        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
            previewUrl = undefined;
        }
        preview.src = preview.dataset.defaultSrc;
        removeValue.value = '1';
    });
});

const subscriptionLoginModalElement = document.querySelector('[data-subscription-login-modal]');

if (subscriptionLoginModalElement) {
    const subscriptionLoginModal = Modal.getOrCreateInstance(subscriptionLoginModalElement);
    const loginForm = subscriptionLoginModalElement.querySelector('[data-subscription-login-form]');
    const loginError = subscriptionLoginModalElement.querySelector('[data-subscription-login-error]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    let isRedirectingAfterLogin = false;
    let activeIntentType = subscriptionLoginModalElement.dataset.autoOpen === 'true' ? 'paid-content' : null;

    const clearLoginErrors = () => {
        loginError.textContent = '';
        loginError.classList.add('d-none');
        subscriptionLoginModalElement.querySelectorAll('[data-error-for]').forEach((element) => {
            element.textContent = '';
        });
    };

    const showLoginErrors = (errors = {}) => {
        clearLoginErrors();
        const messages = Object.values(errors).flat().filter(Boolean);

        Object.entries(errors).forEach(([field, fieldMessages]) => {
            const fieldError = subscriptionLoginModalElement.querySelector(`[data-error-for="${field}"]`);
            if (fieldError) {
                fieldError.textContent = Array.isArray(fieldMessages) ? fieldMessages[0] : fieldMessages;
            }
        });

        if (messages.length > 0) {
            loginError.textContent = messages[0];
            loginError.classList.remove('d-none');
        }
    };

    const clearIntent = (intentType) => {
        const clearUrl = intentType === 'subscription'
            ? subscriptionLoginModalElement.dataset.subscriptionClearIntentUrl
            : subscriptionLoginModalElement.dataset.paidContentClearIntentUrl;

        if (!clearUrl) {
            return Promise.resolve();
        }

        return fetch(clearUrl, {
        method: 'DELETE',
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        keepalive: true,
        });
    };

    document.querySelectorAll('[data-subscription-guest-cta]').forEach((button) => {
        button.addEventListener('click', async () => {
            button.disabled = true;
            activeIntentType = 'subscription';
            clearLoginErrors();

            try {
                const response = await fetch(button.dataset.intentUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                if (!response.ok) {
                    throw new Error('Unable to store subscription intent.');
                }

                loginForm.reset();
                subscriptionLoginModal.show();
                window.setTimeout(() => loginForm.elements.email.focus(), 200);
            } catch {
                await Swal.fire({
                    icon: 'error',
                    text: 'The subscription selection could not be saved. Please try again.',
                    confirmButtonColor: '#3F6CA1',
                    confirmButtonText: 'OK',
                });
            } finally {
                button.disabled = false;
            }
        });
    });

    if (activeIntentType === 'paid-content') {
        subscriptionLoginModal.show();
        window.setTimeout(() => loginForm.elements.email.focus(), 200);
    }

    loginForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearLoginErrors();
        const submit = loginForm.querySelector('[data-subscription-login-submit]');
        submit.disabled = true;

        try {
            const response = await fetch(loginForm.action, {
                method: 'POST',
                headers: { Accept: 'application/json' },
                body: new FormData(loginForm),
            });
            const payload = await response.json();

            if (!response.ok) {
                showLoginErrors(payload.errors || { email: [payload.message || 'Unable to log in.'] });
                loginForm.elements.password.value = '';
                return;
            }

            if (typeof payload.redirect !== 'string') {
                throw new Error('Missing safe login redirect.');
            }

            isRedirectingAfterLogin = true;
            window.location.assign(payload.redirect);
        } catch (error) {
            showLoginErrors({ email: ['Login could not be completed. Please try again.'] });
        } finally {
            submit.disabled = false;
        }
    });

    subscriptionLoginModalElement.addEventListener('hidden.bs.modal', () => {
        if (!isRedirectingAfterLogin && activeIntentType) {
            clearIntent(activeIntentType).catch(() => {});
        }
    });

    subscriptionLoginModalElement.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            if (activeIntentType) {
                clearIntent(activeIntentType).catch(() => {});
            }
        });
    });
}

document.querySelectorAll('[data-article-bookmark]').forEach((button) => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const label = button.querySelector('[data-article-bookmark-label]');
    const icon = button.querySelector('i');

    const applyState = (bookmarked) => {
        button.dataset.bookmarked = String(bookmarked);
        button.setAttribute('aria-pressed', String(bookmarked));
        button.classList.toggle('is-bookmarked', bookmarked);
        icon?.classList.toggle('fa-solid', bookmarked);
        icon?.classList.toggle('fa-regular', !bookmarked);

        if (label) {
            label.textContent = bookmarked ? 'Remove Bookmark' : 'Bookmark';
        }
    };

    button.addEventListener('click', async () => {
        if (button.disabled) {
            return;
        }

        const isBookmarked = button.dataset.bookmarked === 'true';
        const confirmation = await Swal.fire({
            title: isBookmarked ? 'بک مارک سے ہٹائیں؟' : 'مضمون بک مارک کریں؟',
            text: isBookmarked
                ? 'کیا آپ واقعی اس مضمون کو اپنے بک مارکس سے ہٹانا چاہتے ہیں؟'
                : 'کیا آپ اس مضمون کو اپنے بک مارکس میں محفوظ کرنا چاہتے ہیں؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3F6CA1',
            cancelButtonColor: '#737078',
            confirmButtonText: isBookmarked ? 'جی ہاں، ہٹا دیں' : 'جی ہاں، محفوظ کریں',
            cancelButtonText: 'منسوخ کریں',
            reverseButtons: true,
        });

        if (!confirmation.isConfirmed) {
            return;
        }

        button.disabled = true;

        try {
            const response = await fetch(isBookmarked ? button.dataset.destroyUrl : button.dataset.storeUrl, {
                method: isBookmarked ? 'DELETE' : 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });
            const payload = await response.json();

            if (!response.ok || payload.success !== true || typeof payload.bookmarked !== 'boolean') {
                throw new Error(payload.message || 'Bookmark request failed.');
            }

            applyState(payload.bookmarked);
            await Swal.fire({
                icon: 'success',
                text: payload.message,
                confirmButtonColor: '#3F6CA1',
                confirmButtonText: 'ٹھیک ہے',
            });
        } catch {
            await Swal.fire({
                icon: 'error',
                text: 'بک مارک اپ ڈیٹ نہیں ہو سکا۔ براہ کرم دوبارہ کوشش کریں۔',
                confirmButtonColor: '#3F6CA1',
                confirmButtonText: 'ٹھیک ہے',
            });
        } finally {
            button.disabled = false;
        }
    });
});

document.querySelectorAll('[data-magazine-bookmark-controls]').forEach((controls) => {
    const viewer = controls.closest('[data-pdf-viewer]');
    const saveButton = controls.querySelector('[data-magazine-bookmark-save]');
    const removeButton = controls.querySelector('[data-magazine-bookmark-remove]');
    const saveLabel = controls.querySelector('[data-magazine-bookmark-save-label]');
    const saveIcon = saveButton?.querySelector('i');
    const savedPage = controls.querySelector('[data-magazine-bookmark-page]');
    const savedPageValue = savedPage?.querySelector('b');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    const setBusy = (busy) => {
        saveButton.disabled = busy || !viewer?.dataset.currentPage;
        removeButton.disabled = busy;
    };

    const applyState = (bookmarked, pdfPage = null) => {
        controls.dataset.bookmarked = String(bookmarked);
        saveLabel.textContent = bookmarked ? 'موجودہ صفحہ محفوظ کریں' : 'بک مارک کریں';
        saveIcon?.classList.toggle('fa-solid', bookmarked);
        saveIcon?.classList.toggle('fa-regular', !bookmarked);
        removeButton.hidden = !bookmarked;
        savedPage.hidden = !bookmarked || !pdfPage;

        if (savedPageValue && pdfPage) {
            savedPageValue.textContent = String(pdfPage);
        }
    };

    viewer?.addEventListener('front-pdf-page-change', () => setBusy(false));
    setBusy(false);

    saveButton?.addEventListener('click', async () => {
        const currentPage = Number.parseInt(viewer?.dataset.currentPage || '', 10);
        if (!Number.isInteger(currentPage) || currentPage < 1 || saveButton.disabled) {
            return;
        }

        const isBookmarked = controls.dataset.bookmarked === 'true';
        const confirmation = await Swal.fire({
            title: isBookmarked ? 'محفوظ شدہ صفحہ تبدیل کریں؟' : 'شمارہ بک مارک کریں؟',
            text: `صفحہ ${currentPage} کو محفوظ کیا جائے گا۔`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3F6CA1',
            cancelButtonColor: '#737078',
            confirmButtonText: 'جی ہاں، محفوظ کریں',
            cancelButtonText: 'منسوخ کریں',
            reverseButtons: true,
        });

        if (!confirmation.isConfirmed) {
            return;
        }

        const confirmedPage = Number.parseInt(viewer?.dataset.currentPage || '', 10);
        if (!Number.isInteger(confirmedPage) || confirmedPage < 1) {
            return;
        }

        setBusy(true);

        try {
            const response = await fetch(controls.dataset.storeUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ pdf_page: confirmedPage }),
            });
            const payload = await response.json();

            if (!response.ok || payload.success !== true || payload.bookmarked !== true) {
                throw new Error(payload.message || 'Magazine bookmark request failed.');
            }

            applyState(true, payload.pdf_page);
            await Swal.fire({
                icon: 'success',
                text: payload.message,
                confirmButtonColor: '#3F6CA1',
                confirmButtonText: 'ٹھیک ہے',
            });
        } catch {
            await Swal.fire({
                icon: 'error',
                text: 'شمارے کا بک مارک محفوظ نہیں ہو سکا۔ براہ کرم دوبارہ کوشش کریں۔',
                confirmButtonColor: '#3F6CA1',
                confirmButtonText: 'ٹھیک ہے',
            });
        } finally {
            setBusy(false);
        }
    });

    removeButton?.addEventListener('click', async () => {
        if (removeButton.disabled) {
            return;
        }

        const confirmation = await Swal.fire({
            title: 'بک مارک سے ہٹائیں؟',
            text: 'کیا آپ واقعی اس شمارے کا بک مارک ہٹانا چاہتے ہیں؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3F6CA1',
            cancelButtonColor: '#737078',
            confirmButtonText: 'جی ہاں، ہٹا دیں',
            cancelButtonText: 'منسوخ کریں',
            reverseButtons: true,
        });

        if (!confirmation.isConfirmed) {
            return;
        }

        setBusy(true);

        try {
            const response = await fetch(controls.dataset.destroyUrl, {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });
            const payload = await response.json();

            if (!response.ok || payload.success !== true || payload.bookmarked !== false) {
                throw new Error(payload.message || 'Magazine bookmark removal failed.');
            }

            applyState(false);
            await Swal.fire({
                icon: 'success',
                text: payload.message,
                confirmButtonColor: '#3F6CA1',
                confirmButtonText: 'ٹھیک ہے',
            });
        } catch {
            await Swal.fire({
                icon: 'error',
                text: 'شمارے کا بک مارک ہٹایا نہیں جا سکا۔ براہ کرم دوبارہ کوشش کریں۔',
                confirmButtonColor: '#3F6CA1',
                confirmButtonText: 'ٹھیک ہے',
            });
        } finally {
            setBusy(false);
        }
    });
});

const escapeTableValue = (value) => String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

const accountDataTableColumns = {
    bookmarks: [
        { data: 'module', render: (value) => `<span class="front-account-bookmark-module">${escapeTableValue(value)}</span>` },
        { data: 'title', render: (value) => `<span class="front-account-datatable-title" title="${escapeTableValue(value)}">${escapeTableValue(value)}</span>` },
        { data: 'bookmarked_at', render: escapeTableValue },
        {
            data: 'url',
            orderable: false,
            searchable: false,
            render: (url) => url
                ? `<a class="front-account-bookmark-view" href="${escapeTableValue(url)}">View</a>`
                : '<span class="front-account-bookmark-unavailable">Unavailable</span>',
        },
        {
            data: 'destroy_url',
            orderable: false,
            searchable: false,
            render: (url) => `<button class="front-account-bookmark-remove" type="button" data-account-bookmark-remove data-destroy-url="${escapeTableValue(url)}"><i class="fa-regular fa-trash-can" aria-hidden="true"></i> Remove</button>`,
        },
    ],
    activities: [
        { data: 'module', render: (value) => `<span class="front-account-activity-module">${escapeTableValue(value)}</span>` },
        { data: 'title', render: (value) => `<span class="front-account-datatable-title" title="${escapeTableValue(value)}">${escapeTableValue(value)}</span>` },
        {
            data: 'last_visited_at',
            render: (value) => {
                const displayValue = String(value ?? '');
                const timezoneSuffix = 'PKT (UTC+5)';
                const dateTime = displayValue.endsWith(` ${timezoneSuffix}`)
                    ? displayValue.slice(0, -(timezoneSuffix.length + 1))
                    : displayValue;

                return `<span class="front-account-activity-time" dir="ltr"><span>${escapeTableValue(dateTime)}</span><span>${timezoneSuffix}</span></span>`;
            },
        },
        {
            data: 'url',
            orderable: false,
            searchable: false,
            render: (url) => url
                ? `<a class="front-account-activity-view" href="${escapeTableValue(url)}">View</a>`
                : '<span class="front-account-activity-unavailable">Unavailable</span>',
        },
    ],
    subscriptions: [
        {
            data: null,
            render: (value, type, row) => `<strong>${escapeTableValue(row.product_name)}</strong><small>${escapeTableValue(row.product_type)}</small>`,
        },
        {
            data: 'modules',
            orderable: false,
            render: (modules) => modules.length
                ? `<div class="user-subscription-modules">${modules.map((module) => `<span><i class="${escapeTableValue(module.icon)}" aria-hidden="true"></i>${escapeTableValue(module.label)}</span>`).join('')}</div>`
                : '<div class="user-subscription-modules"><span class="is-empty">No modules listed</span></div>',
        },
        {
            data: null,
            render: (value, type, row) => `<span dir="ltr">${escapeTableValue(row.currency)} ${escapeTableValue(row.amount)}</span>`,
        },
        {
            data: null,
            render: (value, type, row) => `<small>Submitted: <span>${escapeTableValue(row.payment_submitted_at)}</span></small><small>Starts: <span>${escapeTableValue(row.start_date)}</span></small><small>Ends: <span>${escapeTableValue(row.end_date)}</span></small>${row.reviewed_at !== '—' ? `<small>Reviewed: <span>${escapeTableValue(row.reviewed_at)}</span></small>` : ''}`,
        },
        { data: null, render: (value, type, row) => `<span dir="ltr">${escapeTableValue(row.payment_method)}</span><small>${escapeTableValue(row.payment_status_label)}</small>${row.transaction_id ? `<small dir="ltr">Ref: ${escapeTableValue(row.transaction_id)}</small>` : ''}` },
        { data: null, render: (value, type, row) => `<span class="user-subscription-status is-${escapeTableValue(row.status)}">${escapeTableValue(row.status_label)}</span>${row.rejection_reason ? `<small class="d-block text-danger">${escapeTableValue(row.rejection_reason)}</small>` : ''}` },
        {
            data: null,
            orderable: false,
            searchable: false,
            render: (value, type, row) => {
                const invoice = row.invoice_view_url && row.invoice_download_url
                    ? `<small dir="ltr">Invoice: ${escapeTableValue(row.invoice_no)}</small><small dir="ltr">Order: ${escapeTableValue(row.order_no)}</small><div class="d-inline-flex flex-wrap gap-1" dir="ltr"><a class="btn btn-sm btn-outline-primary" href="${escapeTableValue(row.invoice_view_url)}" target="_blank" rel="noopener" title="View Invoice"><i class="fa-regular fa-eye" aria-hidden="true"></i><span class="visually-hidden">View Invoice</span></a><a class="btn btn-sm btn-outline-secondary" href="${escapeTableValue(row.invoice_download_url)}" title="Download PDF"><i class="fa-solid fa-download" aria-hidden="true"></i><span class="visually-hidden">Download PDF</span></a></div>`
                    : '<span aria-label="Invoice pending">—</span>';
                const resubmit = row.resubmit_url
                    ? `<a class="btn btn-sm btn-outline-primary" href="${escapeTableValue(row.resubmit_url)}">Resubmit Payment</a>`
                    : '';
                const videos = row.videos_url
                    ? `<a class="btn btn-sm btn-outline-primary" href="${escapeTableValue(row.videos_url)}">View Videos</a>`
                    : '';

                return `${invoice}${resubmit}${videos}`;
            },
        },
    ],
};

document.querySelectorAll('[data-front-account-datatable]').forEach((table) => {
    const kind = table.dataset.frontAccountDatatable;
    const columns = accountDataTableColumns[kind];

    if (!columns || !table.dataset.sourceUrl || DataTable.isDataTable(table)) {
        return;
    }

    table._frontDataTable = new DataTable(table, {
        ajax: table.dataset.sourceUrl,
        columns,
        order: [[kind === 'bookmarks' ? 2 : kind === 'activities' ? 2 : 3, 'desc']],
        pageLength: 10,
        processing: true,
        responsive: true,
        searchDelay: 300,
        serverSide: true,
        language: {
            emptyTable: 'کوئی ریکارڈ موجود نہیں ہے۔',
            info: '_TOTAL_ میں سے _START_ تا _END_ ریکارڈز',
            infoEmpty: 'کوئی ریکارڈ موجود نہیں ہے۔',
            infoFiltered: '(_MAX_ ریکارڈز میں تلاش)',
            lengthMenu: 'فی صفحہ _MENU_ ریکارڈز',
            loadingRecords: 'ریکارڈز لوڈ ہو رہے ہیں…',
            processing: 'ریکارڈز لوڈ ہو رہے ہیں…',
            search: 'تلاش:',
            zeroRecords: 'تلاش کے مطابق کوئی ریکارڈ نہیں ملا۔',
        },
    });
});

const accountListingDataTableLanguage = {
    emptyTable: 'کوئی ریکارڈ موجود نہیں ہے۔',
    info: '_TOTAL_ میں سے _START_ تا _END_ ریکارڈز',
    infoEmpty: 'کوئی ریکارڈ موجود نہیں ہے۔',
    infoFiltered: '(_MAX_ ریکارڈز میں تلاش)',
    lengthMenu: 'فی صفحہ _MENU_ ریکارڈز',
    loadingRecords: 'ریکارڈز لوڈ ہو رہے ہیں…',
    processing: 'ریکارڈز لوڈ ہو رہے ہیں…',
    search: 'تلاش:',
    zeroRecords: 'تلاش کے مطابق کوئی ریکارڈ نہیں ملا۔',
};

document.querySelectorAll('[data-front-account-listing-datatable]').forEach(async (table) => {
    if (DataTable.isDataTable(table)) {
        return;
    }

    const totalPages = Number.parseInt(table.dataset.totalPages ?? '1', 10);
    const currentPage = Number.parseInt(table.dataset.currentPage ?? '1', 10);
    const tableKind = table.dataset.frontAccountListingDatatable;

    if (totalPages > 1) {
        const pageRequests = [];

        for (let page = 1; page <= totalPages; page += 1) {
            if (page === currentPage) {
                continue;
            }

            const pageUrl = new URL(window.location.href);
            pageUrl.searchParams.set('page', page);
            pageRequests.push(fetch(pageUrl, { headers: { Accept: 'text/html' } }).then((response) => {
                if (!response.ok) {
                    throw new Error('Account listing page could not be loaded.');
                }

                return response.text();
            }));
        }

        try {
            const pages = await Promise.all(pageRequests);

            pages.forEach((html) => {
                const documentFragment = new DOMParser().parseFromString(html, 'text/html');
                documentFragment
                    .querySelectorAll(`[data-front-account-listing-datatable="${tableKind}"] tbody tr`)
                    .forEach((row) => table.tBodies[0].append(row));
            });
        } catch {
            // Keep the server-rendered page usable if an additional page cannot be loaded.
        }
    }

    table.closest('.front-account-card')?.querySelector('.user-subscriptions-pagination')?.remove();

    new DataTable(table, {
        columnDefs: [{ orderable: false, searchable: false, targets: -1 }],
        language: accountListingDataTableLanguage,
        order: [[3, 'desc']],
        pageLength: 10,
        responsive: true,
    });
});

document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-account-bookmark-remove]');

    if (!button) {
        return;
    }

        if (button.disabled) {
            return;
        }

        const confirmation = await Swal.fire({
            title: 'بک مارک ہٹائیں؟',
            text: 'کیا آپ واقعی اس بک مارک کو ہٹانا چاہتے ہیں؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3F6CA1',
            cancelButtonColor: '#737078',
            confirmButtonText: 'جی ہاں، ہٹا دیں',
            cancelButtonText: 'منسوخ کریں',
            reverseButtons: true,
        });

        if (!confirmation.isConfirmed) {
            return;
        }

        button.disabled = true;

        try {
            const response = await fetch(button.dataset.destroyUrl, {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                },
            });
            const payload = await response.json();

            if (!response.ok || payload.success !== true || payload.removed !== true) {
                throw new Error(payload.message || 'Bookmark removal failed.');
            }

            const table = button.closest('[data-front-account-datatable]');
            table?._frontDataTable?.ajax.reload(null, false);

            await Swal.fire({
                icon: 'success',
                text: payload.message,
                confirmButtonColor: '#3F6CA1',
                confirmButtonText: 'ٹھیک ہے',
            });
        } catch {
            await Swal.fire({
                icon: 'error',
                text: 'بک مارک ہٹایا نہیں جا سکا۔ براہ کرم دوبارہ کوشش کریں۔',
                confirmButtonColor: '#3F6CA1',
                confirmButtonText: 'ٹھیک ہے',
            });
        } finally {
            button.disabled = false;
        }
});

document.querySelectorAll('[data-subscription-checkout]').forEach((checkout) => {
    const paymentOptions = Array.from(checkout.querySelectorAll('.subscription-payment-option'));
    const paymentInputs = paymentOptions.map((option) => option.querySelector('input[name="payment_account_id"]'));
    const paymentSlip = checkout.querySelector('input[name="payment_slip"]');
    const checkoutAction = checkout.querySelector('[data-checkout-action]');

    const updatePaymentSelection = () => {
        const selectedInput = paymentInputs.find((input) => input?.checked);

        paymentOptions.forEach((option) => {
            option.classList.toggle('is-selected', option.querySelector('input[name="payment_account_id"]')?.checked === true);
        });
        checkoutAction.disabled = !selectedInput || !paymentSlip?.files?.length;
    };

    paymentInputs.forEach((input) => input?.addEventListener('change', updatePaymentSelection));
    paymentSlip?.addEventListener('change', updatePaymentSelection);
    window.addEventListener('pageshow', updatePaymentSelection);
    updatePaymentSelection();
});

const siteVisitToken = document.querySelector('meta[name="site-visit-token"]')?.content;
const siteVisitHeartbeatUrl = document.querySelector('meta[name="site-visit-heartbeat-url"]')?.content;

if (siteVisitToken && siteVisitHeartbeatUrl) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    let unsentActiveSeconds = 0;
    let lastTickAt = Date.now();

    const isActive = () => document.visibilityState === 'visible' && document.hasFocus();

    const collectActiveTime = () => {
        const now = Date.now();
        const elapsedSeconds = Math.min(2, Math.max(0, Math.floor((now - lastTickAt) / 1000)));

        if (isActive()) {
            unsentActiveSeconds += elapsedSeconds;
        }

        lastTickAt = now;
    };

    const sendHeartbeat = (ended = false) => {
        collectActiveTime();

        if (unsentActiveSeconds < 1) {
            return;
        }

        const activeSeconds = Math.min(30, unsentActiveSeconds);
        unsentActiveSeconds -= activeSeconds;
        const data = new FormData();
        data.append('_token', csrfToken);
        data.append('token', siteVisitToken);
        data.append('active_seconds', String(activeSeconds));
        data.append('ended', ended ? '1' : '0');

        if (ended && navigator.sendBeacon) {
            navigator.sendBeacon(siteVisitHeartbeatUrl, data);
            return;
        }

        fetch(siteVisitHeartbeatUrl, {
            method: 'POST',
            body: data,
            credentials: 'same-origin',
            keepalive: ended,
            headers: { Accept: 'application/json' },
        }).catch(() => {
            unsentActiveSeconds += activeSeconds;
        });
    };

    window.setInterval(collectActiveTime, 1000);
    window.setInterval(() => sendHeartbeat(), 20000);
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') {
            sendHeartbeat();
        } else {
            lastTickAt = Date.now();
        }
    });
    window.addEventListener('pagehide', () => sendHeartbeat(true));
}
