document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('.login-form');

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Evitamos que el formulario se envíe al servidor real

            // 1. Guardamos en la memoria del navegador que el usuario entró
            localStorage.setItem('usuarioLogueado', 'true');

            // 2. Redirigimos a la página de inicio
            window.location.href = 'index.html';
        });
    }
});