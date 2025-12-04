<?php
session_start();
include '../conexion.php'; 

$error = '';

// RECUPERAR EL ORIGEN (Para saber a dónde ir después)
// 1. Si viene por GET (URL), lo cogemos.
// 2. Si viene por POST (Formulario enviado), lo mantenemos.
$origen = '';
if (isset($_GET['origen'])) {
    $origen = $_GET['origen'];
} elseif (isset($_POST['origen'])) {
    $origen = $_POST['origen'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. Buscar usuario
    $sql = "SELECT * FROM clientes WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Verificación Híbrida (Encriptada o Texto Plano)
    $login_valido = false;
    if ($usuario) {
        if (password_verify($password, $usuario['password'])) {
            $login_valido = true;
        } elseif ($password === $usuario['password']) {
            $login_valido = true;
        }
    }

    if ($login_valido) {
        // --- LOGIN CORRECTO ---
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_rol'] = $usuario['rol'];

        // --- FUSIÓN DE CARRITOS ---
        if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $item_sess) {
                $pid = $item_sess['id']; 
                $cantidad_nueva = $item_sess['cantidad'];
                
                $stmtCheck = $conn->prepare("SELECT id, cantidad FROM carrito WHERE cliente_id = ? AND producto_id = ?");
                $stmtCheck->execute([$usuario['id'], $pid]);
                $row = $stmtCheck->fetch();

                if ($row) {
                    $nueva_cantidad_total = $row['cantidad'] + $cantidad_nueva;
                    $stmtUpd = $conn->prepare("UPDATE carrito SET cantidad = ? WHERE id = ?");
                    $stmtUpd->execute([$nueva_cantidad_total, $row['id']]);
                } else {
                    $stmtIns = $conn->prepare("INSERT INTO carrito (cliente_id, producto_id, cantidad) VALUES (?, ?, ?)");
                    $stmtIns->execute([$usuario['id'], $pid, $cantidad_nueva]);
                }
            }
        }

        // Recargar carrito desde BD
        $_SESSION['carrito'] = []; 
        $sqlRecuperar = "SELECT c.producto_id, c.cantidad, p.nombre, p.precio, p.imagen_url, p.referencia, p.color, p.medidas 
                         FROM carrito c 
                         JOIN productos p ON c.producto_id = p.id 
                         WHERE c.cliente_id = ?";
        $stmtRec = $conn->prepare($sqlRecuperar);
        $stmtRec->execute([$usuario['id']]);
        $productosBD = $stmtRec->fetchAll(PDO::FETCH_ASSOC);

        foreach ($productosBD as $item) {
            $_SESSION['carrito'][$item['producto_id']] = [
                'id' => $item['producto_id'],
                'nombre' => $item['nombre'],
                'precio' => $item['precio'],
                'imagen' => $item['imagen_url'],
                'referencia' => $item['referencia'],
                'color' => $item['color'],
                'medidas' => $item['medidas'],
                'cantidad' => $item['cantidad']
            ];
        }

        // ============================================================
        // DECISIÓN DE REDIRECCIÓN (AQUÍ ESTÁ LA CLAVE)
        // ============================================================
        
        if ($usuario['rol'] === 'admin') {
            $destino = '../Administrador/indexAdmin.php';
        } else {
            // Si venimos de comprar -> Datos Envio
            if ($origen === 'compra') {
                $destino = '../Cliente/datosEnvio.php';
            } else {
                // Si venimos de la cabecera -> Inicio Cliente
                $destino = '../Cliente/index.php';
            }
        }
        
        echo "<script>
                localStorage.setItem('usuarioLogueado', 'true');
                localStorage.setItem('usuarioNombre', '" . htmlspecialchars($usuario['nombre']) . "');
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
    <script src="../js/auth.js"></script>
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

                <form class="login-form" method="POST" action="">
                    
                    <input type="hidden" name="origen" value="<?php echo htmlspecialchars($origen); ?>">

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
                        ¿Aún no tienes cuenta? <a href="registro.php<?php echo ($origen ? '?origen='.$origen : ''); ?>"><em>Regístrate aquí</em></a>
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