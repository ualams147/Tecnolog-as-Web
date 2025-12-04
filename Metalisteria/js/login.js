document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('.login-form');

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Evita que se recargue la página de golpe

            // 1. GUARDAMOS LA SESIÓN con la clave exacta
            localStorage.setItem('usuarioLogueado', 'true');

            // 2. Redirigimos al inicio
            window.location.href = 'index.php';
        });
    }
});