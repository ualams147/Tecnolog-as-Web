<?php
// 1. CARGAMOS LA CABECERA Y FUNCIONES COMUNES
include '../CabeceraFooter.php'; 

// 2. CONEXIÓN A BASE DE DATOS
include '../conexion.php';

// ====================================================
// 3. RECUPERAR DATOS DEL PEDIDO (ANTES DE BORRARLOS)
// ====================================================

// Verificamos si hay sesión activa (por si CabeceraFooter no la inició)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$productos_compra = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$total_pagado = isset($_SESSION['total_carrito']) ? $_SESSION['total_carrito'] : 0;
$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0;

// DATOS DE SEGURIDAD: Si se recarga la página y el carrito ya se borró
if (empty($productos_compra)) {
    // Datos ficticios para que la página no se vea rota si el usuario recarga
    $productos_compra[] = [
        'nombre' => 'Pedido ya procesado (Recarga de página)',
        'cantidad' => 1,
        'precio' => $total_pagado // Mantiene el total visual
    ];
}

// RECUPERAR DATOS DEL CLIENTE DE LA BD
$cliente = ['nombre' => 'Cliente Invitado', 'direccion' => 'Recogida en tienda']; 

if ($usuario_id > 0 && isset($conn)) {
    try {
        $stmt = $conn->prepare("SELECT nombre, email, direccion FROM clientes WHERE id = ?");
        $stmt->execute([$usuario_id]);
        $datos_bd = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($datos_bd) {
            $cliente = $datos_bd;
        }
    } catch (Exception $e) {
        // Fallo silencioso si la BD da error
    }
}

// Generar número de pedido y fecha
$numero_pedido = "FUL-" . date('Ymd') . "-" . rand(100, 999);
$fecha_pedido = date('d/m/Y H:i');

// ====================================================
// 4. LIMPIEZA DE SESIÓN (EL PEDIDO SE CONSIDERA FINALIZADO)
// ====================================================
if(isset($_SESSION['carrito'])) {
    unset($_SESSION['carrito']);
    unset($_SESSION['total_carrito']);
    // No borramos 'usuario_id' para que siga logueado
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura - Metalistería Fulsan</title>
    
    <link rel="stylesheet" href="../css/datosEnvio.css">
    <link rel="icon" type="image/png" href="../imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    
    <style>
        /* Estilos generales de la factura en pantalla */
        .factura-container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            border: 1px solid #e0e0e0;
        }
        .success-header { text-align: center; margin-bottom: 30px; }
        .success-icon { font-size: 50px; display: block; margin-bottom: 10px; }
        
        .btn-imprimir {
            background-color: #293661;
            color: white;
            padding: 12px 30px; 
            border-radius: 50px; 
            text-decoration: none;
            margin-right: 15px;
            transition: background 0.3s;
            border: none;
            font-size: 16px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-imprimir:hover { background-color: #1a2442; }

        .btn-inicio {
            background-color: #f3f3f3;
            color: #333;
            padding: 12px 30px; 
            border-radius: 50px; 
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
        }
        .btn-inicio:hover { background-color: #e0e0e0; }

        /* =========================================
           ESTILOS DE IMPRESIÓN (PDF)
           ========================================= */
        @media print {
            /* Ocultar todo lo que no sea la factura */
            body * { visibility: hidden; }
            
            /* Hacer visible solo el contenedor de la factura */
            .factura-container, .factura-container * { visibility: visible; }
            
            /* Posicionar la factura arriba a la izquierda en el papel */
            .factura-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 20px;
                box-shadow: none;
                border: none;
            }

            /* Ocultar botones y navegación al imprimir */
            .no-print, .steps-section, header, footer, .visitante-conocenos > header, .visitante-conocenos > footer {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="visitante-conocenos">
        
        <?php sectionheader(); ?>

        <section class="steps-section no-print">
            <div class="container">
                <div class="steps-container">
                    <div class="step"><span class="step-number">Paso 1</span><span class="step-label">Datos de envío</span></div>
                    <div class="step-line"></div>
                    <div class="step"><span class="step-number">Paso 2</span><span class="step-label">Método de Pago</span></div>
                    <div class="step-line"></div>
                    <div class="step active"><span class="step-number">Paso 3</span><span class="step-label">Factura</span></div>
                </div>
            </div>
        </section>

        <main class="envio-main container">
            
            <div class="factura-container">
                
                <div class="success-header no-print">
                    <span class="success-icon">✅</span>
                    <h1 style="color: #293661; margin: 0;">¡Gracias por tu compra!</h1>
                    <p style="color: #666;">Tu pedido se ha procesado correctamente.</p>
                </div>

                <div style="border-bottom: 2px solid #293661; padding-bottom: 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h2 style="color: #293661; margin: 0; font-family: 'Poppins', sans-serif;">METALISTERÍA FULSAN</h2>
                        <p style="margin: 5px 0; font-size: 14px; color: #666; font-family: 'Source Sans Pro', sans-serif;">
                            Cortijo la Purisima, 2P<br>
                            18004 Granada<br>
                            Tlf: 652 921 960
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <h3 style="margin: 0; color: #28a745; font-family: 'Poppins', sans-serif;">FACTURA / RECIBO</h3>
                        <p style="margin: 5px 0; font-family: 'Source Sans Pro', sans-serif;"><strong>Nº Pedido:</strong> <?php echo $numero_pedido; ?></p>
                        <p style="margin: 0; font-family: 'Source Sans Pro', sans-serif;"><strong>Fecha:</strong> <?php echo $fecha_pedido; ?></p>
                    </div>
                </div>

                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-family: 'Source Sans Pro', sans-serif;">
                    <strong style="color: #293661;">Datos del Cliente:</strong><br>
                    <span style="font-size: 1.1em;"><?php echo htmlspecialchars($cliente['nombre']); ?></span><br>
                    <span style="color: #555;"><?php echo htmlspecialchars($cliente['direccion']); ?></span>
                </div>

                <h3 style="color: #293661; font-family: 'Poppins', sans-serif; font-size: 18px;">Resumen de Compra</h3>
                
                <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-family: 'Source Sans Pro', sans-serif;">
                    <thead>
                        <tr style="background: #293661; color: white;">
                            <th style="padding: 12px; text-align: left; border-radius: 6px 0 0 6px;">Producto</th>
                            <th style="padding: 12px; text-align: center;">Cant.</th>
                            <th style="padding: 12px; text-align: right;">Precio</th>
                            <th style="padding: 12px; text-align: right; border-radius: 0 6px 6px 0;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos_compra as $item): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px; color: #333;">
                                <?php echo htmlspecialchars($item['nombre']); ?>
                            </td>
                            <td style="padding: 12px; text-align: center; color: #666;">
                                <?php echo $item['cantidad']; ?>
                            </td>
                            <td style="padding: 12px; text-align: right; color: #666;">
                                <?php echo number_format($item['precio'], 2); ?> €
                            </td>
                            <td style="padding: 12px; text-align: right; font-weight: bold; color: #333;">
                                <?php echo number_format($item['precio'] * $item['cantidad'], 2); ?> €
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="text-align: right; margin-top: 20px; font-family: 'Poppins', sans-serif;">
                    <span style="font-size: 16px; color: #666; margin-right: 10px;">Total Pagado:</span>
                    <span style="font-size: 24px; font-weight: 700; color: #293661;"><?php echo number_format($total_pagado, 2); ?> €</span>
                </div>

                <div class="no-print" style="text-align: center; margin-top: 40px; border-top: 1px solid #eee; padding-top: 30px;">
                    <button onclick="window.print();" class="btn-imprimir">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        Descargar / Imprimir PDF
                    </button>

                    <a href="index.php" class="btn-inicio">Volver al Inicio</a>
                </div>

            </div>
        </main>
        
        <?php sectionfooter(); ?>
    </div>
    
    <script src="../js/auth.js"></script>
</body>
</html>