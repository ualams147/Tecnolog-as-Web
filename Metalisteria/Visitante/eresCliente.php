<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¿Eres Cliente? - Metalistería Fulsan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/eresCliente.css">
</head>
<body>
    <div class="visitante-cliente">
        
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
                    <a href="IniciarSesion.php" id="link-login">Iniciar Sesión</a>
                </nav>

                <div class="sign-in" id="box-registro">
                    <a href="registro.php" id="link-registro">Registrarse</a>
                </div>
            </div>
        </header>

        <main class="cliente-section">
            
            <div class="cliente-card">
                
                <h1 class="cliente-title">Inicia sesión o crea una cuenta para continuar</h1>

                <div class="buttons-row">
                    <a href="IniciarSesion.php?origen=compra" class="btn-big-action">Iniciar Sesión</a>
                    <a href="registro.php?origen=compra" class="btn-big-action">Registro</a>
                </div>

                <a href="carrito.php" class="btn-back-pill">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                    Volver atrás
                </a>

            </div>

        </main>

        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-logo-section">
                        <div class="logo-footer">
                            <img src="../imagenes/footer.png" alt="Logo Metalful">
                        </div>
                        <div class="redes">
                           </div>
                    </div>
                    <div class="footer-links">
                        </div>
                </div>
                <div class="footer-bottom">
                     </div>
            </div>
        </footer>
    </div>
</body>
</html>
2. Archivo Visitante/registro.php
El IniciarSesion.php ya lo arreglamos antes para que guardara el carrito. Ahora debemos hacer lo mismo en el registro. Si no ponemos esto, cuando alguien se registre, perderá su carrito.

Copia y pega este código en Visitante/registro.php:

PHP

<?php
session_start();
include '../conexion.php'; 

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recogemos los datos del formulario
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $email = $_POST['email'];
    $email_confirm = $_POST['email_confirm'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $dni = $_POST['dni'];
    $telefono = $_POST['telefono'];
    $calle = $_POST['calle']; 
    $numero = $_POST['numero'];
    $piso = $_POST['piso'];
    $cp = $_POST['cp'];       
    $localidad = $_POST['localidad']; 

    // 2. Validaciones
    if ($email !== $email_confirm) {
        $error = "Los correos electrónicos no coinciden.";
    } elseif ($password !== $password_confirm) {
        $error = "Las contraseñas no coinciden.";
    } else {
        try {
            // Encriptamos contraseña para seguridad (RECOMENDADO)
            // Si tu IniciarSesion usa password_verify, usa esto:
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            // Si tu IniciarSesion usa texto plano (como tenías al principio), usa $password directo.
            // Usaremos el hash porque te pasé el código seguro antes.

            $sql = "INSERT INTO clientes (nombre, apellidos, email, password, dni, telefono, direccion, numero, piso, ciudad, codigo_postal, rol) 
                    VALUES (:nombre, :apellidos, :email, :pass, :dni, :telefono, :direccion, :numero, :piso, :ciudad, :codigo_postal, 'cliente')";
            
            $stmt = $conn->prepare($sql);
            
            $stmt->execute([
                ':nombre' => $nombre,
                ':apellidos' => $apellidos,
                ':email' => $email,
                ':pass' => $password_hash, // Usamos la encriptada
                ':dni' => $dni,
                ':telefono' => $telefono,
                ':direccion' => $calle,
                ':numero' => $numero,
                ':piso' => $piso,
                ':ciudad' => $localidad,
                ':codigo_postal' => $cp
            ]);

            // --- LOGIN AUTOMÁTICO TRAS REGISTRO ---
            $nuevo_id = $conn->lastInsertId();
            $_SESSION['usuario_id'] = $nuevo_id;
            $_SESSION['usuario_nombre'] = $nombre;
            $_SESSION['usuario_rol'] = 'cliente';

            // ============================================================
            // 3. LÓGICA CRÍTICA: PASAR CARRITO DE VISITANTE A BASE DE DATOS
            // ============================================================
            if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
                foreach ($_SESSION['carrito'] as $item) {
                    // Insertamos cada producto de la sesión en la tabla carrito del nuevo usuario
                    $sqlCart = "INSERT INTO carrito (cliente_id, producto_id, cantidad) VALUES (:uid, :pid, :cant)";
                    $stmtCart = $conn->prepare($sqlCart);
                    $stmtCart->execute([
                        ':uid' => $nuevo_id,
                        ':pid' => $item['id'], // Asegúrate de que tu array de sesión tiene la clave 'id'
                        ':cant' => $item['cantidad']
                    ]);
                }
            }
            // ============================================================

            // 4. REDIRECCIÓN INTELIGENTE
            // Si venía de intentar comprar, lo mandamos directo a los datos de envío
            $destino = '../Cliente/datosEnvio.php'; // O ../Cliente/index.php si prefieres

            echo "<script>
                localStorage.setItem('usuarioLogueado', 'true');
                localStorage.setItem('usuarioNombre', '" . htmlspecialchars($nombre) . "');
                window.location.href = '$destino';
              </script>";
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "El correo o DNI ya están registrados.";
            } else {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/registro.css">
    <script src="../js/auth.js"></script>
</head>
<body>
    <div class="visitante-registro">
        
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
                    <a href="IniciarSesion.php" id="link-login">Iniciar Sesión</a>
                </nav>

                <div class="sign-in" id="box-registro">
                    <a href="registro.php" id="link-registro">Registrarse</a>
                </div>
            </div>
        </header>

        <main class="registro-section">
            <div class="registro-card">
                <h1 class="registro-title">Regístrate</h1>
                
                <?php if(!empty($error)): ?>
                    <div style="background-color:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:15px; text-align:center;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form class="registro-form" method="POST" action="">
                    <div class="form-row">
                        <label for="nombre" class="label-icon">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" class="form-input" required>
                    </div>
                     <div class="form-row">
                        <label for="apellidos" class="label-icon">Apellidos:</label>
                        <input type="text" id="apellidos" name="apellidos" class="form-input" required>
                    </div>
                    <div class="form-row">
                        <label for="email" class="label-icon">Email:</label>
                        <input type="email" id="email" name="email" class="form-input" required>
                    </div>
                     <div class="form-row">
                        <label for="email_confirm" class="label-icon">Confirmar Email:</label>
                        <input type="email" id="email_confirm" name="email_confirm" class="form-input" required>
                    </div>
                     <div class="form-row">
                        <label for="password" class="label-icon">Contraseña:</label>
                        <input type="password" id="password" name="password" class="form-input" required>
                    </div>
                     <div class="form-row">
                        <label for="password_confirm" class="label-icon">Repetir Contraseña:</label>
                        <input type="password" id="password_confirm" name="password_confirm" class="form-input" required>
                    </div>
                     <div class="form-row">
                        <label for="dni" class="label-icon">DNI:</label>
                        <input type="text" id="dni" name="dni" class="form-input">
                    </div>
                    <div class="form-row">
                        <label for="telefono" class="label-icon">Teléfono:</label>
                        <input type="tel" id="telefono" name="telefono" class="form-input">
                    </div>
                    <div class="form-row">
                        <label for="calle" class="label-icon">Calle:</label>
                        <input type="text" id="calle" name="calle" class="form-input" required>
                    </div>
                    <div class="form-row">
                        <div class="input-group">
                            <input type="text" name="numero" placeholder="Nº" class="form-input" required>
                            <input type="text" name="piso" placeholder="Piso" class="form-input">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="input-group">
                            <input type="text" name="cp" placeholder="CP" class="form-input" required>
                            <input type="text" name="localidad" placeholder="Localidad" class="form-input">
                        </div>
                    </div>

                    <button type="submit" class="btn-register-submit">Registrarme</button>
                    <p class="register-text">¿Ya tienes cuenta? <a href="IniciarSesion.php"><em>Inicia sesión aquí</em></a></p>
                </form>
            </div>
        </main>
        </div>
</body>
</html>