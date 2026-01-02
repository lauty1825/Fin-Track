document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registerForm');
    if (!form) return;
    const pass = document.getElementById('pass'), pass2 = document.getElementById('pass2'), alertBox = document.getElementById('formAlert');

    form.addEventListener('submit', e => {
        let hasError = false;
        alertBox.classList.add('d-none'); // Ocultar alerta previa

        if (pass.value.length < 8) {
            e.preventDefault();
            alertBox.className = 'alert alert-danger';
            alertBox.textContent = 'La contraseña debe tener 8+ caracteres';
            alertBox.classList.remove('d-none');
        } else if (pass.value !== pass2.value) {
            e.preventDefault();
            alertBox.className = 'alert alert-danger';
            alertBox.textContent = 'Las contraseñas no coinciden';
            alertBox.classList.remove('d-none');
        }
    });
});
