<?php
// 1. CARGA DE RECURSOS Y SESIÓN
include 'CabeceraFooter.php'; 
include 'conexion.php'; // Asegúrate de incluir la conexión

// 2. RECUPERAR DATOS DEL CLIENTE (Si está logueado)
// Inicializamos variables vacías por seguridad
$datos_cliente = [
    'nombre_completo' => '',
    'email' => '',
    'telefono' => '',
    'direccion' => '',
    'numero' => '',
    'piso' => '',
    'codigo_postal' => '',
    'ciudad' => 'Granada' // Valor por defecto según tu diseño anterior
];

// Verificamos si hay usuario en sesión
if (isset($_SESSION['usuario_id'])) { // O $_SESSION['usuario']['id'] según tu sistema
    $uid = $_SESSION['usuario_id'];
    
    try {
        // Consultamos la tabla 'clientes'
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
        // En caso de error, simplemente no rellenamos los datos
    }
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
    <title><?php echo $lang['envio_titulo_pag']; ?></title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/datosEnvio.css">
</head>
<body>
    <div class="visitante-envio">
        
        <?php sectionheader(); ?>

        <section class="steps-section">
            <div class="container">
                <div class="steps-container">
                    <div class="step active">
                        <span class="step-number"><?php echo $lang['envio_paso']; ?> 1</span>
                        <span class="step-label"><?php echo $lang['envio_step_1']; ?></span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step">
                        <span class="step-number"><?php echo $lang['envio_paso']; ?> 2</span>
                        <span class="step-label"><?php echo $lang['envio_step_2']; ?></span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step">
                        <span class="step-number"><?php echo $lang['envio_paso']; ?> 3</span>
                        <span class="step-label"><?php echo $lang['envio_step_3']; ?></span>
                    </div>
                </div>
            </div>
        </section>

        <main class="envio-main container">
            
            <div class="envio-card">
                <h1 class="page-title"><?php echo $lang['envio_h1']; ?></h1>

                <form class="envio-form" action="metodopago.php" method="POST">
                    
                    <div class="form-row">
                        <label for="nombre"><?php echo $lang['envio_lbl_nombre']; ?></label>
                        <input type="text" id="nombre" name="nombre" 
                            value="<?php echo e($datos_cliente['nombre_completo']); ?>" 
                            pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" 
                            title="<?php echo $lang['envio_title_nombre']; ?>"
                            required>
                    </div>

                    <div class="form-row">
                        <label for="email"><?php echo $lang['envio_lbl_email']; ?></label>
                        <input type="email" id="email" name="email" 
                            value="<?php echo e($datos_cliente['email']); ?>" 
                            required>
                    </div>

                    <div class="form-row">
                        <label for="telefono"><?php echo $lang['envio_lbl_telefono']; ?></label>
                        <input type="tel" id="telefono" name="telefono" 
                            value="<?php echo e($datos_cliente['telefono']); ?>" 
                            pattern="[0-9\-\s]{7,15}"
                            title="<?php echo $lang['envio_title_telefono']; ?>"
                            required>
                    </div>

                    <div class="form-row">
                        <label for="calle"><?php echo $lang['envio_lbl_calle']; ?></label>
                        <input type="text" id="calle" name="calle" 
                            value="<?php echo e($datos_cliente['direccion']); ?>" 
                            placeholder="<?php echo $lang['envio_ph_calle']; ?>" required>
                    </div>

                    <div class="form-row">
                        <label for="numero"><?php echo $lang['envio_lbl_num']; ?></label>
                        <div class="input-group">
                            <input type="text" id="numero" name="numero" 
                                value="<?php echo e($datos_cliente['numero']); ?>" 
                                placeholder="<?php echo $lang['envio_ph_num']; ?>" class="input-small" required>
                            
                            <input type="text" id="piso" name="piso" 
                                value="<?php echo e($datos_cliente['piso']); ?>" 
                                placeholder="<?php echo $lang['envio_ph_piso']; ?>" class="input-large">
                        </div>
                    </div>

                    <div class="form-row">
                        <label for="cp"><?php echo $lang['envio_lbl_cp']; ?></label>
                        <div class="input-group">
                            <input type="text" id="cp" name="cp" 
                                value="<?php echo e($datos_cliente['codigo_postal']); ?>" 
                                pattern="[0-9]{4,5}"
                                title="<?php echo $lang['envio_title_cp']; ?>"
                                placeholder="<?php echo $lang['envio_ph_cp']; ?>" class="input-small" required>
                            
                            <input type="text" id="localidad" name="localidad" 
                                value="<?php echo e($datos_cliente['ciudad']); ?>" 
                                class="input-large" required>
                        </div>
                    </div>

                    <div class="form-row notes-row">
                        <label for="notas"><?php echo $lang['envio_lbl_notas']; ?></label>
                        <textarea id="notas" name="notas" placeholder="<?php echo $lang['envio_ph_notas']; ?>"></textarea>
                    </div>

                    <div class="form-actions">
                        <a href="carrito.php" class="btn-action btn-back">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                            </svg>
                            <?php echo $lang['envio_btn_volver']; ?>
                        </a>

                        <button type="submit" class="btn-action btn-next">
                            <?php echo $lang['envio_btn_continuar']; ?>
                        </button>
                    </div>

                </form>
            </div>
        </main>

        <?php sectionfooter(); ?>
    </div>
    <script src="js/auth.js"></script>
</body>
</html>