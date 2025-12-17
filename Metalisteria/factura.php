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

$email_empresa = "metalfulsan@gmail.com"; 

// Recuperamos datos ANTES de borrar
$productos_compra = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$total_pagado     = 0; // Recalculamos abajo para seguridad
$usuario_id       = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0; 

// Recalcular total real (ignorar pendientes o rechazados por seguridad)
foreach($productos_compra as $item) {
    // En la nueva estructura, 'precio' ya viene filtrado desde carrito.php
    $total_pagado += $item['precio'] * $item['cantidad'];
}

// -----------------------------------------------------------------------
// [SEGURIDAD] ANTI-REFRESH Y VALIDACIÓN
// -----------------------------------------------------------------------
if (empty($productos_compra) && !isset($_SESSION['pedido_procesado_temp'])) {
    header("Location: index.php");
    exit;
}

// LÓGICA DE RECARGA (F5)
if (isset($_SESSION['pedido_procesado_temp'])) {
    $datos_temp = $_SESSION['pedido_procesado_temp'];
    $productos_compra     = $datos_temp['productos'];
    $total_pagado         = $datos_temp['total'];
    $cliente              = $datos_temp['cliente'];
    $numero_pedido_visual = $datos_temp['ref'];
    $fecha_visual         = $datos_temp['fecha'];
    $metodo_pago_texto    = $datos_temp['metodo'];
    $pedido_ya_guardado = true; 
} else {
    $pedido_ya_guardado = false; 
}
// -----------------------------------------------------------------------

if (!$pedido_ya_guardado) {
    
    // --- LÓGICA PAGO ---
    $metodo_pago_texto = isset($lang['factura_pago_tarjeta']) ? $lang['factura_pago_tarjeta'] : "Tarjeta";
    if (isset($_SESSION['metodo_pago_final'])) {
        $metodo_pago_texto = $_SESSION['metodo_pago_final'];
    } elseif (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'fake_bizum.php') !== false) {
        $metodo_pago_texto = isset($lang['factura_pago_bizum']) ? $lang['factura_pago_bizum'] : "Bizum";
    }

    $numero_pedido_visual = "PED-" . date('dmY') . "-" . rand(100, 999);
    $fecha_visual = date('d/m/Y H:i');

    // --- DATOS CLIENTE ---
    $cliente = [
        'nombre_completo' => 'Cliente Invitado', 
        'email' => 'Sin email',
        'direccion_completa' => 'Recogida en tienda'
    ]; 

    $nombre_envio = 'Invitado'; 
    $email_envio  = '';
    $tel_envio    = '';
    $calle        = '';
    $numero       = '';
    $piso         = '';
    $cp           = '';
    $localidad    = '';
    $notas        = '';

    if (isset($_SESSION['datos_envio']) && !empty($_SESSION['datos_envio'])) {
        $d = $_SESSION['datos_envio']; 
        $nombre_envio = $d['nombre']; 
        $email_envio  = $d['email'];
        $tel_envio    = $d['telefono'];
        $calle        = $d['calle'];
        $numero       = $d['numero'];
        $piso         = $d['piso'];
        $cp           = $d['cp'];
        $localidad    = $d['localidad'];
        $notas        = isset($d['notas']) ? $d['notas'] : '';

        $cliente['nombre_completo'] = $nombre_envio;
        $cliente['email'] = $email_envio;
    } elseif ($usuario_id > 0 && isset($conn)) {
        try {
            $stmt = $conn->prepare("SELECT nombre, apellidos, email, telefono, direccion, numero, piso, codigo_postal, ciudad FROM clientes WHERE id = ?");
            $stmt->execute([$usuario_id]);
            $datos_bd = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($datos_bd) {
                $nombre_envio = $datos_bd['nombre'] . ' ' . $datos_bd['apellidos'];
                $email_envio  = $datos_bd['email'];
                $tel_envio    = $datos_bd['telefono'];
                $calle        = $datos_bd['direccion'];
                $numero       = $datos_bd['numero'];
                $piso         = $datos_bd['piso'];
                $cp           = $datos_bd['codigo_postal'];
                $localidad    = $datos_bd['ciudad'];
                $cliente['nombre_completo'] = $nombre_envio;
                $cliente['email'] = $email_envio;
            }
        } catch (Exception $e) { }
    }

    $dir = $calle;
    if(!empty($numero)) $dir .= ", Nº " . $numero;
    if(!empty($piso))   $dir .= ", Piso " . $piso;
    if(!empty($cp))     $dir .= ", CP: " . $cp;
    if(!empty($localidad)) $dir .= " (" . $localidad . ")";
    if (!empty(trim($dir))) { $cliente['direccion_completa'] = $dir; }

    // ====================================================
    // 5. GUARDAR EN BASE DE DATOS (CORREGIDO PARA PERSONALIZADOS)
    // ====================================================
    if (isset($conn) && !empty($productos_compra)) {
        try {
            $conn->beginTransaction();

            // Insertar Venta Principal
            $sql_venta = "INSERT INTO ventas (
                            fecha, id_cliente, total, nombre_completo, email, telefono, 
                            calle, numero, piso, codigo_postal, localidad, notas
                          ) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt_venta = $conn->prepare($sql_venta);
            $stmt_venta->execute([
                $usuario_id, 
                $total_pagado,
                $nombre_envio,
                $email_envio,
                $tel_envio,
                $calle,
                $numero,
                $piso,
                $cp,
                $localidad,
                $notas
            ]);
            
            $id_venta_generada = $conn->lastInsertId();

            // Insertar Detalles (AHORA INCLUYE MEDIDAS Y COLOR)
            // Asegúrate de haber ejecutado el ALTER TABLE que te di antes
            $sql_detalle = "INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario, subtotal, medidas, color, detalles_extra) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_detalle = $conn->prepare($sql_detalle);

            foreach ($productos_compra as $item) {
                $subtotal_producto = $item['precio'] * $item['cantidad'];
                // La nueva estructura usa 'producto_id', no 'id'
                $id_prod = isset($item['producto_id']) ? $item['producto_id'] : (isset($item['id']) ? $item['id'] : 0); 

                // Datos personalizados
                $medidas_fin = isset($item['medidas']) ? $item['medidas'] : '';
                $color_fin   = isset($item['color']) ? $item['color'] : '';
                $detalles_fin = isset($item['detalles']) ? $item['detalles'] : '';

                $stmt_detalle->execute([
                    $id_venta_generada,
                    $id_prod,
                    $item['cantidad'],
                    $item['precio'],
                    $subtotal_producto,
                    $medidas_fin,
                    $color_fin,
                    $detalles_fin
                ]);
            }

            // Borrar Carrito (Si es usuario registrado)
            // Al borrar el carrito padre, los personalizados se borran por CASCADE
            if ($usuario_id > 0) {
                $sql_borrar_carrito = "DELETE FROM carrito WHERE cliente_id = ?";
                $stmt_borrar = $conn->prepare($sql_borrar_carrito);
                $stmt_borrar->execute([$usuario_id]);
            }

            $conn->commit();

            // Guardar datos temporales para evitar F5
            $_SESSION['pedido_procesado_temp'] = [
                'productos' => $productos_compra,
                'total' => $total_pagado,
                'cliente' => $cliente,
                'ref' => $numero_pedido_visual,
                'fecha' => $fecha_visual,
                'metodo' => $metodo_pago_texto
            ];

            // Limpiar Sesión
            unset($_SESSION['carrito']);
            unset($_SESSION['total_carrito']);
            if(isset($_SESSION['datos_envio'])) unset($_SESSION['datos_envio']);

        } catch (Exception $e) {
            $conn->rollBack();
            // Si falla, podrías descomentar esto para ver el error:
            // die("Error guardando pedido: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['idioma']) ? $_SESSION['idioma'] : 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($lang['factura_titulo']) ? $lang['factura_titulo'] : 'Factura'; ?> - Metalistería Fulsan</title>
    
    <link rel="stylesheet" href="css/datosEnvio.css">
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* ESTILOS DE LA FACTURA (Idénticos a los que ya te gustaban) */
        :root { --primary: #293661; --bg-light: #f4f7f6; --text-dark: #2c3e50; }
        body { background-color: var(--bg-light); font-size: 14px; }
        .factura-wrapper { max-width: 800px; margin: 0 auto; background: #fff; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid rgba(0,0,0,0.05); }
        .invoice-header { background: linear-gradient(135deg, var(--primary) 0%, #1a2442 100%); color: white; padding: 25px 30px; display: flex; justify-content: space-between; align-items: center; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .brand-section h1 { margin: 0; font-size: 24px; font-weight: 800; letter-spacing: 1px; font-family: 'Poppins', sans-serif; }
        .brand-section p { opacity: 0.8; margin: 2px 0 0; font-size: 13px; }
        .status-badge { background: rgba(255,255,255,0.1); backdrop-filter: blur(5px); padding: 8px 15px; border-radius: 8px; text-align: right; border: 1px solid rgba(255,255,255,0.2); }
        .status-badge .label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; display: block; opacity: 0.8; }
        .status-badge .value { font-size: 16px; font-weight: 700; color: #4ade80; display: block; margin-top: 2px; }
        .invoice-body { padding: 25px 30px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
        .info-card h3 { color: var(--primary); font-size: 13px; text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 10px; border-bottom: 2px solid #eee; padding-bottom: 5px; }
        .client-data p { margin: 3px 0; color: #555; font-size: 13px; display: flex; align-items: center; gap: 8px; }
        .client-data strong { color: var(--text-dark); min-width: 65px; }
        .table-container { border-radius: 8px; border: 1px solid #eee; overflow: hidden; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #f8f9fa; }
        th { padding: 10px 15px; text-align: left; font-size: 12px; color: #777; font-weight: 600; text-transform: uppercase; }
        td { padding: 10px 15px; border-bottom: 1px solid #eee; color: #333; font-weight: 500; font-size: 13px; }
        tr:last-child td { border-bottom: none; }
        .total-section { background: var(--primary); color: white; padding: 15px 30px; border-radius: 10px; display: flex; flex-direction: column; align-items: flex-end; justify-content: center; margin-top: 15px; -webkit-print-color-adjust: exact; print-color-adjust: exact; page-break-inside: avoid; }
        .botones-container { display: flex; justify-content: center; gap: 20px; margin-top: 30px; padding-top: 10px; }
        .btn-base { display: inline-flex; align-items: center; justify-content: center; height: 45px; padding: 0 25px; border-radius: 50px; text-decoration: none; font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .btn-imprimir { background-color: var(--primary); color: white; gap: 10px; }
        .btn-imprimir:hover { background-color: #1a2442; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(41, 54, 97, 0.3); }
        .btn-inicio { background-color: white; color: var(--text-dark); border: 2px solid #eee; }
        .btn-inicio:hover { border-color: var(--primary); color: var(--primary); }
        
        @media print {
            @page { size: auto; margin: 0mm; }
            body { background-color: white; margin: 10mm; -webkit-print-color-adjust: exact; }
            header, footer, .steps-section, .botones-container, .no-print { display: none !important; }
            .visitante-conocenos, .envio-main, .container { visibility: visible !important; display: block !important; margin: 0 !important; padding: 0 !important; width: 100% !important; height: auto !important; overflow: visible !important; box-shadow: none !important; }
            .factura-wrapper { visibility: visible !important; width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; box-shadow: none !important; border: none !important; transform: scale(0.95); transform-origin: top left; }
            .invoice-header, .invoice-body, .total-section, .info-grid { page-break-inside: avoid; }
        }
        @media (max-width: 768px) { .info-grid { grid-template-columns: 1fr; gap: 15px; } .invoice-header { flex-direction: column; text-align: center; gap: 15px; } .botones-container { flex-direction: column; } }
    </style>
</head>
<body>
    <div class="visitante-conocenos">
        
        <?php sectionheader(); ?>

        <section class="steps-section no-print">
            <div class="container">
                <div class="steps-container">
                    <div class="step"><span class="step-number">1</span><span class="step-label"><?php echo isset($lang['factura_paso1']) ? $lang['factura_paso1'] : 'Datos'; ?></span></div>
                    <div class="step-line"></div>
                    <div class="step"><span class="step-number">2</span><span class="step-label"><?php echo isset($lang['factura_paso2']) ? $lang['factura_paso2'] : 'Pago'; ?></span></div>
                    <div class="step-line"></div>
                    <div class="step active"><span class="step-number">3</span><span class="step-label"><?php echo isset($lang['factura_paso3']) ? $lang['factura_paso3'] : 'Factura'; ?></span></div>
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
                        <span class="label"><?php echo isset($lang['factura_ref_pedido']) ? $lang['factura_ref_pedido'] : 'Ref. Pedido'; ?></span>
                        <span class="value"><?php echo $numero_pedido_visual; ?></span>
                        <div style="font-size: 12px; margin-top: 5px; color: #ccc;"><?php echo $fecha_visual; ?></div>
                    </div>
                </div>

                <div class="invoice-body">
                    <div class="info-grid">
                        <div class="info-card">
                            <h3><?php echo isset($lang['factura_facturar_a']) ? $lang['factura_facturar_a'] : 'Facturar A'; ?></h3>
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
                            <h3><?php echo isset($lang['factura_detalles_pago']) ? $lang['factura_detalles_pago'] : 'Pago'; ?></h3>
                            <div class="client-data">
                                <p><strong>Estado:</strong> <span style="color: green; font-weight: bold;">Pagado ✅</span></p>
                                <p><strong>Método:</strong> <?php echo htmlspecialchars($metodo_pago_texto); ?></p>
                                <br>
                                <div style="background: #f0f8ff; padding: 10px; border-radius: 8px; font-size: 13px; color: #293661;">
                                    <?php 
                                    if ($usuario_id > 0) {
                                        echo isset($lang['factura_mensaje_registrado']) ? $lang['factura_mensaje_registrado'] : 'Factura disponible en Mi Perfil';
                                    } else {
                                        echo isset($lang['factura_mensaje_invitado']) ? $lang['factura_mensaje_invitado'] : 'Guarde este documento.';
                                    }
                                    ?>
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
                                    <td>
                                        <span style="display: block; font-weight: 600; color: #293661;"><?php echo htmlspecialchars($item['nombre']); ?></span>
                                        <?php if(isset($item['es_personalizado']) && $item['es_personalizado'] == 1): ?>
                                            <span style="font-size: 11px; color: #666;">
                                                <?php echo htmlspecialchars($item['medidas'] . ' - ' . $item['color']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center; color: #666;"><?php echo $item['cantidad']; ?></td>
                                    
                                    <td style="text-align: right; color: #666;">
                                        <div style="display:flex; flex-direction:column; align-items:flex-end;">
                                            <span><?php echo number_format($item['precio'], 2); ?> €</span>
                                        </div>
                                    </td>
                                    
                                    <td style="text-align: right; font-weight: bold;"><?php echo number_format($item['precio'] * $item['cantidad'], 2); ?> €</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="total-section">
                        <div style="display: flex; justify-content: space-between; width: 100%; max-width: 300px; margin-bottom: 5px; font-size: 14px; opacity: 0.9;">
                            <span>Base Imponible:</span>
                            <span><?php echo number_format($total_pagado / 1.21, 2); ?> €</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; width: 100%; max-width: 300px; margin-bottom: 10px; font-size: 14px; opacity: 0.9;">
                            <span>IVA (21%):</span>
                            <span><?php echo number_format($total_pagado - ($total_pagado / 1.21), 2); ?> €</span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; width: 100%; max-width: 300px; border-top: 1px solid rgba(255,255,255,0.3); padding-top: 10px;">
                            <span class="total-label" style="font-size: 18px; font-weight: 600;">Total Pagado:</span>
                            <span class="total-amount" style="font-size: 24px; font-weight: 800;"><?php echo number_format($total_pagado, 2); ?> €</span>
                        </div>
                    </div>

                    <div class="botones-container no-print">
                        <a href="#" onclick="window.print(); return false;" class="btn-base btn-imprimir">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                            Descargar PDF
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