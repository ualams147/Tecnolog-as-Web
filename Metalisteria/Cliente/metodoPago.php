<?php
// 1. PRIMERO LAS FUNCIONES (Inicia la sesión y carga los estilos)
include '../CabeceraFooter.php'; 

// 2. LUEGO LA CONEXIÓN (Por si necesitas guardar el pedido más tarde)
include '../conexion.php';

// =======================================================================
// CALCULAR EL TOTAL REAL DEL CARRITO
// =======================================================================
$total_a_pagar = 0;

if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        // Sumamos: Precio x Cantidad
        $total_a_pagar += $item['precio'] * $item['cantidad'];
    }
} else {
    // Si el carrito está vacío por error, redirigimos al carrito
    header("Location: carrito.php");
    exit;
}

// Guardamos este total en la sesión
$_SESSION['total_carrito'] = $total_a_pagar;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Método de Pago - Metalistería Fulsan</title>
    
    <link rel="stylesheet" href="../css/datosEnvio.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    
    <style>
        /* Estilos de las opciones de pago y overlay */
        .payment-selection { margin-bottom: 30px; display: flex; flex-direction: column; gap: 15px; }
        .payment-option { display: flex; flex-direction: column; padding: 20px; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; background: white; }
        .payment-option:hover { border-color: #999; }
        .payment-option.selected { border-color: #293661; background-color: #f0f4ff; }
        
        /* Contenedor interno para alinear radio + texto */
        .option-header { display: flex; align-items: center; gap: 15px; width: 100%; }
        
        .option-content { display: flex; align-items: center; justify-content: space-between; width: 100%; }
        .option-title { font-weight: 700; font-size: 18px; color: #2b2b2b; }
        .option-desc { font-size: 14px; color: #666; display: block; margin-top: 4px; }
        .card-icons { font-size: 24px; letter-spacing: 5px; }
        input[type="radio"] { transform: scale(1.5); accent-color: #293661; }

        /* Pantalla de Carga (Overlay) */
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
                    <div class="step"><span class="step-number">Paso 1</span><span class="step-label">Datos de envío</span></div>
                    <div class="step-line"></div>
                    <div class="step active"><span class="step-number">Paso 2</span><span class="step-label">Método de Pago</span></div>
                    <div class="step-line"></div>
                    <div class="step"><span class="step-number">Paso 3</span><span class="step-label">Factura de Compra</span></div>
                </div>
            </div>
        </section>

        <main class="envio-main container">
            <div class="envio-card">
                <h1 class="page-title">Método de Pago</h1>
                
                <p style="margin-bottom: 25px; text-align: center; font-size: 18px;">
                    Total a pagar: <strong><?php echo number_format($total_a_pagar, 2); ?> €</strong>
                </p>

                <form method="POST" id="paymentForm">
                    <div class="payment-selection">
                        
                        <label class="payment-option selected" onclick="selectOption(this)">
                            <div class="option-header">
                                <input type="radio" name="metodo_pago" value="stripe" checked>
                                <div class="option-content">
                                    <div>
                                        <span class="option-title">Tarjeta de Crédito / Débito</span>
                                        <span class="option-desc">Plataforma segura Stripe (Visa, MC, Amex)</span>
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
                                        <span class="option-title">Bizum</span>
                                        <span class="option-desc">Pago rápido y seguro desde tu móvil</span>
                                    </div>
                                    <div style="font-weight:800; color:#00bfd3;">bizum</div>
                                </div>
                            </div>

                            <div id="bizum-input-container" class="hidden" style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px; padding-left: 30px;">
                                <label style="display:block; font-size: 14px; margin-bottom: 5px; font-weight:600;">Introduce tu nº de móvil:</label>
                                <input type="tel" id="telefono_bizum" name="telefono_bizum" placeholder="Ej: 600 123 456" 
                                       style="width: 100%; max-width: 300px; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px;"
                                       onclick="event.stopPropagation();"> </div>
                        </label>
                    </div>

                    <div class="form-actions">
                        <a href="datosEnvio.php" class="btn-action btn-back">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                            Volver
                        </a>
                        <button type="submit" class="btn-action btn-next">Pagar Ahora</button>
                    </div>
                </form>
            </div>
        </main>
        
        <?php sectionfooter(); ?>
    </div>
    
    <div id="payment-overlay" class="hidden">
        <div class="spinner"></div>
        <div class="processing-text">Procesando...</div>
        <div class="processing-subtext">Por favor espere</div>
    </div>
    
    <script src="../js/auth.js"></script>

    <script>
        function selectOption(label) {
            // 1. Quitamos la clase 'selected' de todos
            document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
            
            // 2. Marcamos el seleccionado
            label.classList.add('selected');
            const radio = label.querySelector('input[type="radio"]');
            radio.checked = true;

            // 3. LOGICA PARA MOSTRAR/OCULTAR CAMPO BIZUM
            const bizumContainer = document.getElementById('bizum-input-container');
            
            if (radio.value === 'bizum') {
                bizumContainer.classList.remove('hidden');
                // Hacemos foco en el input con un pequeño retraso
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
                // Opción 1: STRIPE
                form.action = "procesar_pago_stripe.php";
                overlay.querySelector('.processing-text').innerText = "Conectando con Stripe...";
                overlay.classList.remove('hidden');
                form.submit(); 

            } else {
                // Opción 2: BIZUM
                const movil = document.getElementById('telefono_bizum').value.trim();

                // Validación simple: que haya escrito algo decente (al menos 9 dígitos)
                if(movil.length < 9) {
                    alert("⚠️ Por favor, introduce un número de móvil válido para Bizum.");
                    // Volvemos a mostrar el campo por si acaso
                    document.getElementById('bizum-input-container').classList.remove('hidden');
                    return; // Paramos aquí, no seguimos
                }

                overlay.querySelector('.processing-text').innerText = "Conectando con Bizum...";
                overlay.classList.remove('hidden');
                
                setTimeout(function(){
                    // Enviamos el móvil por la URL para que fake_bizum.php lo pueda leer
                    window.location.href = "fake_bizum.php?movil=" + encodeURIComponent(movil);
                }, 1500);
            }
        });
    </script>
</body>
</html>