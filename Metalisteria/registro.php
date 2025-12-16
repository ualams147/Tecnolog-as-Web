<?php
// 1. INICIAR SESIÓN
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'CabeceraFooter.php'; 
include 'conexion.php'; 

$error = '';
$error_email = '';
$error_dni = '';
$success = '';

// --- INICIALIZAR VARIABLES ---
$nombre = '';
$apellidos = '';
$email = '';
$email_confirm = '';
$dni = '';
$telefono = '';
$calle = '';
$numero = '';
$piso = '';
$cp = '';
$localidad = '';

// --- CAPTURAR ORIGEN ---
$origen = '';
if (isset($_GET['origen'])) $origen = $_GET['origen'];
elseif (isset($_POST['origen'])) $origen = $_POST['origen'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... (Tu lógica de POST se mantiene igual) ...
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $email = $_POST['email'] ?? '';
    $email_confirm = $_POST['email_confirm'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $dni = $_POST['dni'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $calle = $_POST['calle'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $piso = $_POST['piso'] ?? '';
    $cp = $_POST['cp'] ?? '';
    $localidad = $_POST['localidad'] ?? '';

    // Validaciones
    if ($email !== $email_confirm) {
        $error = "Los correos electrónicos no coinciden.";
    } elseif ($password !== $password_confirm) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Comprobar Email
        $stmt = $conn->prepare("SELECT id FROM clientes WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) $error_email = "❌ Este correo ya está registrado.";

        // Comprobar DNI
        if (!empty($dni)) {
            $stmt = $conn->prepare("SELECT id FROM clientes WHERE dni = :dni LIMIT 1");
            $stmt->execute([':dni' => $dni]);
            if ($stmt->fetch()) $error_dni = "❌ Este DNI ya está registrado.";
        }

        if (empty($error) && empty($error_email) && empty($error_dni)) {
            try {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO clientes (nombre, apellidos, email, password, dni, telefono, direccion, numero, piso, ciudad, codigo_postal, rol) 
                        VALUES (:nombre, :apellidos, :email, :password, :dni, :telefono, :direccion, :numero, :piso, :ciudad, :codigo_postal, 'cliente')";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':nombre' => $nombre, ':apellidos' => $apellidos, ':email' => $email,
                    ':password' => $password_hash,
                    ':dni' => $dni, ':telefono' => $telefono,
                    ':direccion' => $calle, ':numero' => $numero, ':piso' => $piso,
                    ':ciudad' => $localidad, ':codigo_postal' => $cp
                ]);

                // Login automático
                $nuevo_id = $conn->lastInsertId();
                $stmtUser = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
                $stmtUser->execute([$nuevo_id]);
                $nuevo_usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

                $_SESSION['usuario'] = $nuevo_usuario; 
                $_SESSION['usuario_id'] = $nuevo_usuario['id'];
                $_SESSION['usuario_nombre'] = $nuevo_usuario['nombre'];
                $_SESSION['usuario_rol'] = 'cliente';

                if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
                    foreach ($_SESSION['carrito'] as $item_sess) {
                        $stmtIns = $conn->prepare("INSERT INTO carrito (cliente_id, producto_id, cantidad) VALUES (?, ?, ?)");
                        $stmtIns->execute([$nuevo_id, $item_sess['id'], $item_sess['cantidad']]);
                    }
                }

                $destino = ($origen === 'compra') ? 'datosenvio.php' : 'index.php';
                echo "<script>localStorage.setItem('usuarioLogueado', 'true'); window.location.href = '$destino';</script>";
                exit;

            } catch (PDOException $e) {
                $error = "Hubo un error técnico: " . $e->getMessage();
            }
        }
    }
}
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
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="js/auth.js"></script>
</head>
<body>
    <div class="visitante-registro">
        
        <?php sectionheader(); ?>

        <main class="registro-section">
            <div class="registro-card">
                <h1 class="registro-title">Regístrate</h1>
                
                <?php if(!empty($error)): ?>
                    <div style="background-color:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:15px; text-align:center;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form class="registro-form" id="form-registro" method="POST" action="">
                    
                    <?php if(!empty($origen)): ?>
                        <input type="hidden" name="origen" value="<?php echo htmlspecialchars($origen); ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <label for="nombre" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg> Nombre:
                        </label>
                        <input type="text" id="nombre" name="nombre" class="form-input" value="<?php echo htmlspecialchars($nombre); ?>" placeholder="Ej: Juan" required>
                    </div>

                    <div class="form-row">
                        <label for="apellidos" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg> Apellidos:
                        </label>
                        <input type="text" id="apellidos" name="apellidos" class="form-input" value="<?php echo htmlspecialchars($apellidos); ?>" placeholder="Ej: Pérez García" required>
                    </div>

                    <div class="form-row">
                        <label for="email" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg> Correo electrónico:
                        </label>
                        <input type="email" id="email" name="email" class="form-input <?php echo !empty($error_email) ? 'error' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>" placeholder="ejemplo@gmail.com" required>
                        <?php if(!empty($error_email)): ?> <span class="error-text"><?php echo $error_email; ?></span> <?php endif; ?>
                    </div>

                    <div class="form-row">
                        <label for="email_confirm" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg> 
                            Confirmación Correo:
                        </label>
                        <input type="email" id="email_confirm" name="email_confirm" class="form-input" value="<?php echo htmlspecialchars($email_confirm); ?>" placeholder="Repite tu correo" required>
                    </div>

                    <div class="form-row alinear-arriba">
                        <label for="password" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg> 
                            Contraseña:
                        </label>
                        
                        <div class="grupo-input-columna">
                            <div style="position: relative;">
                                <input type="password" id="password" name="password" class="form-input" required style="padding-right: 40px;">
                                <i class="fas fa-eye" id="ojo_nueva" onclick="mostrarOcultar('password', 'ojo_nueva')" 
                                   style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #293661;"></i>
                            </div>
                            
                            <ul id="lista-requisitos-pass"></ul> 
                        </div>
                    </div>

                    <div class="form-row">
                        <label for="password_confirm" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg> 
                            Repetir contraseña:
                        </label>
                        
                        <div style="flex: 1; position: relative;">
                            <input type="password" id="password_confirm" name="password_confirm" class="form-input" required style="padding-right: 40px; width: 100%;">
                            <i class="fas fa-eye" id="ojo_confirm" onclick="mostrarOcultar('password_confirm', 'ojo_confirm')" 
                               style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #293661;"></i>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="dni" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg> DNI/NIF/NIE:
                        </label>
                        <input type="text" id="dni" name="dni" class="form-input <?php echo !empty($error_dni) ? 'error' : ''; ?>" value="<?php echo htmlspecialchars($dni); ?>" placeholder="Ej: 12345678A" required>
                        <?php if(!empty($error_dni)): ?> <span class="error-text"><?php echo $error_dni; ?></span> <?php endif; ?>
                    </div>

                    <div class="form-row">
                        <label for="telefono" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg> Teléfono:
                        </label>
                        <input type="tel" id="telefono" name="telefono" class="form-input" value="<?php echo htmlspecialchars($telefono); ?>" placeholder="Ej: 600 000 000" required>
                    </div>

                    <div class="form-row">
                        <label for="calle" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg> Calle:
                        </label>
                        <input type="text" id="calle" name="calle" class="form-input" value="<?php echo htmlspecialchars($calle); ?>" placeholder="Ej: Calle Recogidas" required>
                    </div>

                    <div class="form-row">
                        <label for="numero" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg> Nº / Piso:
                        </label>
                        <div class="input-group">
                            <input type="text" id="numero" name="numero" class="form-input" value="<?php echo htmlspecialchars($numero); ?>" placeholder="Ej: 12" required>
                            <input type="text" id="piso" name="piso" class="form-input" value="<?php echo htmlspecialchars($piso); ?>" placeholder="Ej: 3º A">
                        </div>
                    </div>

                    <div class="form-row">
                        <label for="cp" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg> Código Postal / Localidad:
                        </label>
                        <div class="input-group">
                            <input type="text" id="cp" name="cp" class="form-input" value="<?php echo htmlspecialchars($cp); ?>" placeholder="Ej: 18001" required>
                            <input type="text" id="localidad" name="localidad" class="form-input" value="<?php echo !empty($localidad) ? htmlspecialchars($localidad) : 'Granada'; ?>" placeholder="Ej: Armilla">
                        </div>
                    </div>

                    <button type="submit" id="btn-registrarse" class="btn-register-submit">Registrarme</button>

                    <p class="register-text">
                        ¿Ya tienes cuenta? 
                        <a href="iniciarsesion.php<?php echo (!empty($origen)) ? '?origen='.$origen : ''; ?>">
                            <em>Inicia sesión aquí</em>
                        </a>
                    </p>

                </form> 
            </div>
        </main>

        <?php sectionfooter(); ?>
    </div>

    <script src="js/AlgoritmoDNIs.js"></script> <script src="js/validarpasswd.js"></script> <script>
        document.addEventListener("DOMContentLoaded", function() {
            
            // 1. ACTIVAR VALIDACIÓN DE CONTRASEÑAS (Bloquea el botón visualmente)
            activarValidacionPassword(
                'password',              
                'password_confirm',      
                'lista-requisitos-pass', 
                'btn-registrarse'        
            );

            // 2. ACTIVAR CONTROL DE DNI AL ENVIAR (Bloquea el envío real)
            const form = document.getElementById('form-registro');
            const inputDNI = document.getElementById('dni');

            form.addEventListener('submit', function(e) {
                // Paso A: Verificar que todos los campos requeridos HTML estén llenos
                if (!form.checkValidity()) {
                    // Si falta algo, el navegador lo mostrará, pero por seguridad paramos
                    // (Normalmente el navegador ya para el submit antes de llegar aquí, pero esto asegura)
                    return; 
                }

                // Paso B: Verificar DNI con tu algoritmo JS
                if (typeof validarDocumento === 'function') {
                    const esDniValido = validarDocumento(inputDNI);
                    
                    if (!esDniValido) {
                        e.preventDefault(); // ¡STOP! No enviamos el formulario
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'DNI Incorrecto',
                            text: 'El formato del DNI no es válido o la letra es incorrecta.',
                            confirmButtonColor: '#293661'
                        });
                        
                        inputDNI.focus(); // Llevamos al usuario al campo DNI
                    }
                }
            });
        });
    </script>
</body>
</html>