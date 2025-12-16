document.addEventListener('DOMContentLoaded', function() {

    // ==========================================
    // 1. VALIDACIÓN DEL FORMULARIO DE REGISTRO
    // ==========================================
    const form = document.querySelector('.registro-form');
    
    if (form) {
        // Inputs
        const email = document.getElementById('email');
        const emailConfirm = document.getElementById('email_confirm');
        const pass = document.getElementById('password');
        const passConfirm = document.getElementById('password_confirm');
        
        // --- ESTO FALTABA: Seleccionar los huecos para el texto ---
        const msgEmail = document.getElementById('msg-email');
        const msgPass = document.getElementById('msg-pass');

        const inputs = form.querySelectorAll('.form-input');

        // Limpiar errores cuando el usuario escribe
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                // Quitar borde rojo
                this.classList.remove('error');

                // --- ESTO FALTABA: Borrar el texto si existe ---
                if (this.id === 'email' || this.id === 'email_confirm') {
                    if (msgEmail) msgEmail.textContent = '';
                }
                if (this.id === 'password' || this.id === 'password_confirm') {
                    if (msgPass) msgPass.textContent = '';
                }
            });
        });

        form.addEventListener('submit', function(e) {
            let hayErrores = false;

            // Validar Email
            if (email.value !== emailConfirm.value) {
                e.preventDefault(); 
                email.classList.add('error');
                emailConfirm.classList.add('error');
                
                // --- ESTO FALTABA: Escribir el mensaje ---
                if (msgEmail) msgEmail.textContent = '❌ Los correos electrónicos no coinciden.';
                
                hayErrores = true;
            }

            // Validar Contraseña
            if (pass.value !== passConfirm.value) {
                e.preventDefault(); 
                pass.classList.add('error');
                passConfirm.classList.add('error');
                
                // --- ESTO FALTABA: Escribir el mensaje ---
                if (msgPass) msgPass.textContent = '❌ Las contraseñas no coinciden.';
                
                hayErrores = true;
            }
        });
    }

    // ==========================================
    // 2. GESTIÓN DE SESIÓN (LOGIN / LOGOUT)
    // ==========================================
    const isLogueado = localStorage.getItem('usuarioLogueado') === 'true';
    const linkLogin = document.getElementById('link-login');
    const linkRegistro = document.getElementById('link-registro');
    const boxRegistro = document.getElementById('box-registro');

    if (isLogueado) {
        if (linkLogin) {
            linkLogin.textContent = 'Mi Perfil';
            linkLogin.href = '../cliente/index.php'; 
        }

        if (linkRegistro && boxRegistro) {
            linkRegistro.textContent = 'Cerrar Sesión';
            linkRegistro.href = '#';
            boxRegistro.style.backgroundColor = '#dc3545'; 
            
            linkRegistro.addEventListener('click', function(e) {
                e.preventDefault(); 
                localStorage.removeItem('usuarioLogueado');
                localStorage.removeItem('usuarioNombre');
                window.location.href = '../index.php'; 
            });
        }
    }
});