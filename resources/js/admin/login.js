document.addEventListener('DOMContentLoaded', () => {
    const passwordInput = document.querySelector('#password');
    const passwordToggle = document.querySelector('[data-password-toggle]');

    if (!passwordInput || !passwordToggle) {
        return;
    }

    passwordToggle.addEventListener('click', () => {
        const shouldShowPassword = passwordInput.type === 'password';

        passwordInput.type = shouldShowPassword ? 'text' : 'password';
        passwordToggle.setAttribute('aria-pressed', String(shouldShowPassword));
        passwordToggle.setAttribute('aria-label', shouldShowPassword ? 'Hide password' : 'Show password');
    });
});
