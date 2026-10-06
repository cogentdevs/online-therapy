document.querySelectorAll('[data-newsletter-form]').forEach((form) => {
    const email = form.querySelector('input[name="email"]');
    const button = form.querySelector('button[type="submit"]');
    const feedback = form.querySelector('[data-newsletter-feedback]');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        button.disabled = true;
        feedback.textContent = 'براہ کرم انتظار کریں…';
        feedback.classList.remove('d-none', 'alert-danger', 'alert-success');
        feedback.classList.add('alert-info');

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                credentials: 'same-origin',
                headers: { Accept: 'application/json' },
            });
            const payload = await response.json();

            if (! response.ok || payload.success !== true) {
                throw new Error(payload.message || payload.errors?.email?.[0] || 'Subscription failed.');
            }

            feedback.textContent = payload.message;
            feedback.classList.remove('alert-info', 'alert-danger');
            feedback.classList.add('alert-success');
            email.value = '';
        } catch (error) {
            feedback.textContent = error instanceof Error ? error.message : 'سبسکرپشن محفوظ نہیں ہو سکی، دوبارہ کوشش کریں۔';
            feedback.classList.remove('alert-info', 'alert-success');
            feedback.classList.add('alert-danger');
        } finally {
            button.disabled = false;
        }
    });
});
