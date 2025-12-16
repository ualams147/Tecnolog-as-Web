<?php
include 'CabeceraFooter.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regístrate - Metalistería Fulsan</title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/registro.css">
</head>
<body>
    <div class="visitante-registro">
        
        <?php sectionheader(); ?>

        <main class="registro-section">
            <div class="registro-card">
                <h1 class="registro-title">Regístrate</h1>

                <form class="registro-form">
                    
                    <div class="form-row">
                        <label for="nombre" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            Nombre:
                        </label>
                        <input type="text" id="nombre" name="nombre" class="form-input" placeholder="Ej: Juan" required>
                    </div>

                    <div class="form-row">
                        <label for="apellidos" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            Apellidos:
                        </label>
                        <input type="text" id="apellidos" name="apellidos" class="form-input" placeholder="Ej: Pérez García" required>
                    </div>

                    <div class="form-row">
                        <label for="email" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                            Correo electrónico:
                        </label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="ejemplo@gmail.com" required>
                    </div>

                    <div class="form-row">
                        <label for="email_confirm" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                            Confirmación Correo electrónico:
                        </label>
                        <input type="email" id="email_confirm" name="email_confirm" class="form-input" placeholder="Repite tu correo" required>
                    </div>

                    <div class="form-row">
                        <label for="password" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                            Contraseña:
                        </label>
                        <input type="password" id="password" name="password" class="form-input" required>
                    </div>

                    <div class="form-row">
                        <label for="password_confirm" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                            Repetir contraseña:
                        </label>
                        <input type="password" id="password_confirm" name="password_confirm" class="form-input" required>
                    </div>

                    <div class="form-row">
                        <label for="dni" class="label-icon">
                            <svg viewBox="0 0 24 24">
                                 <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                             </svg>
                            DNI/NIF/NIE:
                        </label>
                         <input type="text" id="dni" name="dni" class="form-input" placeholder="Ej: 12345678A">
                    </div>

                    <div class="form-row">
                        <label for="telefono" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                            Teléfono:
                        </label>
                        <input type="tel" id="telefono" name="telefono" class="form-input" placeholder="Ej: 600 000 000">
                    </div>

                    <div class="form-row">
                        <label for="calle" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                            Calle:
                        </label>
                        <input type="text" id="calle" name="calle" class="form-input" placeholder="Ej: Calle Recogidas" required>
                    </div>

                    <div class="form-row">
                        <label for="numero" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                            Nº / Piso:
                        </label>
                        <div class="input-group">
                            <input type="text" id="numero" name="numero" class="form-input" placeholder="Ej: 12" required>
                            <input type="text" id="piso" name="piso" class="form-input" placeholder="Ej: 3º A, Esc. Dcha">
                        </div>
                    </div>

                    <div class="form-row">
                        <label for="cp" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                            Código Postal / Localidad:
                        </label>
                        <div class="input-group">
                            <input type="text" id="cp" name="cp" class="form-input" placeholder="Ej: 18001" required>
                            <input type="text" id="localidad" name="localidad" class="form-input" value="Granada" placeholder="Ej: Armilla">
                        </div>
                    </div>

                    <button type="submit" class="btn-register-submit">Registrarme</button>

                    <p class="register-text">
                        ¿Ya tienes cuenta? <a href="IniciarSesion.php"><em>Inicia sesión aquí</em></a>
                    </p>

                </form>
            </div>
        </main>

        <?php sectionfooter(); ?>
    </div>
    <script src="js/auth.js"></script>
</body>
</html>