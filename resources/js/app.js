// Live search/filter: debounced auto-submit for GET filter forms.
document.querySelectorAll('form[data-live-filter]').forEach((form) => {
    let timer = null;

    const submitSoon = () => {
        clearTimeout(timer);
        timer = setTimeout(() => form.requestSubmit(), 300);
    };

    form.addEventListener('input', (event) => {
        if (event.target.matches('input[type="text"], input[type="search"]')) {
            submitSoon();
        }
    });

    form.addEventListener('change', (event) => {
        if (event.target.matches('select')) {
            submitSoon();
        }
    });

    form.addEventListener('submit', () => clearTimeout(timer));
});
