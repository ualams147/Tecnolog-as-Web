document.addEventListener('DOMContentLoaded', function() {
    // 1. Comprobamos si el usuario ha iniciado sesión
    const usuarioLogueado = localStorage.getItem('usuarioLogueado') === 'true';

    // 2. Buscamos los elementos de la cabecera por su ID
    const linkLogin = document.getElementById('link-login');
    const linkRegistro = document.getElementById('link-registro');
    const boxRegistro = document.getElementById('box-registro'); // El botón azul/rojo

    // 3. Si está logueado, cambiamos los textos
    if (usuarioLogueado) {
        
        // Cambiar "Iniciar Sesión" -> "Mi Perfil"
        if (linkLogin) {
            linkLogin.textContent = 'Mi Perfil';
            linkLogin.href = '#'; // Aquí iría tu pagina de perfil
        }

        // Cambiar "Registrarse" -> "Cerrar Sesión"
        if (linkRegistro && boxRegistro) {
            linkRegistro.textContent = 'Cerrar Sesión';
            linkRegistro.href = '#';
            
            // Cambiamos el color del botón a Rojo para indicar salir
            boxRegistro.style.backgroundColor = '#dc3545'; 
            
            // Lógica para Cerrar Sesión
            linkRegistro.addEventListener('click', function(e) {
                e.preventDefault();
                localStorage.removeItem('usuarioLogueado'); // Borramos la sesión
                window.location.href = 'index.html'; // Volvemos al inicio
            });
        }
    }
});