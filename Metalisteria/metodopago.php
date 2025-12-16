<?php
// 1. INICIAR SESIÓN PRIMERO (Siempre primera línea)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. GUARDAR DATOS DEL FORMULARIO ANTERIOR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    $_SESSION['datos_envio'] = $_POST;
}

// 3. AHORA SÍ, INCLUIR EL RESTO (Aquí se carga el idioma)
include 'CabeceraFooter.php'; 
include 'conexion.php';

// =======================================================================
// CALCULAR EL TOTAL REAL DEL CARRITO
// =======================================================================
$total_a_pagar = 0;

if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $total_a_pagar += $item['precio'] * $item['cantidad'];
    }
} else {
    header("Location: carrito.php");
    exit;
}

$_SESSION['total_carrito'] = $total_a_pagar;

// --- NUEVO: OBTENER TELÉFONO POR DEFECTO PARA BIZUM ---
$telefono_defecto = "";
if (isset($_SESSION['datos_envio']['telefono'])) {
    $telefono_defecto = $_SESSION['datos_envio']['telefono'];
} elseif (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] > 0) {
    try {
        $stmt = $conn->prepare("SELECT telefono FROM clientes WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res) $telefono_defecto = $res['telefono'];
    } catch(Exception $e) {}
}
?>

<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['idioma']) ? $_SESSION['idioma'] : 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['pago_titulo_pag']; ?></title>
    
    <link rel="stylesheet" href="css/datosEnvio.css">
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    
    <style>
        /* Estilos de las opciones de pago y overlay */
        .payment-selection { margin-bottom: 30px; display: flex; flex-direction: column; gap: 15px; }
        .payment-option { display: flex; flex-direction: column; padding: 20px; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; background: white; }
        .payment-option:hover { border-color: #999; }
        .payment-option.selected { border-color: #293661; background-color: #f0f4ff; }
        
        .option-header { display: flex; align-items: center; gap: 15px; width: 100%; }
        .option-content { display: flex; align-items: center; justify-content: space-between; width: 100%; }
        .option-title { font-weight: 700; font-size: 18px; color: #2b2b2b; }
        .option-desc { font-size: 14px; color: #666; display: block; margin-top: 4px; }
        .card-icons { font-size: 24px; letter-spacing: 5px; }
        input[type="radio"] { transform: scale(1.5); accent-color: #293661; }

        #payment-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.95); z-index: 9999; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; }
        .spinner { width: 60px; height: 60px; border: 6px solid #f3f3f3; border-top: 6px solid #293661; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 20px; }
        .processing-text { font-family: 'Poppins', sans-serif; font-size: 22px; font-weight: 600; color: #2b2b2b; margin-bottom: 10px; }
        .processing-subtext { font-family: 'Source Sans Pro', sans-serif; font-size: 16px; color: #666; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        .hidden { display: none !important; }
    </style>
</head>
<body>
    <div class="visitante-conocenos">
        
        <?php sectionheader(); ?>

        <section class="steps-section">
            <div class="container">
                <div class="steps-container">
                    <div class="step"><span class="step-number"><?php echo $lang['envio_paso']; ?> 1</span><span class="step-label"><?php echo $lang['envio_step_1']; ?></span></div>
                    <div class="step-line"></div>
                    <div class="step active"><span class="step-number"><?php echo $lang['envio_paso']; ?> 2</span><span class="step-label"><?php echo $lang['envio_step_2']; ?></span></div>
                    <div class="step-line"></div>
                    <div class="step"><span class="step-number"><?php echo $lang['envio_paso']; ?> 3</span><span class="step-label"><?php echo $lang['envio_step_3']; ?></span></div>
                </div>
            </div>
        </section>

        <main class="envio-main container">
            <div class="envio-card">
                <h1 class="page-title"><?php echo $lang['pago_h1']; ?></h1>
                
                <p style="margin-bottom: 25px; text-align: center; font-size: 18px;">
                    <?php echo $lang['pago_total_pagar']; ?> <strong><?php echo number_format($total_a_pagar, 2); ?> €</strong>
                </p>

                <form method="POST" id="paymentForm">
                    <div class="payment-selection">
                        
                        <label class="payment-option selected" onclick="selectOption(this)">
                            <div class="option-header">
                                <input type="radio" name="metodo_pago" value="stripe" checked>
                                <div class="option-content">
                                    <div>
                                        <span class="option-title"><?php echo $lang['pago_tarjeta_tit']; ?></span>
                                        <span class="option-desc"><?php echo $lang['pago_tarjeta_desc']; ?></span>
                                    </div>
                                    <div class="card-icons">💳 🛡️</div>
                                </div>
                            </div>
                        </label>

                        <label class="payment-option" onclick="selectOption(this)">
                            <div class="option-header">
                                <input type="radio" name="metodo_pago" value="bizum">
                                <div class="option-content">
                                    <div>
                                        <span class="option-title"><?php echo $lang['pago_bizum_tit']; ?></span>
                                        <span class="option-desc"><?php echo $lang['pago_bizum_desc']; ?></span>
                                    </div>
                                    <div style="font-weight:800; color:#00bfd3;">bizum</div>
                                </div>
                            </div>

                            <div id="bizum-input-container" class="hidden" style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px; padding-left: 30px;">
                                <label style="display:block; font-size: 14px; margin-bottom: 5px; font-weight:600;"><?php echo $lang['pago_bizum_lbl_movil']; ?></label>
                                
                                <input type="tel" id="telefono_bizum" name="telefono_bizum" 
                                       placeholder="<?php echo $lang['pago_bizum_ph_movil']; ?>" 
                                       value="<?php echo htmlspecialchars($telefono_defecto); ?>"
                                       style="width: 100%; max-width: 300px; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px;"
                                       onclick="event.stopPropagation();"> 
                            </div>
                        </label>
                    </div>

                    <div class="form-actions">
                        <a href="datosenvio.php" class="btn-action btn-back">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                            <?php echo $lang['pago_btn_volver']; ?>
                        </a>
                        <button type="submit" class="btn-action btn-next"><?php echo $lang['pago_btn_pagar']; ?></button>
                    </div>
                </form>
            </div>
        </main>
        
        <?php sectionfooter(); ?>
    </div>
    
    <div id="payment-overlay" class="hidden">
        <div class="spinner"></div>
        <div class="processing-text"><?php echo $lang['pago_overlay_procesando']; ?></div>
        <div class="processing-subtext"><?php echo $lang['pago_overlay_espere']; ?></div>
    </div>
    
    <script src="js/auth.js"></script>

    <script>
        function selectOption(label) {
            document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
            label.classList.add('selected');
            const radio = label.querySelector('input[type="radio"]');
            radio.checked = true;

            const bizumContainer = document.getElementById('bizum-input-container');
            
            if (radio.value === 'bizum') {
                bizumContainer.classList.remove('hidden');
                setTimeout(() => {
                    const input = document.getElementById('telefono_bizum');
                    if(input) input.focus();
                }, 100);
            } else {
                bizumContainer.classList.add('hidden');
            }
        }

        const form = document.getElementById('paymentForm');
        const overlay = document.getElementById('payment-overlay');

        form.addEventListener('submit', function(e) {
            e.preventDefault(); 
            
            const metodo = document.querySelector('input[name="metodo_pago"]:checked').value;

            if (metodo === 'stripe') {
                form.action = "procesar_pago_stripe.php";
                overlay.querySelector('.processing-text').innerText = "<?php echo $lang['pago_overlay_stripe']; ?>";
                overlay.classList.remove('hidden');
                form.submit(); 

            } else {
                const movil = document.getElementById('telefono_bizum').value.trim();

                if(movil.length < 9) {
                    alert("<?php echo $lang['pago_alert_bizum_movil']; ?>");
                    document.getElementById('bizum-input-container').classList.remove('hidden');
                    return; 
                }

                overlay.querySelector('.processing-text').innerText = "<?php echo $lang['pago_overlay_bizum']; ?>";
                overlay.classList.remove('hidden');
                
                setTimeout(function(){
                    window.location.href = "fake_bizum.php?movil=" + encodeURIComponent(movil);
                }, 1500);
            }
        });
    </script>
</body>
</html>