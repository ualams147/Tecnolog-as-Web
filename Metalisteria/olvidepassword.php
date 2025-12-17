<?php
// olvidepassword.php
session_start();
require_once 'conexion.php';
require_once 'CabeceraFooter.php'; // Para cargar estilos y cabecera

$paso = 1; // 1: Verificar identidad, 2: Cambiar contraseña, 3: Éxito
$error = "";

// --- LÓGICA DE VERIFICACIÓN (PASO 1) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verificar_identidad'])) {
    $email = trim($_POST['email']);
    $dni = trim($_POST['dni']);
    $telefono = trim($_POST['telefono']);

    // Buscamos un usuario que coincida con LOS TRES datos
    $sql = "SELECT id, nombre FROM clientes WHERE email = ? AND dni = ? AND telefono = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email, $dni, $telefono]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // ¡Identidad confirmada! Guardamos ID en sesión temporalmente
        $_SESSION['reset_user_id'] = $usuario['id'];
        $_SESSION['reset_user_nombre'] = $usuario['nombre'];
        $paso = 2; // Pasamos a la pantalla de cambio
    } else {
        $error = "Los datos introducidos no coinciden con ningún cliente registrado.";
    }
}

// --- LÓGICA DE CAMBIO DE CONTRASEÑA (PASO 2) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_password'])) {
    if (isset($_SESSION['reset_user_id'])) {
        $pass1 = $_POST['pass_nueva'];
        $pass2 = $_POST['pass_confirm'];

        if (strlen($pass1) < 8) {
            $error = "La contraseña debe tener al menos 8 caracteres.";
            $paso = 2;
        } elseif ($pass1 !== $pass2) {
            $error = "Las contraseñas no coinciden.";
            $paso = 2;
        } else {
            // Todo correcto: Actualizamos
            $hash = password_hash($pass1, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE clientes SET password = ? WHERE id = ?");
            
            if ($stmt->execute([$hash, $_SESSION['reset_user_id']])) {
                // Limpiamos sesión de reset y mostramos éxito
                unset($_SESSION['reset_user_id']);
                unset($_SESSION['reset_user_nombre']);
                $paso = 3;
            } else {
                $error = "Error al actualizar la base de datos.";
                $paso = 2;
            }
        }
    } else {
        // Si intenta saltarse el paso 1
        header("Location: olvidepassword.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="css/iniciarsesion.css">
    
    <style>
        /* Pequeños ajustes específicos para esta página */
        .alert-error {
            background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;
            padding: 15px; border-radius: 8px; margin-bottom: 20px; width: 100%; text-align: center;
        }
        .success-box {
            text-align: center; padding: 40px;
        }
        .success-icon {
            font-size: 60px; color: #28a745; margin-bottom: 20px;
        }
        .info-text {
            text-align: center; color: #666; margin-bottom: 20px; font-size: 15px;
        }
    </style>
</head>
<body>

    <div class="visitante-login">
        <?php if(function_exists('sectionheader')) sectionheader(5); ?>

        <div class="login-section">
            <div class="login-card">
                
                <?php if ($paso == 1): ?>
                    <h1 class="login-title">Recuperar Acceso</h1>
                    <p class="info-text">Por seguridad, verifica tu identidad introduciendo los datos asociados a tu cuenta.</p>

                    <?php if($error): ?>
                        <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" class="login-form">
                        <div class="form-row">
                            <label class="label-icon"><i class="far fa-envelope"></i> Correo</label>
                            <input type="email" name="email" class="form-input" required placeholder="ejemplo@email.com">
                        </div>

                        <div class="form-row">
                            <label class="label-icon"><i class="far fa-id-card"></i> DNI/NIE</label>
                            <input type="text" name="dni" class="form-input" required placeholder="12345678Z">
                        </div>

                        <div class="form-row">
                            <label class="label-icon"><i class="fas fa-phone"></i> Teléfono</label>
                            <input type="tel" name="telefono" class="form-input" required placeholder="600000000">
                        </div>

                        <button type="submit" name="verificar_identidad" class="btn-login-submit">Verificar</button>
                        
                        <div class="register-text" style="margin-top: 20px;">
                            <a href="iniciarsesion.php">Volver a <em>Iniciar Sesión</em></a>
                        </div>
                    </form>

                <?php elseif ($paso == 2): ?>
                    <h1 class="login-title">Hola, <?php echo htmlspecialchars($_SESSION['reset_user_nombre']); ?></h1>
                    <p class="info-text">Identidad verificada. Establece tu nueva contraseña.</p>

                    <?php if($error): ?>
                        <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" class="login-form">
                        <div class="form-row">
                            <label class="label-icon"><i class="fas fa-lock"></i> Nueva</label>
                            <div style="position: relative; width: 100%;">
                                <input type="password" id="p1" name="pass_nueva" class="form-input" required placeholder="Mínimo 8 caracteres" style="padding-right: 40px;">
                                <i class="fas fa-eye" onclick="togglePass('p1')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #293661;"></i>
                            </div>
                        </div>

                        <div class="form-row">
                            <label class="label-icon"><i class="fas fa-lock"></i> Repetir</label>
                            <div style="position: relative; width: 100%;">
                                <input type="password" id="p2" name="pass_confirm" class="form-input" required placeholder="Confirma la contraseña" style="padding-right: 40px;">
                                <i class="fas fa-eye" onclick="togglePass('p2')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #293661;"></i>
                            </div>
                        </div>

                        <button type="submit" name="guardar_password" class="btn-login-submit">Guardar Nueva Contraseña</button>
                    </form>

                <?php elseif ($paso == 3): ?>
                    <div class="success-box">
                        <i class="fas fa-check-circle success-icon"></i>
                        <h2 style="color: #293661; margin-bottom: 15px;">¡Contraseña Actualizada!</h2>
                        <p style="color: #666; margin-bottom: 30px;">Ya puedes acceder a tu cuenta con la nueva clave.</p>
                        <a href="iniciarsesion.php" class="btn-login-submit" style="text-decoration: none; display: inline-block;">Ir a Iniciar Sesión</a>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <?php if(function_exists('sectionfooter')) sectionfooter(); ?>
    </div>

    <script>
        function togglePass(id) {
            var x = document.getElementById(id);
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>
</body>
</html>