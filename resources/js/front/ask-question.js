document.querySelectorAll('[data-ask-question-text]').forEach((input) => {
    const counter = input.parentElement.querySelector('[data-ask-question-count]');

    if (!counter) {
        return;
    }

    const updateCount = () => {
        counter.textContent = `${[...input.value].length} / 2000`;
    };

    input.addEventListener('input', updateCount);
    updateCount();
});
