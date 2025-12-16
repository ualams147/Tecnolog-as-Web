<?php
// detallesproducto.php

// --- 1. CONFIGURACIÓN Y SEGURIDAD ---
require_once 'conexion.php';
require_once 'CabeceraFooter.php';

// Verificar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: iniciarsesion.php");
    exit;
}

$id_cliente = $_SESSION['usuario_id'];

// Verificar que nos pasan un ID
if (!isset($_GET['id'])) {
    header("Location: pedidosactivos.php");
    exit;
}

$id_venta = $_GET['id'];

try {
    // --- 2. CONSULTA SEGURA (Pedido + Cliente) ---
    // Buscamos el pedido, pero SOLO si coincide con el id_cliente logueado.
    $sql_venta = "SELECT * FROM ventas WHERE id = ? AND id_cliente = ?";
    $stmt = $conn->prepare($sql_venta);
    $stmt->execute([$id_venta, $id_cliente]);
    $venta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$venta) {
        // Si no existe o no es suyo, lo mandamos fuera
        header("Location: pedidosactivos.php");
        exit;
    }

    // --- 3. CONSULTA DE DETALLES (Productos) ---
    $sql_detalles = "SELECT d.*, p.nombre as nombre_prod, p.imagen_url 
                     FROM detalle_ventas d 
                     JOIN productos p ON d.id_producto = p.id 
                     WHERE d.id_venta = ?";
    $stmt_det = $conn->prepare($sql_detalles);
    $stmt_det->execute([$id_venta]);
    $productos_venta = $stmt_det->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error en base de datos: " . $e->getMessage());
}

// Lógica de colores para el estado
$estado = $venta['estado'] ?? 'Pendiente';
$clase_estado = 'estado-default';
if(stripos($estado, 'Entregado') !== false) $clase_estado = 'estado-entregado';
elseif(stripos($estado, 'Pendiente') !== false) $clase_estado = 'estado-pendiente';
elseif(stripos($estado, 'Cancelado') !== false) $clase_estado = 'estado-cancelado';
elseif(stripos($estado, 'Proceso') !== false) $clase_estado = 'estado-proceso';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Pedido #<?php echo $venta['id']; ?> - Metalistería Fulsan</title>
    
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="css/detallesproductos.css">
</head>

<body>

    <div class="visitante-producto-detalle">
        
        <?php 
        // Cabecera del sitio
        if(function_exists('sectionheader')) {
            sectionheader(1); 
        } 
        ?>

        <section class="product-hero">
            <div class="container hero-content">
                <a href="javascript:history.back()" class="btn-back">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </a>
                
                <h1 class="product-title-main">Pedido #<?php echo str_pad($venta['id'], 5, '0', STR_PAD_LEFT); ?></h1>
            </div>
        </section>

        <main class="product-main container">
            <div class="details-container-card">
                
                <div class="info-pedido-grid">
                    <div class="info-grupo">
                        <h3><i class="far fa-calendar-alt"></i> Fecha</h3>
                        <p><?php echo date('d/m/Y', strtotime($venta['fecha'])); ?></p>
                    </div>
                    
                    <div class="info-grupo">
                        <h3><i class="fas fa-tasks"></i> Estado</h3>
                        <span class="estado-badge <?php echo $clase_estado; ?>">
                            <?php echo htmlspecialchars($estado); ?>
                        </span>
                    </div>

                    <div class="info-grupo">
                        <h3><i class="fas fa-hashtag"></i> Referencia</h3>
                        <p><?php echo str_pad($venta['id'], 5, '0', STR_PAD_LEFT); ?></p>
                    </div>
                </div>

                <h3 class="titulo-seccion">Productos del Pedido</h3>
                
                <div class="lista-productos">
                    <?php foreach($productos_venta as $item): ?>
                        <div class="item-producto">
                            <img src="<?php echo !empty($item['imagen_url']) ? htmlspecialchars($item['imagen_url']) : 'imagenes/producto_default.png'; ?>" 
                                 class="thumb-prod" 
                                 alt="Producto"
                                 onerror="this.src='https://via.placeholder.com/90?text=No+Img'">
                            
                            <div class="detalles-prod">
                                <h4><?php echo htmlspecialchars($item['nombre_prod']); ?></h4>
                                <div class="meta-info">
                                    <span class="meta-tag">Cant: <?php echo $item['cantidad']; ?></span>
                                    <?php if(!empty($item['color'])): ?>
                                        <span class="meta-tag">Color: <?php echo htmlspecialchars($item['color']); ?></span>
                                    <?php endif; ?>
                                    <?php if(!empty($item['medidas'])): ?>
                                        <span class="meta-tag">Medidas: <?php echo htmlspecialchars($item['medidas']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="precio-column">
                                <span class="precio-subtotal"><?php echo number_format($item['subtotal'], 2); ?> €</span>
                                <span class="precio-unitario"><?php echo number_format($item['precio_unitario'], 2); ?> € / ud</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php 
                    // Cálculos
                    $total = $venta['total'];
                    $base = $total / 1.21;
                    $iva = $total - $base;
                ?>
                <div class="resumen-economico">
                    <div class="fila-resumen">
                        <span>Base Imponible:</span>
                        <span><?php echo number_format($base, 2); ?> €</span>
                    </div>
                    <div class="fila-resumen">
                        <span>IVA (21%):</span>
                        <span><?php echo number_format($iva, 2); ?> €</span>
                    </div>
                    <div class="fila-resumen total">
                        <span>Total:</span>
                        <span><?php echo number_format($total, 2); ?> €</span>
                    </div>
                </div>

            </div>
        </main>

        <?php 
        // Footer del sitio
        if(function_exists('sectionfooter')) {
            sectionfooter(); 
        }
        ?>
    </div>

</body>
</html>