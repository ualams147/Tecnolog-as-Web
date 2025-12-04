<?php
session_start();
include 'conexion.php'; 

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. Buscamos el usuario SOLO por email
    $sql = "SELECT * FROM clientes WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Verificamos la contraseña encriptada
    if ($usuario && password_verify($password, $usuario['password'])) {
        
        // --- LOGIN CORRECTO ---
        
        // A. Guardamos datos básicos de sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_rol'] = $usuario['rol'];

        // ==========================================================
        // B. LÓGICA DE RECUPERACIÓN DEL CARRITO (PERSISTENCIA)
        // ==========================================================
        
        // B.1. Si el usuario añadió cosas como "invitado" antes de entrar, las guardamos en la BD
        if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $prod_sess) {
                // Ajusta las claves 'id' y 'cantidad' según cómo guardes tu carrito en sesión
                // Si guardas solo IDs simples, el código cambia ligeramente. 
                // Asumo que $_SESSION['carrito'] es un array de arrays con 'id' y 'cantidad'.
                $pid = $prod_sess['id'];       
                $cant = $prod_sess['cantidad'];
                
                // Comprobar si ya existe en BD
                $stmtCheck = $conn->prepare("SELECT id FROM carrito WHERE cliente_id = ? AND producto_id = ?");
                $stmtCheck->execute([$usuario['id'], $pid]);
                $existe = $stmtCheck->fetch();

                if ($existe) {
                    // Si existe, sumamos cantidad
                    $stmtUpd = $conn->prepare("UPDATE carrito SET cantidad = cantidad + ? WHERE id = ?");
                    $stmtUpd->execute([$cant, $existe['id']]);
                } else {
                    // Si no, insertamos
                    $stmtIns = $conn->prepare("INSERT INTO carrito (cliente_id, producto_id, cantidad) VALUES (?, ?, ?)");
                    $stmtIns->execute([$usuario['id'], $pid, $cant]);
                }
            }
        }

        // B.2. Borramos el carrito temporal de la sesión
        $_SESSION['carrito'] = [];

        // B.3. Descargamos el carrito DEFINITIVO desde la base de datos
        // Hacemos JOIN con productos para tener nombre, precio e imagen disponibles
        $sqlRecuperar = "SELECT c.producto_id, c.cantidad, p.nombre, p.precio, p.imagen_url 
                         FROM carrito c 
                         JOIN productos p ON c.producto_id = p.id 
                         WHERE c.cliente_id = ?";
        $stmtRec = $conn->prepare($sqlRecuperar);
        $stmtRec->execute([$usuario['id']]);
        $productosBD = $stmtRec->fetchAll(PDO::FETCH_ASSOC);

        // B.4. Rellenamos la sesión para que la web funcione normal
        foreach ($productosBD as $item) {
            $_SESSION['carrito'][] = [
                'id' => $item['producto_id'],
                'nombre' => $item['nombre'],
                'precio' => $item['precio'],
                'imagen' => $item['imagen_url'],
                'cantidad' => $item['cantidad']
            ];
        }
        // ==========================================================

        // C. Definimos destino
        $destino = ($usuario['rol'] === 'admin') ? '../Administrador/indexAdmin.php' : '../Cliente/index.php';
        
        // D. Script para auth.js y redirección
        echo "<script>
                localStorage.setItem('usuarioLogueado', 'true');
                localStorage.setItem('usuarioNombre', '" . $usuario['nombre'] . "');
                window.location.href = '$destino';
              </script>";
        exit;

    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - Metalistería Fulsan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/IniciarSesion.css">
    
    <script src="auth.js"></script>
</head>
<body>
    <div class="visitante-login">
        
        <header class="cabecera">
            <div class="container">
                <div class="logo-main">
                    <a href="index.php" class="logo-link">
                        <img src="../imagenes/logo.png" alt="Logo Metalful">
                        <div class="logo-text">
                            <span>Metalistería</span>
                            <strong>Fulsan</strong>
                        </div>
                    </a>
                </div>

                <nav class="nav-bar">
                    <a href="conocenos.php">Conócenos</a>
                    <a href="productos.php">Productos</a>
                    <a href="carrito.php">Carrito</a>
                    <a href="IniciarSesion.php" class="activo" id="link-login">Iniciar Sesión</a>
                </nav>

                <div class="sign-in" id="box-registro">
                    <a href="registro.php" id="link-registro">Registrarse</a>
                </div>
            </div>
        </header>

        <main class="login-section">
            <div class="login-card">
                <h1 class="login-title">Iniciar Sesión</h1>
                
                <?php if(!empty($error)): ?>
                    <div style="background-color:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:15px; text-align:center;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($_GET['registrado'])): ?>
                    <div style="background-color:#d4edda; color:#155724; padding:10px; border-radius:5px; margin-bottom:15px; text-align:center;">
                        ¡Registro exitoso! Ya puedes iniciar sesión.
                    </div>
                <?php endif; ?>

                <form class="login-form" method="POST" action="">
                    <div class="form-row">
                        <label for="email" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                            Email:
                        </label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="ejemplo@gmail.com" required>
                    </div>
                    <div class="form-row">
                        <label for="password" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                            Contraseña:
                        </label>
                        <input type="password" id="password" name="password" class="form-input" required>
                    </div>
                    <button type="submit" class="btn-login-submit">Iniciar Sesión</button>
                    <p class="register-text">
                        ¿Aún no tienes cuenta? <a href="registro.php"><em>Regístrate aquí</em></a>
                    </p>
                </form>
            </div>
        </main>

        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-logo-section">
                        <div class="logo-footer"><img src="../imagenes/footer.png" alt="Logo Metalful"></div>
                        <div class="redes">
                            <a href="https://www.instagram.com/metalfulsansl/" target="_blank" class="instagram-link">
                                <svg viewBox="0 0 24 24" fill="white"><path d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z"/></svg>
                            </a>
                        </div>
                    </div>
                    <div class="footer-links">
                        <div class="enlaces-rapidos">
                            <h3>Enlaces rápidos</h3>
                            <ul>
                                <li><a href="conocenos.php">Conócenos</a></li>
                                <li><a href="productos.php">Productos</a></li>
                                <li><a href="IniciarSesion.php">Iniciar Sesión</a></li>
                            </ul>
                        </div>
                        <div class="contacto-footer">
                            <h3>Contacto</h3>
                            <ul>
                                <li><svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg><a href="https://www.google.com/maps/place//data=!4m2!3m1!1s0xd71fd00684554b1:0xef4e70ab821a7762?sa=X&ved=1t:8290&ictx=111" target="_blank">Extrarradio Cortijo la Purisima, 2P, 18004 Granada</a></li>
                                <li><svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg><a href="tel:652921960">652 921 960</a></li>
                                <li><svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg><a href="mailto:metalfulsan@gmail.com">metalfulsan@gmail.com</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom">
                    <div class="politica-legal">
                        <a href="aviso-legal.php">Aviso Legal</a><span>•</span><a href="privacidad.php">Política de Privacidad</a><span>•</span><a href="cookies.php">Política de Cookies</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>