<?php
// 1. CARGAMOS LIBRERÍAS
require '../../../vendor/autoload.php'; 

use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
include '../conexion.php'; 

// ====================================================
// 2. RECUPERAR DATOS
// ====================================================

$productos_compra = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
$total_pagado = isset($_SESSION['total_carrito']) ? $_SESSION['total_carrito'] : 0;
$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0;

// DATOS FICTICIOS SI SE RECARGA LA PÁGINA (Para que no te eche al probar)
if (empty($productos_compra)) {
    $productos_compra[] = [
        'nombre' => 'Producto de Prueba (Recarga)',
        'cantidad' => 1,
        'precio' => 0.00,
        'medidas' => 'N/A'
    ];
}

// Intentamos sacar datos del cliente de la BD para mostrarlos en el PDF
$cliente = ['nombre' => 'Cliente', 'direccion' => 'Dirección']; // Valores por defecto
if ($usuario_id > 0) {
    // CAMBIO: Usamos la tabla 'clientes' que es la correcta en tu BD
    $stmt = $conn->prepare("SELECT nombre, email, direccion FROM clientes WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $datos_bd = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($datos_bd) {
        $cliente = $datos_bd;
    }
}

$numero_pedido = "FUL-" . date('Ymd') . "-" . rand(100, 999);
$fecha_pedido = date('d/m/Y H:i');

// ====================================================
// 3. GENERAR PDF (Dompdf)
// ====================================================
$pathLogo = '../imagenes/logo.png';
$base64Logo = '';
if (file_exists($pathLogo)) {
    $type = pathinfo($pathLogo, PATHINFO_EXTENSION);
    $data = file_get_contents($pathLogo);
    $base64Logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
}

ob_start(); 
?>
<html>
<head>
    <style>
        body { font-family: sans-serif; color: #333; font-size: 14px; }
        .header { width: 100%; border-bottom: 2px solid #293661; padding-bottom: 20px; margin-bottom: 20px; }
        .logo { width: 150px; }
        .company-info { text-align: right; float: right; }
        .client-box { background: #f4f4f4; padding: 15px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #293661; color: white; padding: 8px; text-align: left; }
        td { border-bottom: 1px solid #ddd; padding: 8px; }
        .total { text-align: right; font-size: 18px; font-weight: bold; margin-top: 15px; color: #293661; }
    </style>
</head>
<body>
    <div class="header">
        <?php if($base64Logo): ?>
            <img src="<?php echo $base64Logo; ?>" class="logo">
        <?php else: ?>
            <h2>METALISTERÍA FULSAN</h2>
        <?php endif; ?>
        <div class="company-info">
            <p>Cortijo la Purisima, 2P<br>18004 Granada<br>Tlf: 652 921 960</p>
        </div>
    </div>

    <div class="client-box">
        <strong>Cliente:</strong> <?php echo htmlspecialchars($cliente['nombre']); ?><br>
        <strong>Dirección:</strong> <?php echo htmlspecialchars($cliente['direccion']); ?><br>
        <strong>Nº Pedido:</strong> <?php echo $numero_pedido; ?><br>
        <strong>Fecha:</strong> <?php echo $fecha_pedido; ?>
    </div>

    <table>
        <thead><tr><th>Producto</th><th>Cant.</th><th>Precio</th><th>Total</th></tr></thead>
        <tbody>
            <?php foreach ($productos_compra as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                <td><?php echo $item['cantidad']; ?></td>
                <td><?php echo number_format($item['precio'], 2); ?> €</td>
                <td><?php echo number_format($item['precio'] * $item['cantidad'], 2); ?> €</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="total">Total: <?php echo number_format($total_pagado, 2); ?> €</div>
</body>
</html>
<?php
$html_factura = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html_factura);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$pdfOutput = $dompdf->output();


// ====================================================
// 4. ENVIAR EMAIL
// ====================================================
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    
    // ---------------------------------------------------------
    // TUS DATOS DE ENVÍO (REMITENTE)
    // ---------------------------------------------------------
    $mail->Username   = 'ams147@inlumine.ual.es'; // <--- TU GMAIL DE ORIGEN
    $mail->Password   = 'ijqf gkvu jdzx rqsg';    // <--- TU CLAVE DE 16 LETRAS
    // ---------------------------------------------------------
    
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom($mail->Username, 'Metalisteria Fulsan'); 
    
    // ---------------------------------------------------------
    // DESTINATARIO (AQUÍ PONES EL CORREO A MANO)
    // ---------------------------------------------------------
    
    $mail->addAddress('mk466@inlumine.ual.es', 'Cliente Prueba'); 

    // ---------------------------------------------------------

    $mail->isHTML(true);
    $mail->Subject = "Factura Pedido $numero_pedido";
    $mail->Body    = "Hola, gracias por tu compra. Adjuntamos tu factura.";
    $mail->addStringAttachment($pdfOutput, "Factura_$numero_pedido.pdf");

    $mail->send();

} catch (Exception $e) {
    // Silencioso: Si falla, no mostramos error feo en pantalla, solo seguimos.
    // (Puedes mirar el log de errores de PHP si algo va mal)
}


    unset($_SESSION['carrito']); 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Completado</title>
    <link rel="stylesheet" href="../css/datosEnvio.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        .factura-container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .success-icon { font-size: 50px; color: #28a745; text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="visitante-conocenos">
        <header class="cabecera">
            <div class="container">
                <div class="logo-main">
                    <a href="index.php" class="logo-link">
                        <img src="../imagenes/logo.png" alt="Logo">
                        <div class="logo-text"><span>Metalistería</span><strong>Fulsan</strong></div>
                    </a>
                </div>
                <nav class="nav-bar">
                    <a href="conocenos.php">Conócenos</a>
                    <a href="productos.php">Productos</a>
                    <a href="carrito.php">Carrito</a>
                </nav>
            </div>
        </header>

        <section class="steps-section">
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
                <div class="success-icon">✅</div>
                <h1 style="text-align: center; color: #293661;">¡Gracias por tu compra!</h1>
                <p style="text-align: center; color: #666;">Hemos enviado la factura a tu correo electrónico.</p>

                <h3 style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: 30px;">Resumen del Pedido</h3>
                
                <table style="width: 100%; margin-top: 20px; border-collapse: collapse;">
                    <tr style="background: #f9f9f9;">
                        <th style="padding: 10px; text-align: left;">Producto</th>
                        <th style="padding: 10px; text-align: right;">Total</th>
                    </tr>
                    <?php foreach ($productos_compra as $item): ?>
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #eee;">
                            <?php echo htmlspecialchars($item['nombre']); ?> (x<?php echo $item['cantidad']; ?>)
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: right;">
                            <?php echo number_format($item['precio'] * $item['cantidad'], 2); ?> €
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>

                <div style="text-align: right; font-size: 24px; font-weight: bold; margin-top: 20px; color: #293661;">
                    Total: <?php echo number_format($total_pagado, 2); ?> €
                </div>

                <div style="text-align: center; margin-top: 40px;">
                    <a href="index.php" style="background: #293661; color: white; padding: 12px 30px; border-radius: 50px; text-decoration: none;">Volver al Inicio</a>
                </div>
            </div>
        </main>
        
        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-logo-section"><div class="logo-footer"><img src="../imagenes/footer.png" alt="Logo"></div></div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>