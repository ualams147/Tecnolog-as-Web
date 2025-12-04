<?php
include 'conexion.php';

// 1. VERIFICAR ID DE VENTA
if (!isset($_GET['id'])) {
    header("Location: ListadoVentasAdmin.php");
    exit;
}

$id_venta = $_GET['id'];

// 2. OBTENER DATOS DE LA VENTA Y EL CLIENTE
$sql_venta = "SELECT v.*, c.id as id_cliente, c.nombre as nombre_cli, c.apellidos as apellidos_cli, c.email 
              FROM ventas v 
              JOIN clientes c ON v.id_cliente = c.id 
              WHERE v.id = ?";
$stmt = $conn->prepare($sql_venta);
$stmt->execute([$id_venta]);
$venta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venta) {
    echo "Venta no encontrada.";
    exit;
}

// 3. OBTENER LOS PRODUCTOS DE ESTA VENTA
$sql_detalles = "SELECT d.*, p.nombre as nombre_prod 
                 FROM detalle_ventas d 
                 JOIN productos p ON d.id_producto = p.id 
                 WHERE d.id_venta = ?";
$stmt_det = $conn->prepare($sql_detalles);
$stmt_det->execute([$id_venta]);
$productos_venta = $stmt_det->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles Venta #<?php echo $venta['id']; ?> - Metalisteria Fulsan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stylesDetallesVentas.css">
</head>
<body>
    <div class="DetallesVentas">
        <!-- Cabecera -->
        <header class="cabecera">
                <div class="container">
                    <div class="logo-main">
                        <a href="indexAdmin.php" class="logo-main">
                            <img src="../imagenes/logo.png" alt="Logo Metalful">
                            <div class="logo-text">
                                <span> Metalisteria</span>
                                <strong>Fulsan</strong>
                            </div>
                        </a>
                    </div>
                    
                    <nav class="nav-bar">
                        <a href="ListadoVentasAdmin.php" style="font-weight:bold; border-bottom: 2px solid currentColor;">Ventas</a>
                        <a href="ListadoProductosAdmin.php" >Productos</a>
                        <a href="ListadoClientesAdmin.php">Clientes</a>
                    </nav>

                    <div class="log-out">
                    <a href="../Visitante/index.php">Cerrar Sesión</a>
                </div>

                </div>
        </header>

        <!-- Título -->
        <div class="titulo-section">
            <div class="degradado"></div>
            <div class="recuadro-fondo-titulo"></div> 
            <a href="ListadoVentasAdmin.php" class="flecha-volver">&#8592;</a><a href="ListadoVentasAdmin.php" class="flecha-circular">&#8678;</a>
            <!-- Mostramos el ID de la venta formateado -->
            <h1 class="titulo-principal">Venta #<?php echo str_pad($venta['id'], 4, '0', STR_PAD_LEFT); ?></h1>
        </div>

        <!-- Main Content -->
        <div class="container main-container">
            <!-- Tarjeta Principal Blanca -->
            <div class="details-card">
                
                <!-- Fila Cliente -->
                <div class="client-row">
                    <span class="label-text">Cliente:</span>
                    <!-- Mostramos el nombre completo del cliente -->
                    <input type="text" class="input-display" readonly value="<?php echo $venta['nombre_cli'] . ' ' . $venta['apellidos_cli']; ?>">
                </div>

                <!-- Sección Productos -->
                <div class="products-section">
                    <span class="label-text">Productos del pedido:</span>
                    
                    <!-- Caja contenedora de tarjetas azules -->
                    <div class="products-list-box">
                        
                        <!-- BUCLE: Generamos una tarjeta por cada producto comprado -->
                        <?php foreach($productos_venta as $item): ?>
                            <div class="product-item-card">
                                <div class="product-info-line">
                                    <span class="product-info-label">Nombre Producto:</span>
                                    <span class="product-info-value"><?php echo $item['nombre_prod']; ?></span>
                                </div>
                                <div class="product-info-line">
                                    <span class="product-info-label">Precio Unitario:</span>
                                    <span class="product-info-value"><?php echo number_format($item['precio_unitario'], 2); ?> €</span>
                                </div>
                                <div class="product-info-line">
                                    <span class="product-info-label">Unidades:</span>
                                    <span class="product-info-value"><?php echo $item['cantidad']; ?></span>
                                </div>
                                <div class="product-info-line">
                                    <span class="product-info-label">Subtotal:</span>
                                    <span class="product-info-value" style="font-weight:bold;"><?php echo number_format($item['subtotal'], 2); ?> €</span>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>

                <!-- Importe Total -->
                <div class="total-row">
                    <span class="total-label">Importe Total: <?php echo number_format($venta['total'], 2); ?> €</span>
                </div>

            </div>

            <!-- Botones de acción -->
            <div class="botones-finales">
                <div class="boton-salir">
                    <a href="ListadoVentasAdmin.php">Volver al listado</a>
                </div>
                <div class="boton-usuario">
                    <!-- Enlace dinámico al perfil del cliente -->
                    <a href="ModificarDatosCliente.php?id=<?php echo $venta['id_cliente']; ?>">Ir al usuario</a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
                <div class="container">
                    <div class="footer-content">
                        <div class="footer-logo-section">
                            <div class="logo-footer">
                                <img src="../imagenes/footer.png" alt="Logo Metalful">
                            </div>
                            <div class="redes">
                                <a href="https://www.instagram.com/metalfulsansl/" target="_blank" class="instagram-link">
                                    <svg viewBox="0 0 24 24" fill="white">
                                        <path d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <div class="footer-links">
                            <div class="contacto-footer">
                                <h3>Contacto</h3>
                                <div class="contacto-item">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                    </svg>
                                    <a href="https://maps.google.com" target="_blank">Extrarradio Cortijo la Purisima, 2P, 18004 Granada</a>
                                </div>

                                <div class="contacto-item">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                                    </svg>
                                    <a href="tel:652921960">652 921 960</a>
                                </div>

                                <div class="contacto-item">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                    </svg>
                                    <a href="mailto:metalfulsan@gmail.com">metalfulsan@gmail.com</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="footer-bottom">
                        <div class="politica-legal">
                            <a href="#aviso-legal">Aviso Legal</a>
                            <span>•</span>
                            <a href="#privacidad">Política de Privacidad</a>
                            <span>•</span>
                            <a href="#cookies">Política de Cookies</a>
                        </div>
                    </div>
                </div>
        </footer>
    </div>
</body>
</html>