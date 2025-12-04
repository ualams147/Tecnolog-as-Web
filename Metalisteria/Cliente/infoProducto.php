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
                    
                    <a href="carrito.php">Carrito</a>
                    
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

                    <form action="AgregarProducto.php" method="POST" id="form-carrito">
                        
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
               </div>
        </footer>
    </div>

    <script src="../js/auth.js"></script>
    <script src="../js/infoProductos.js"></script>
</body>
</html>