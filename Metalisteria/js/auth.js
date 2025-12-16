document.addEventListener('DOMContentLoaded', function() {
    // 1. Comprobamos si la "llave" existe en la memoria
    const isLogueado = localStorage.getItem('usuarioLogueado') === 'true';

    // 2. Buscamos los elementos de la cabecera
    const linkLogin = document.getElementById('link-login');     // El de "Iniciar Sesión"
    const linkRegistro = document.getElementById('link-registro'); // El de "Registrarse"
    const boxRegistro = document.getElementById('box-registro');   // La cajita azul/roja

    // 3. Solo si estamos logueados, hacemos cambios
    if (isLogueado) {
        
        // CAMBIO 1: Enlace de la izquierda
        if (linkLogin) {
            linkLogin.textContent = 'Mi Perfil';
            linkLogin.href = 'perfil.php'; 
        }

        // CAMBIO 2: Botón de la derecha
        if (linkRegistro && boxRegistro) {
            linkRegistro.textContent = 'Cerrar Sesión';
            linkRegistro.href = '#';
            
            // Cambiamos el color a ROJO
            boxRegistro.style.backgroundColor = '#dc3545'; 
            
            // --- LÓGICA DE CERRAR SESIÓN ---
            // Al hacer clic, borramos la llave y recargamos
            linkRegistro.addEventListener('click', function(e) {
                e.preventDefault(); // Que no te lleve a registro.html
                
                // BORRAMOS LA SESIÓN
                localStorage.removeItem('usuarioLogueado');
                localStorage.removeItem('usuarioNombre');
                
                // RECARGAMOS LA PÁGINA (o vamos al index) para ver los cambios
                window.location.href = 'logout.php';
            });
        }
    }
});