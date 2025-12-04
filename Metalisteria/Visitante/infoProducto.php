<?php
session_start();
include '../conexion.php'; 

// 1. VALIDACIÓN: ¿Nos han pasado un ID?
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: productos.php");
    exit;
}

$id_producto = $_GET['id'];

try {
    // 2. CONSULTA PRINCIPAL: Datos del producto actual
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id = :id");
    $stmt->execute([':id' => $id_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        die("Producto no encontrado.");
    }

    // 3. CONSULTA DE VARIANTES (Para los desplegables)
    $stmt_var = $conn->prepare("SELECT id, color, medidas, stock FROM productos WHERE referencia = :ref");
    $stmt_var->execute([':ref' => $producto['referencia']]);
    $todas_las_variantes = $stmt_var->fetchAll(PDO::FETCH_ASSOC);

    // Extraemos colores únicos y medidas únicas para los <select>
    $colores_disponibles = array_unique(array_column($todas_las_variantes, 'color'));
    $medidas_disponibles = array_unique(array_column($todas_las_variantes, 'medidas'));

} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}

// 4. LÓGICA DEL CONTADOR DEL CARRITO (Para el menú)
$total_items = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $total_items += $item['cantidad'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producto['nombre']); ?> - Metalistería Fulsan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/infoProducto.css">
    
    <style>
        /* Chrome, Safari, Edge, Opera */
        .qty-input::-webkit-outer-spin-button,
        .qty-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        .qty-input {
            -moz-appearance: textfield;
        }
        
        /* Quitar borde azul al seleccionar */
        .qty-input:focus {
            outline: none;
        }
    </style>

    <div id="data-json" 
         data-variantes='<?php echo json_encode($todas_las_variantes); ?>' 
         data-actual='<?php echo $producto['id']; ?>'
         style="display:none;">
    </div>
</head>
<body>
    <div class="visitante-producto-detalle">
        
        <header class="cabecera">
            <div class="container">
                <div class="logo-main">
                    <a href="index.php" class="logo-link">
                        <img src="../imagenes/logo.png" alt="Logo Metalful">
                        <div class="logo-text">
                            <span>Metalistería</span>
                            <strong>Fulsan</strong>
                        </div>
                    </a>
                </div>

                <nav class="nav-bar">
                    <a href="conocenos.php">Conócenos</a>
                    <a href="productos.php" class="activo">Productos</a>
                    
                    <a href="carrito.php">
                        Carrito 
                        <?php if($total_items > 0): ?>
                            <span style="background: #e74c3c; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; position: relative; top: -2px;">
                                <?php echo $total_items; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    
                    <a href="IniciarSesion.php" id="link-login">Iniciar Sesión</a>
                </nav>

                <div class="sign-in" id="box-registro">
                    <a href="registro.php" id="link-registro">Registrarse</a>
                </div>
            </div>
        </header>

        <section class="product-hero">
            <div class="container hero-content">
                <a href="productos.php" class="btn-back">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </a>
                <h1 class="product-title-main"><?php echo htmlspecialchars($producto['nombre']); ?></h1>
            </div>
        </section>

        <main class="product-main container">
            <div class="product-card">
                
                <div class="product-image-col">
                    <img src="../<?php echo htmlspecialchars($producto['imagen_url']); ?>" 
                         alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                         onerror="this.src='https://via.placeholder.com/600x400?text=Imagen+No+Disponible'">
                </div>

                <div class="product-details-col">
                    
                    <div class="detail-group">
                        <h3>Detalles:</h3>
                        <p class="detail-text"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                        <p style="color: #666; font-size: 0.9em; margin-top: 5px;">Ref: <?php echo $producto['referencia']; ?></p>
                    </div>

                    <div class="detail-group price-group">
                        <h3>Precio:</h3>
                        <span class="price-value"><?php echo number_format($producto['precio'], 2); ?>€</span>
                    </div>

                    <form action="AgregarCarrito.php" method="POST" id="form-carrito">
                        
                        <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">

                        <div class="detail-group quantity-group">
                            <h3>Unidades:</h3>
                            <div class="qty-selector">
                                <button type="button" class="qty-btn btn-menos">-</button>
                                
                                <input type="number" 
                                       name="cantidad" 
                                       id="input-cantidad" 
                                       value="1" 
                                       min="1" 
                                       max="<?php echo $producto['stock']; ?>"
                                       class="qty-input"
                                       style="width: 50px; text-align: center; border: none; background: transparent; font-weight: bold; font-size: 18px; color: #333; margin: 0 5px; outline: none; padding: 0;">
                                
                                <button type="button" class="qty-btn btn-mas">+</button>
                            </div>
                            <span style="font-size: 0.8em; color: #666; margin-left: 10px;">
                                (Stock: <?php echo $producto['stock']; ?>)
                            </span>
                        </div>

                        <div class="selectors-container">
                            <div class="custom-select-wrapper">
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Color:</label>
                                <select id="select-color" class="custom-select">
                                    <?php foreach ($colores_disponibles as $color): ?>
                                        <option value="<?php echo $color; ?>" <?php echo ($color == $producto['color']) ? 'selected' : ''; ?>>
                                            <?php echo ucfirst($color); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="custom-select-wrapper">
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Medidas:</label>
                                <select id="select-medidas" class="custom-select">
                                    <?php foreach ($medidas_disponibles as $medida): ?>
                                        <option value="<?php echo $medida; ?>" <?php echo ($medida == $producto['medidas']) ? 'selected' : ''; ?>>
                                            <?php echo $medida; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn-add-cart" style="display: block; width: 100%; text-align: center; text-decoration: none; border:none; font-family: inherit; font-size: 16px; cursor: pointer;">
                            Añadir al carrito
                        </button>
                    </form>

                </div>
            </div>
        </main>

        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-logo-section">
                        <div class="logo-footer">
                            <img src="../imagenes/footer.png" alt="Logo Metalful">
                        </div>
                        <div class="redes">
                            <a href="https://www.instagram.com/metalfulsansl/" target="_blank" class="instagram-link">
                                <svg viewBox="0 0 24 24" fill="white"><path d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z"/></svg>
                            </a>
                        </div>
                    </div>
                    <div class="footer-links">
                        <div class="enlaces-rapidos">
                            <h3>Enlaces rápidos</h3>
                            <ul>
                                <li><a href="conocenos.php">Conócenos</a></li>
                                <li><a href="productos.php">Productos</a></li>
                                <li><a href="IniciarSesion.php">Iniciar Sesión</a></li>
                            </ul>
                        </div>
                        <div class="contacto-footer">
                            <h3>Contacto</h3>
                            <ul>
                                <li><svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg><a href="#">Granada, España</a></li>
                                <li><svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg><a href="tel:652921960">652 921 960</a></li>
                                <li><svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg><a href="mailto:metalfulsan@gmail.com">metalfulsan@gmail.com</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom">
                    <div class="politica-legal">
                        <a href="aviso-legal.php">Aviso Legal</a><span>•</span><a href="privacidad.php">Política de Privacidad</a><span>•</span><a href="cookies.php">Política de Cookies</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="../js/auth.js"></script>
    <script src="../js/infoProductos.js"></script>
</body>
</html>