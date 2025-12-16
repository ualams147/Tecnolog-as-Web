<?php
// 1. CARGAMOS LA CABECERA Y FUNCIONES COMUNES
include 'CabeceraFooter.php'; 

// 2. CONEXIÓN A BASE DE DATOS
include 'conexion.php';

// ====================================================
// 3. CONFIGURACIÓN Y RECUPERACIÓN DE DATOS
// ====================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CONFIGURACIÓN DE LA EMPRESA
$email_empresa = "metalfulsan@gmail.com"; 

$productos_compra = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$total_pagado = isset($_SESSION['total_carrito']) ? $_SESSION['total_carrito'] : 0;
$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0;

// -----------------------------------------------------------------------
// [MODIFICACIÓN] SEGURIDAD ANTI-REFRESH
// -----------------------------------------------------------------------
// Si el carrito está vacío, significa que el pedido ya se procesó (se borró la sesión al final)
// o que el usuario entró directo sin comprar. Lo echamos al inicio.
if (empty($productos_compra)) {
    header("Location: index.php");
    exit;
}
// -----------------------------------------------------------------------

// --- LÓGICA DE DETECCIÓN DE PAGO ---
$origen = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
if (strpos($origen, 'fake_bizum.php') !== false) {
    $metodo_pago_texto = "Bizum";
} else {
    // Si viene de Stripe o metodopago.php directo
    $metodo_pago_texto = "Tarjeta de Crédito/Débito";
}
$_SESSION['metodo_pago_final'] = $metodo_pago_texto;


$numero_pedido_visual = "PED-" . date('dmY') . "-" . rand(100, 999);
$fecha_visual = date('d/m/Y H:i');

// ====================================================
// 4. DATOS DEL CLIENTE (LÓGICA PRIORITARIA)
// ====================================================

$cliente = [
    'nombre_completo' => 'Cliente Invitado', 
    'email' => 'Sin email',
    'direccion_completa' => 'Recogida en tienda'
]; 

// A) INTENTAMOS COGER DATOS DEL FORMULARIO DE ENVÍO (Si existen en sesión)
if (isset($_SESSION['datos_envio']) && !empty($_SESSION['datos_envio'])) {
    $d = $_SESSION['datos_envio']; 
    
    $cliente['nombre_completo'] = $d['nombre']; 
    $cliente['email'] = $d['email'];
    
    $dir = $d['calle'];
    if(!empty($d['numero'])) $dir .= ", Nº " . $d['numero'];
    if(!empty($d['piso']))   $dir .= ", Piso " . $d['piso'];
    if(!empty($d['cp']))     $dir .= ", CP: " . $d['cp'];
    if(!empty($d['localidad'])) $dir .= " (" . $d['localidad'] . ")";
    
    $cliente['direccion_completa'] = $dir;

} 
// B) SI NO HAY DATOS DE FORMULARIO, USAMOS LA BASE DE DATOS
elseif ($usuario_id > 0 && isset($conn)) {
    try {
        $stmt = $conn->prepare("SELECT nombre, apellidos, email, direccion, numero, piso, ciudad FROM clientes WHERE id = ?");
        $stmt->execute([$usuario_id]);
        $datos_bd = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($datos_bd) {
            $cliente['nombre_completo'] = $datos_bd['nombre'] . ' ' . $datos_bd['apellidos'];
            $cliente['email'] = $datos_bd['email'];
            
            $dir = $datos_bd['direccion'];
            if(!empty($datos_bd['numero'])) $dir .= ", Nº " . $datos_bd['numero'];
            if(!empty($datos_bd['piso']))   $dir .= ", Piso " . $datos_bd['piso'];
            if(!empty($datos_bd['ciudad'])) $dir .= " (" . $datos_bd['ciudad'] . ")";
            
            $cliente['direccion_completa'] = $dir;
        }
    } catch (Exception $e) { }
}

// ====================================================
// 5. GUARDAR EN BASE DE DATOS Y VACIAR CARRITO
// ====================================================
// Nota: Quitamos la comprobación de $es_recarga porque si llegamos aquí, NO es recarga.
if (isset($conn) && $usuario_id > 0) {
    try {
        $conn->beginTransaction();

        // A) INSERTAR VENTA
        $sql_venta = "INSERT INTO ventas (fecha, id_cliente, total) VALUES (NOW(), ?, ?)";
        $stmt_venta = $conn->prepare($sql_venta);
        $stmt_venta->execute([$usuario_id, $total_pagado]);
        
        $id_venta_generada = $conn->lastInsertId();

        // B) INSERTAR DETALLES
        $sql_detalle = "INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
        $stmt_detalle = $conn->prepare($sql_detalle);

        foreach ($productos_compra as $item) {
            $subtotal_producto = $item['precio'] * $item['cantidad'];
            $id_prod = isset($item['id']) ? $item['id'] : 0; 

            $stmt_detalle->execute([
                $id_venta_generada,
                $id_prod,
                $item['cantidad'],
                $item['precio'],
                $subtotal_producto
            ]);
        }

        // C) BORRAR CARRITO DE LA BASE DE DATOS
        $sql_borrar_carrito = "DELETE FROM carrito WHERE cliente_id = ?";
        $stmt_borrar = $conn->prepare($sql_borrar_carrito);
        $stmt_borrar->execute([$usuario_id]);

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollBack();
    }
}

// ====================================================
// 6. LIMPIEZA DE SESIÓN (Browser)
// ====================================================
// Esto borra el carrito de la memoria. Si refrescan después de esto,
// $productos_compra estará vacío y saltará el "header" del principio.
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
    <title>Factura - Metalistería Fulsan</title>
    
    <link rel="stylesheet" href="css/datosEnvio.css">
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* ESTILOS GENERALES */
        :root {
            --primary: #293661;
            --accent: #eca400;
            --bg-light: #f4f7f6;
            --text-dark: #2c3e50;
        }

        .factura-wrapper {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .invoice-header {
            background: linear-gradient(135deg, var(--primary) 0%, #1a2442 100%);
            color: white;
            padding: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact;
        }
        
        .invoice-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: repeating-linear-gradient(45deg, var(--accent), var(--accent) 10px, transparent 10px, transparent 20px);
        }

        .brand-section h1 { margin: 0; font-size: 28px; font-weight: 800; letter-spacing: 1px; font-family: 'Poppins', sans-serif; }
        .brand-section p { opacity: 0.8; margin: 5px 0 0; font-size: 14px; }

        .status-badge {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(5px);
            padding: 10px 20px;
            border-radius: 12px;
            text-align: right;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .status-badge .label { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; display: block; opacity: 0.8; }
        .status-badge .value { font-size: 20px; font-weight: 700; color: #4ade80; display: block; margin-top: 5px; }

        .invoice-body { padding: 40px; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .info-card h3 {
            color: var(--primary); font-size: 14px; text-transform: uppercase; letter-spacing: 1.5px;
            margin-bottom: 15px; border-bottom: 2px solid #eee; padding-bottom: 10px;
        }

        .client-data p { margin: 5px 0; color: #555; font-size: 15px; display: flex; align-items: center; gap: 10px; }
        .client-data strong { color: var(--text-dark); min-width: 70px; }

        .table-container { border-radius: 12px; border: 1px solid #eee; overflow: hidden; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #f8f9fa; }
        th { padding: 15px 20px; text-align: left; font-size: 13px; color: #777; font-weight: 600; text-transform: uppercase; }
        td { padding: 15px 20px; border-bottom: 1px solid #eee; color: #333; font-weight: 500; }
        tr:last-child td { border-bottom: none; }
        
        .total-section {
            background: var(--primary); color: white; padding: 20px 40px; border-radius: 12px;
            display: flex; justify-content: space-between; align-items: center; margin-top: 20px;
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact;
        }
        .total-label { font-size: 18px; font-weight: 500; }
        .total-amount { font-size: 28px; font-weight: 800; }

        .botones-container { display: flex; justify-content: center; gap: 20px; margin-top: 40px; padding-top: 20px; }
        .btn-base {
            display: inline-flex; align-items: center; justify-content: center; height: 50px; padding: 0 30px;
            border-radius: 50px; text-decoration: none; font-family: 'Poppins', sans-serif; font-weight: 600;
            font-size: 15px; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .btn-imprimir { background-color: var(--primary); color: white; gap: 10px; }
        .btn-imprimir:hover { background-color: #1a2442; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(41, 54, 97, 0.3); }
        .btn-inicio { background-color: white; color: var(--text-dark); border: 2px solid #eee; }
        .btn-inicio:hover { border-color: var(--primary); color: var(--primary); }

        /* IMPRESIÓN (PDF) */
        @media print {
            body { background-color: white; margin: 0; }
            header, footer, .steps-section, .botones-container, .no-print { display: none !important; }
            .visitante-conocenos, .envio-main, .container {
                visibility: visible !important; display: block !important; margin: 0 !important;
                padding: 0 !important; width: 100% !important; height: auto !important; overflow: visible !important;
            }
            .factura-wrapper {
                visibility: visible !important; position: absolute; left: 0; top: 0;
                width: 100%; margin: 0; padding: 0; box-shadow: none; border: none;
            }
            .invoice-header, .total-section {
                -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color: white !important;
            }
        }
        
        @media (max-width: 768px) {
            .info-grid { grid-template-columns: 1fr; gap: 20px; }
            .invoice-header { flex-direction: column; text-align: center; gap: 20px; }
            .botones-container { flex-direction: column; }
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

        <main class="envio-main container" style="margin-top: 40px; margin-bottom: 60px;">
            <div class="factura-wrapper">
                
                <div class="invoice-header">
                    <div class="brand-section">
                        <h1>METALISTERÍA FULSAN</h1>
                        <p>Cortijo la Purisima, 2P | 18004 Granada</p>
                        <p>Tlf: 652 921 960 | <?php echo htmlspecialchars($email_empresa); ?></p>
                    </div>
                    <div class="status-badge">
                        <span class="label">Ref. Pedido</span>
                        <span class="value"><?php echo $numero_pedido_visual; ?></span>
                        <div style="font-size: 12px; margin-top: 5px; color: #ccc;"><?php echo $fecha_visual; ?></div>
                    </div>
                </div>

                <div class="invoice-body">
                    <div class="info-grid">
                        
                        <div class="info-card">
                            <h3>Facturar A</h3>
                            <div class="client-data">
                                <div style="font-size: 20px; font-weight: 700; margin-bottom: 10px; color: #293661;">
                                    <?php echo htmlspecialchars($cliente['nombre_completo']); ?>
                                </div>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($cliente['email']); ?></p>
                                <p><strong>Dirección:</strong></p>
                                <p style="padding-left: 10px; border-left: 3px solid #eeca00;">
                                    <?php echo htmlspecialchars($cliente['direccion_completa']); ?>
                                </p>
                            </div>
                        </div>

                        <div class="info-card">
                            <h3>Detalles del Pago</h3>
                            <div class="client-data">
                                <p><strong>Estado:</strong> <span style="color: green; font-weight: bold;">Pagado ✅</span></p>
                                <p><strong>Método:</strong> <?php echo htmlspecialchars($metodo_pago_texto); ?></p>
                                <p><strong>Moneda:</strong> EUR (€)</p>
                                <br>
                                <div style="background: #f0f8ff; padding: 10px; border-radius: 8px; font-size: 13px; color: #293661;">
                                    Gracias por tu compra. Tienes la factura y los detalles del pedido disponibles <strong>en tu perfil</strong>.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Descripción / Producto</th>
                                    <th style="text-align: center;">Cant.</th>
                                    <th style="text-align: right;">Precio Unit.</th>
                                    <th style="text-align: right;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos_compra as $item): ?>
                                <tr>
                                    <td><span style="display: block; font-weight: 600; color: #293661;"><?php echo htmlspecialchars($item['nombre']); ?></span></td>
                                    <td style="text-align: center; color: #666;"><?php echo $item['cantidad']; ?></td>
                                    <td style="text-align: right; color: #666;"><?php echo number_format($item['precio'], 2); ?> €</td>
                                    <td style="text-align: right; font-weight: bold;"><?php echo number_format($item['precio'] * $item['cantidad'], 2); ?> €</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="total-section">
                        <span class="total-label">Total Pagado</span>
                        <span class="total-amount"><?php echo number_format($total_pagado, 2); ?> €</span>
                    </div>

                    <div class="botones-container no-print">
                        <a href="#" onclick="window.print(); return false;" class="btn-base btn-imprimir">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                            Descargar Factura PDF
                        </a>

                        <a href="index.php" class="btn-base btn-inicio">
                            Volver a la Tienda
                        </a>
                    </div>

                </div>
            </div>
        </main>
        
        <?php sectionfooter(); ?>
    </div>
    
    <script src="js/auth.js"></script>
</body>
</html>