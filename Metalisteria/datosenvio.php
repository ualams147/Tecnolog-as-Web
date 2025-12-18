<?php
// 1. CARGA DE RECURSOS Y SESIÓN
// Iniciamos sesión antes que nada para poder verificarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'CabeceraFooter.php'; 
include 'conexion.php'; 

// --- SEGURIDAD: VERIFICAR CARRITO ---
// Si no hay productos, no tiene sentido estar aquí -> Al carrito
if (empty($_SESSION['carrito'])) {
    header("Location: carrito.php");
    exit;
}

// --- SEGURIDAD: TOKEN CSRF ---
// Generamos un token único para este formulario
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 2. RECUPERAR DATOS DEL CLIENTE (Si está logueado)
$datos_cliente = [
    'nombre_completo' => '',
    'email' => '',
    'telefono' => '',
    'direccion' => '',
    'numero' => '',
    'piso' => '',
    'codigo_postal' => '',
    'ciudad' => 'Granada' 
];

if (isset($_SESSION['usuario_id'])) { 
    $uid = $_SESSION['usuario_id'];
    
    try {
        $stmt = $conn->prepare("SELECT nombre, apellidos, email, telefono, direccion, numero, piso, codigo_postal, ciudad FROM clientes WHERE id = ?");
        $stmt->execute([$uid]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($res) {
            $datos_cliente['nombre_completo'] = trim($res['nombre'] . ' ' . $res['apellidos']);
            $datos_cliente['email'] = $res['email'];
            $datos_cliente['telefono'] = $res['telefono'];
            $datos_cliente['direccion'] = $res['direccion']; 
            $datos_cliente['numero'] = $res['numero'];
            $datos_cliente['piso'] = $res['piso'];
            $datos_cliente['codigo_postal'] = $res['codigo_postal'];
            if (!empty($res['ciudad'])) {
                $datos_cliente['ciudad'] = $res['ciudad'];
            }
        }
    } catch (Exception $e) {
        // Fallo silencioso, formulario vacío
    }
} else {
    // SI LA COMPRA REQUIERE REGISTRO OBLIGATORIO, DESCOMENTA ESTO:
    /*
    header("Location: iniciarsesion.php?origen=compra");
    exit;
    */
}

// Función auxiliar para limpiar salida HTML (Seguridad XSS)
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['idioma']) ? $_SESSION['idioma'] : 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($lang['envio_titulo_pag']) ? $lang['envio_titulo_pag'] : 'Datos de Envío'; ?></title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/datosEnvio.css">
</head>
<body>
    <div class="visitante-envio">
        
        <?php if(function_exists('sectionheader')) sectionheader(); ?>

        <section class="steps-section">
            <div class="container">
                <div class="steps-container">
                    <div class="step active">
                        <span class="step-number"><?php echo isset($lang['envio_paso']) ? $lang['envio_paso'] : 'Paso'; ?> 1</span>
                        <span class="step-label"><?php echo isset($lang['envio_step_1']) ? $lang['envio_step_1'] : 'Envío'; ?></span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step">
                        <span class="step-number"><?php echo isset($lang['envio_paso']) ? $lang['envio_paso'] : 'Paso'; ?> 2</span>
                        <span class="step-label"><?php echo isset($lang['envio_step_2']) ? $lang['envio_step_2'] : 'Pago'; ?></span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step">
                        <span class="step-number"><?php echo isset($lang['envio_paso']) ? $lang['envio_paso'] : 'Paso'; ?> 3</span>
                        <span class="step-label"><?php echo isset($lang['envio_step_3']) ? $lang['envio_step_3'] : 'Confirmación'; ?></span>
                    </div>
                </div>
            </div>
        </section>

        <main class="envio-main container">
            
            <div class="envio-card">
                <h1 class="page-title"><?php echo isset($lang['envio_h1']) ? $lang['envio_h1'] : 'Datos de Envío'; ?></h1>

                <form class="envio-form" action="metodopago.php" method="POST">
                    
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="form-row">
                        <label for="nombre"><?php echo isset($lang['envio_lbl_nombre']) ? $lang['envio_lbl_nombre'] : 'Nombre Completo'; ?></label>
                        <input type="text" id="nombre" name="nombre" 
                            value="<?php echo e($datos_cliente['nombre_completo']); ?>" 
                            pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" 
                            title="<?php echo isset($lang['envio_title_nombre']) ? $lang['envio_title_nombre'] : 'Solo letras y espacios'; ?>"
                            required>
                    </div>

                    <div class="form-row">
                        <label for="email"><?php echo isset($lang['envio_lbl_email']) ? $lang['envio_lbl_email'] : 'Correo Electrónico'; ?></label>
                        <input type="email" id="email" name="email" 
                            value="<?php echo e($datos_cliente['email']); ?>" 
                            required>
                    </div>

                    <div class="form-row">
                        <label for="telefono"><?php echo isset($lang['envio_lbl_telefono']) ? $lang['envio_lbl_telefono'] : 'Teléfono'; ?></label>
                        <input type="tel" id="telefono" name="telefono" 
                            value="<?php echo e($datos_cliente['telefono']); ?>" 
                            pattern="[0-9\-\s]{7,15}"
                            title="<?php echo isset($lang['envio_title_telefono']) ? $lang['envio_title_telefono'] : 'Solo números'; ?>"
                            required>
                    </div>

                    <div class="form-row">
                        <label for="calle"><?php echo isset($lang['envio_lbl_calle']) ? $lang['envio_lbl_calle'] : 'Calle'; ?></label>
                        <input type="text" id="calle" name="calle" 
                            value="<?php echo e($datos_cliente['direccion']); ?>" 
                            placeholder="<?php echo isset($lang['envio_ph_calle']) ? $lang['envio_ph_calle'] : 'Ej: Calle Real'; ?>" required>
                    </div>

                    <div class="form-row">
                        <label for="numero"><?php echo isset($lang['envio_lbl_num']) ? $lang['envio_lbl_num'] : 'Número'; ?></label>
                        <div class="input-group">
                            <input type="text" id="numero" name="numero" 
                                value="<?php echo e($datos_cliente['numero']); ?>" 
                                placeholder="<?php echo isset($lang['envio_ph_num']) ? $lang['envio_ph_num'] : 'Nº'; ?>" class="input-small" required>
                            
                            <input type="text" id="piso" name="piso" 
                                value="<?php echo e($datos_cliente['piso']); ?>" 
                                placeholder="<?php echo isset($lang['envio_ph_piso']) ? $lang['envio_ph_piso'] : 'Piso/Puerta'; ?>" class="input-large">
                        </div>
                    </div>

                    <div class="form-row">
                        <label for="cp"><?php echo isset($lang['envio_lbl_cp']) ? $lang['envio_lbl_cp'] : 'Código Postal'; ?></label>
                        <div class="input-group">
                            <input type="text" id="cp" name="cp" 
                                value="<?php echo e($datos_cliente['codigo_postal']); ?>" 
                                pattern="[0-9]{4,5}"
                                title="<?php echo isset($lang['envio_title_cp']) ? $lang['envio_title_cp'] : '5 dígitos'; ?>"
                                placeholder="<?php echo isset($lang['envio_ph_cp']) ? $lang['envio_ph_cp'] : 'CP'; ?>" class="input-small" required>
                            
                            <input type="text" id="localidad" name="localidad" 
                                value="<?php echo e($datos_cliente['ciudad']); ?>" 
                                class="input-large" required>
                        </div>
                    </div>

                    <div class="form-row notes-row">
                        <label for="notas"><?php echo isset($lang['envio_lbl_notas']) ? $lang['envio_lbl_notas'] : 'Notas adicionales'; ?></label>
                        <textarea id="notas" name="notas" placeholder="<?php echo isset($lang['envio_ph_notas']) ? $lang['envio_ph_notas'] : 'Ej: Dejar en portería...'; ?>"></textarea>
                    </div>

                    <div class="form-actions">
                        <a href="carrito.php" class="btn-action btn-back">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                            </svg>
                            <?php echo isset($lang['envio_btn_volver']) ? $lang['envio_btn_volver'] : 'Volver'; ?>
                        </a>

                        <button type="submit" class="btn-action btn-next">
                            <?php echo isset($lang['envio_btn_continuar']) ? $lang['envio_btn_continuar'] : 'Continuar al Pago'; ?>
                        </button>
                    </div>

                </form>
            </div>
        </main>

        <?php if(function_exists('sectionfooter')) sectionfooter(); ?>
    </div>
</body>
</html>