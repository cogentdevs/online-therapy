document.querySelectorAll('[data-advertise-form]').forEach((form) => {
    const fromInput = form.querySelector('[data-advertise-from]');
    const toInput = form.querySelector('[data-advertise-to]');
    const duration = form.querySelector('[data-advertise-duration]');
    const message = form.querySelector('[data-advertise-message]');
    const options = [...form.querySelectorAll('[data-advertise-placement]')];
    const initialSelections = new Set(options.filter((option) => option.querySelector('input')?.checked)
        .map((option) => `${option.dataset.pageName}:${option.dataset.place}`));
    let activeRequest;
    let initialLoad = true;

    const resetOptions = () => {
        options.forEach((option) => {
            const checkbox = option.querySelector('input');
            checkbox.checked = false;
            checkbox.disabled = true;
            option.classList.remove('is-available', 'is-unavailable');
            option.querySelector('[data-advertise-placement-status]').textContent = 'تاریخیں منتخب کریں';
        });
    };

    const update = async () => {
        activeRequest?.abort();
        activeRequest = null;
        resetOptions();

        if (fromInput.value) {
            toInput.min = fromInput.value;
        }

        if (!fromInput.value || !toInput.value || fromInput.value < fromInput.min || toInput.value < fromInput.value) {
            duration.textContent = 'کل مدت: تاریخیں منتخب کریں';
            message.textContent = 'تشہیری مقامات دیکھنے کے لیے پہلے آغاز اور اختتام کی تاریخ منتخب کریں۔';
            initialLoad = false;
            return;
        }

        const start = Date.parse(`${fromInput.value}T00:00:00Z`);
        const end = Date.parse(`${toInput.value}T00:00:00Z`);

        if (!Number.isFinite(start) || !Number.isFinite(end)) {
            duration.textContent = 'کل مدت: تاریخیں منتخب کریں';
            message.textContent = 'درست تاریخیں منتخب کریں۔';
            initialLoad = false;
            return;
        }

        duration.textContent = `کل مدت: ${Math.round((end - start) / 86400000) + 1} دن`;
        message.textContent = 'تشہیری مقامات کی دستیابی دیکھی جا رہی ہے…';

        const controller = new AbortController();
        activeRequest = controller;
        const url = new URL(form.dataset.availabilityUrl, window.location.origin);
        url.searchParams.set('from_date', fromInput.value);
        url.searchParams.set('to_date', toInput.value);

        try {
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: { Accept: 'application/json' },
                signal: controller.signal,
            });

            if (!response.ok) {
                throw new Error('Availability request failed');
            }

            const result = await response.json();
            const availability = new Map(result.placements.map((placement) => [
                `${placement.page_name}:${placement.place}`, placement.available,
            ]));

            options.forEach((option) => {
                const key = `${option.dataset.pageName}:${option.dataset.place}`;
                const available = availability.get(key) === true;
                const checkbox = option.querySelector('input');
                checkbox.disabled = !available;
                checkbox.checked = available && initialLoad && initialSelections.has(key);
                option.classList.toggle('is-available', available);
                option.classList.toggle('is-unavailable', !available);
                option.querySelector('[data-advertise-placement-status]').textContent = available
                    ? 'دستیاب'
                    : 'منتخب تاریخوں میں دستیاب نہیں';
            });

            message.textContent = 'دستیاب مقامات میں سے ایک یا زیادہ منتخب کریں۔';
        } catch (error) {
            if (error.name !== 'AbortError') {
                message.textContent = 'دستیابی معلوم نہیں ہو سکی۔ تاریخیں دوبارہ منتخب کریں۔';
            }
        } finally {
            if (activeRequest === controller) {
                activeRequest = null;
            }
            initialLoad = false;
        }
    };

    fromInput.addEventListener('change', update);
    toInput.addEventListener('change', update);
    update();
});
