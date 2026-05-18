document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('.needs-validation');

    forms.forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        });
    });

    const confirmForms = document.querySelectorAll('[data-confirm]');

    confirmForms.forEach(form => {
        form.addEventListener('submit', event => {
            const mensaje = form.dataset.confirm || '¿Confirmas esta acción?';

            if (!confirm(mensaje)) {
                event.preventDefault();
            }
        });
    });

    const previews = document.querySelectorAll('[data-preview]');

    previews.forEach(input => {
        input.addEventListener('change', () => {
            const img = document.querySelector(input.dataset.preview);

            if (img && input.files[0]) {
                img.src = URL.createObjectURL(input.files[0]);
            }
        });
    });
});