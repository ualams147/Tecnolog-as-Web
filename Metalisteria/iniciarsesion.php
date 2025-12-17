<?php
// 1. INICIAR SESIÓN (Si no está iniciada)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. INCLUIR ARCHIVOS
include 'CabeceraFooter.php'; // Aquí se carga el idioma automáticamente
include 'conexion.php'; 

$error = '';

// 3. RECUPERAR EL ORIGEN (La clave para saber a dónde ir)
$origen = '';
if (isset($_GET['origen'])) {
    $origen = $_GET['origen'];
} elseif (isset($_POST['origen'])) {
    $origen = $_POST['origen'];
}

// 4. PROCESAR LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Buscar usuario
    $sql = "SELECT * FROM clientes WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar contraseña
    $login_valido = false;
    
    if ($usuario) {
        // Usamos 'password' (tu columna correcta en la BD)
        if (password_verify($password, $usuario['password'])) { 
            $login_valido = true;
        } elseif ($password === $usuario['password']) { // Fallback texto plano
            $login_valido = true;
        }
    }

    if ($login_valido) {
        // --- LOGIN CORRECTO ---
        
        // 1. Guardar variables de sesión
        $_SESSION['usuario'] = $usuario; // Activa la cabecera
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_rol'] = $usuario['rol'];

        // 2. FUSIÓN DE CARRITOS (Sesión + BD)
        if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $item_sess) {
                $pid = $item_sess['id']; 
                $cantidad_nueva = $item_sess['cantidad'];
                
                // Verificar si ya existe en BD
                $stmtCheck = $conn->prepare("SELECT id, cantidad FROM carrito WHERE cliente_id = ? AND producto_id = ?");
                $stmtCheck->execute([$usuario['id'], $pid]);
                $row = $stmtCheck->fetch();

                if ($row) {
                    $stmtUpd = $conn->prepare("UPDATE carrito SET cantidad = ? WHERE id = ?");
                    $stmtUpd->execute([$row['cantidad'] + $cantidad_nueva, $row['id']]);
                } else {
                    $stmtIns = $conn->prepare("INSERT INTO carrito (cliente_id, producto_id, cantidad) VALUES (?, ?, ?)");
                    $stmtIns->execute([$usuario['id'], $pid, $cantidad_nueva]);
                }
            }
        }

        // 3. RECARGAR CARRITO DESDE BD (Para tenerlo actualizado en sesión)
        $_SESSION['carrito'] = []; 
        $sqlRec = "SELECT c.producto_id, c.cantidad, p.nombre, p.precio, p.imagen_url, p.referencia, p.color, p.medidas 
                   FROM carrito c JOIN productos p ON c.producto_id = p.id WHERE c.cliente_id = ?";
        $stmtRec = $conn->prepare($sqlRec);
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

        // 4. REDIRECCIÓN SEGÚN ORIGEN
        if ($usuario['rol'] === 'admin') {
            $destino = 'indexadmin.php'; 
        } else {
            // AQUI ESTÁ LA MAGIA:
            if ($origen === 'compra') {
                $destino = 'datosenvio.php'; // Continúa la compra
            } else {
                $destino = 'index.php'; // Login normal
            }
        }
        
        echo "<script>
                localStorage.setItem('usuarioLogueado', 'true');
                window.location.href = '$destino';
              </script>";
        exit;

    } else {
        $error = $lang['login_err_credenciales'];
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['idioma']) ? $_SESSION['idioma'] : 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['login_titulo_pag']; ?></title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/iniciarsesion.css"> 
    <script src="js/auth.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="visitante-login">
        
        <?php sectionheader(5); ?>

        <main class="login-section">
            <div class="login-card">
                <h1 class="login-title"><?php echo $lang['login_h1']; ?></h1>
                
                <?php if(!empty($error)): ?>
                    <div style="background-color:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:15px; text-align:center;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($_GET['registrado'])): ?>
                    <div style="background-color:#d4edda; color:#155724; padding:10px; border-radius:5px; margin-bottom:15px; text-align:center;">
                        <?php echo $lang['login_success_reg']; ?>
                    </div>
                <?php endif; ?>

                <form class="login-form" method="POST" action="">
                    
                    <?php if(!empty($origen)): ?>
                        <input type="hidden" name="origen" value="<?php echo htmlspecialchars($origen); ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <label for="email" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                            <?php echo $lang['login_lbl_email']; ?>
                        </label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="<?php echo $lang['login_ph_email']; ?>" required>
                    </div>
                    <div class="form-row">
                        <label for="password" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                            <?php echo $lang['login_lbl_pass']; ?>
                        </label>
                        
                        <div style="width: 100%; display: flex; flex-direction: column; margin-top: 20px;">
    
                            <div style="position: relative; width: 100%;">
                                <input type="password" id="password" name="password" class="form-input" required style="padding-right: 40px;">
                                <i class="fas fa-eye" id="ojo_login" onclick="mostrarOcultar('password', 'ojo_login')" 
                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #293661;"></i>
                            </div>

                            <div style="text-align: right; margin-top: 8px;">
                                <a href="olvidepassword.php" style="color: #666; font-size: 13px; text-decoration: none; font-weight: 500; transition: color 0.3s;" onmouseover="this.style.color='#293661'" onmouseout="this.style.color='#666'">
                                    ¿Has olvidado tu contraseña?
                                </a>
                            </div>

                        </div>
                    </div>
                    
                    <button type="submit" class="btn-login-submit"><?php echo $lang['login_btn_submit']; ?></button>
                    
                    <p class="register-text">
                        <?php echo $lang['login_txt_no_account']; ?> 
                        <a href="registro.php<?php echo (!empty($origen)) ? '?origen='.$origen : ''; ?>">
                            <em><?php echo $lang['login_link_register']; ?></em>
                        </a>
                    </p>
                </form>
            </div>
        </main>

        <?php sectionfooter(); ?>
    </div>

    <script>
        function mostrarOcultar(idInput, idIcono) {
            var input = document.getElementById(idInput);
            var icono = document.getElementById(idIcono);

            if (input.type === "password") {
                input.type = "text";
                icono.classList.remove("fa-eye");
                icono.classList.add("fa-eye-slash"); // Cambia al icono de ojo tachado
            } else {
                input.type = "password";
                icono.classList.remove("fa-eye-slash");
                icono.classList.add("fa-eye"); // Vuelve al icono de ojo normal
            }
        }
    </script>

</body>
</html>