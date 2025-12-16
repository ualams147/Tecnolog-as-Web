<?php
// 1. CARGAMOS LIBRERÍAS
require '../../../vendor/autoload.php'; 

use Dompdf\Dompdf;
use Dompdf\Options;

// --- [COMENTADO PARA HOST GRATUITO] ---
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;
// --------------------------------------
include '../CabeceraFooter.php';
session_start();

// 1. CONEXIÓN (Manejo de errores simple para evitar pantalla blanca)
if (file_exists('../conexion.php')) {
    include '../conexion.php';
} else {
    // Si no encuentra la conexión, definimos variables dummy para que no rompa la demo
    $cliente = ['nombre' => 'Cliente Demo', 'direccion' => 'Dirección Demo'];
}

// ====================================================
// 2. RECUPERAR DATOS
// ====================================================

// Recuperamos el carrito ANTES de borrarlo para mostrarlo
$productos_compra = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$total_pagado = isset($_SESSION['total_carrito']) ? $_SESSION['total_carrito'] : 0;
$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0;

// DATOS DE SEGURIDAD: Si no hay carrito y se recarga, evitar errores
if (empty($productos_compra)) {
    // Opcional: Redirigir al inicio si no hay compra
    // header("Location: index.php"); 
    // exit;
    
    // O mostramos datos ficticios para la visualización
    $productos_compra[] = [
        'nombre' => 'Pedido Procesado anteriormente',
        'cantidad' => 0,
        'precio' => 0.00
    ];
}

// Datos del cliente desde BD si existe conexión
if ($usuario_id > 0 && isset($conn)) {
    try {
        $stmt = $conn->prepare("SELECT nombre, email, direccion FROM clientes WHERE id = ?");
        $stmt->execute([$usuario_id]);
        $datos_bd = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($datos_bd) {
            $cliente = $datos_bd;
        }
    } catch (Exception $e) {
        // Fallo silencioso de BD
    }
} elseif (!isset($cliente)) {
    $cliente = ['nombre' => 'Invitado', 'direccion' => 'Recogida en tienda'];
}

$numero_pedido = "FUL-" . date('Ymd') . "-" . rand(100, 999);
$fecha_pedido = date('d/m/Y H:i');

// ====================================================
// 3. LIMPIEZA DE SESIÓN (COMPRA FINALIZADA)
// ====================================================
// Borramos el carrito de la sesión porque la compra ya "se hizo"
if(isset($_SESSION['carrito'])) {
    unset($_SESSION['carrito']);
    unset($_SESSION['total_carrito']);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Completado - Fulsan</title>
    <link rel="stylesheet" href="../css/datosEnvio.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        .factura-container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
        }
        .success-icon { font-size: 50px; color: #28a745; text-align: center; margin-bottom: 20px; }
        
        .btn-imprimir {
            background-color: #666;
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
        }
        .btn-imprimir:hover { background-color: #444; }

        /* ESTILOS ESPECÍFICOS PARA IMPRESIÓN */
        @media print {
            body * {
                visibility: hidden;
            }
            .factura-container, .factura-container * {
                visibility: visible;
            }
            .factura-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .cabecera, .footer, .steps-section {
                display: none;
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
                    <div class="step active"><span class="step-number">Paso 3</span><span class="step-label">Factura de Compra</span></div>
                </div>
            </div>
        </section>

        <main class="envio-main container">
            <div class="factura-container">
                
                <div style="border-bottom: 2px solid #293661; padding-bottom: 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h2 style="color: #293661; margin: 0;">METALISTERÍA FULSAN</h2>
                        <p style="margin: 5px 0; font-size: 14px; color: #666;">Cortijo la Purisima, 2P<br>18004 Granada<br>Tlf: 652 921 960</p>
                    </div>
                    <div style="text-align: right;">
                        <h3 style="margin: 0; color: #28a745;">PEDIDO CONFIRMADO</h3>
                        <p style="margin: 5px 0;"><strong>Nº:</strong> <?php echo $numero_pedido; ?></p>
                        <p style="margin: 0;"><strong>Fecha:</strong> <?php echo $fecha_pedido; ?></p>
                    </div>
                </div>

                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <strong>Datos del Cliente:</strong><br>
                    <?php echo htmlspecialchars($cliente['nombre']); ?><br>
                    <?php echo htmlspecialchars($cliente['direccion']); ?>
                </div>

                <h3 style="color: #293661;">Resumen de Compra</h3>
                
                <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                    <thead>
                        <tr style="background: #293661; color: white;">
                            <th style="padding: 10px; text-align: left;">Producto</th>
                            <th style="padding: 10px; text-align: center;">Cant.</th>
                            <th style="padding: 10px; text-align: right;">Precio</th>
                            <th style="padding: 10px; text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos_compra as $item): ?>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 10px;">
                                <?php echo htmlspecialchars($item['nombre']); ?>
                            </td>
                            <td style="padding: 10px; text-align: center;">
                                <?php echo $item['cantidad']; ?>
                            </td>
                            <td style="padding: 10px; text-align: right;">
                                <?php echo number_format($item['precio'], 2); ?> €
                            </td>
                            <td style="padding: 10px; text-align: right;">
                                <?php echo number_format($item['precio'] * $item['cantidad'], 2); ?> €
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="text-align: right; font-size: 22px; font-weight: bold; margin-top: 20px; color: #293661;">
                    Total Pagado: <?php echo number_format($total_pagado, 2); ?> €
                </div>

                <div class="no-print" style="text-align: center; margin-top: 40px;">
                    <button onclick="window.print();" class="btn-imprimir">
                        🖨️ Imprimir / Guardar PDF
                    </button>

                    <a href="index.php" style="background: #293661; color: white; padding: 12px 30px; border-radius: 50px; text-decoration: none;">Volver al Inicio</a>
                </div>
            </div>
        </main>
        
        <?php sectionfooter(); ?>
    </div>
</body>
</html>