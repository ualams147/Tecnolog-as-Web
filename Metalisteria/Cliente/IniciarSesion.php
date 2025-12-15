<?php
// 1. INCLUIR FUNCIONES (Esto inicia la sesión)
include '../CabeceraFooter.php'; 

// 2. CONEXIÓN
include '../conexion.php'; 

$error = '';

// ==========================================
// LÓGICA DE LOGIN (Tu código adaptado)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. Buscamos el usuario
    $sql = "SELECT * FROM clientes WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Verificamos contraseña
    if ($usuario && password_verify($password, $usuario['password'])) {
        
        // A. Guardamos sesión (Array 'usuario' para coherencia con otros archivos)
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'rol' => $usuario['rol']
        ];
        // Compatibilidad extra por si usas estas variables sueltas en otro lado
        $_SESSION['usuario_id'] = $usuario['id']; 

        // ==========================================================
        // B. FUSIONAR CARRITO (Invitado -> Base de Datos)
        // ==========================================================
        if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $prod_sess) {
                $pid = $prod_sess['id'];       
                $cant = $prod_sess['cantidad'];
                
                // Comprobar si ya existe en BD
                $stmtCheck = $conn->prepare("SELECT id FROM carrito WHERE cliente_id = ? AND producto_id = ?");
                $stmtCheck->execute([$usuario['id'], $pid]);
                $existe = $stmtCheck->fetch();

                if ($existe) {
                    $stmtUpd = $conn->prepare("UPDATE carrito SET cantidad = cantidad + ? WHERE id = ?");
                    $stmtUpd->execute([$cant, $existe['id']]);
                } else {
                    $stmtIns = $conn->prepare("INSERT INTO carrito (cliente_id, producto_id, cantidad) VALUES (?, ?, ?)");
                    $stmtIns->execute([$usuario['id'], $pid, $cant]);
                }
            }
        }

        // B.2. Vaciar sesión temporal
        $_SESSION['carrito'] = [];

        // B.3. Recuperar carrito DEFINITIVO de la BD
        $sqlRecuperar = "SELECT c.producto_id, c.cantidad, p.nombre, p.precio, p.imagen, p.referencia 
                         FROM carrito c 
                         JOIN productos p ON c.producto_id = p.id 
                         WHERE c.cliente_id = ?";
        $stmtRec = $conn->prepare($sqlRecuperar);
        $stmtRec->execute([$usuario['id']]);
        $productosBD = $stmtRec->fetchAll(PDO::FETCH_ASSOC);

        // B.4. Rellenar sesión (IMPORTANTE: Usamos el ID como clave del array)
        foreach ($productosBD as $item) {
            // Nota: Asegúrate de si tu campo en BD es 'imagen' o 'imagen_url'
            $img = $item['imagen'] ?? $item['imagen_url'] ?? 'sin_imagen.jpg';
            
            $_SESSION['carrito'][$item['producto_id']] = [
                'id' => $item['producto_id'],
                'nombre' => $item['nombre'],
                'precio' => $item['precio'],
                'imagen' => $img,
                'referencia' => $item['referencia'] ?? '',
                'cantidad' => $item['cantidad']
            ];
        }

        // C. Redirección
        $destino = ($usuario['rol'] === 'admin') ? '../Administrador/indexAdmin.php' : '../Cliente/index.php';
        
        // Usamos JS para redirigir porque ya se han enviado cabeceras HTML arriba
        echo "<script>
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
        
        <?php sectionheader(5); ?>

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

        <?php sectionfooter(); ?>
    </div>
</body>
</html>