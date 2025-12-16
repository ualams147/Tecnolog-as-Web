<?php
// 1. IMPORTANTE: Primero incluimos el archivo de funciones (que inicia la sesión y carga el idioma)
include 'CabeceraFooter.php'; 

include 'conexion.php';

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

    // 3. CONSULTA DE VARIANTES (Para saber qué colores y medidas existen de este modelo)
    $stmt_var = $conn->prepare("SELECT id, color, medidas FROM productos WHERE referencia = :ref");
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
<html lang="<?php echo isset($_SESSION['idioma']) ? $_SESSION['idioma'] : 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producto['nombre']); ?> - Metalistería Fulsan</title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/infoProducto.css">
    
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
        
        <?php sectionheader(3); ?>

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
                    <img id="prod-img"
                         src="<?php echo htmlspecialchars($producto['imagen_url']); ?>"
                         alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                         onerror="this.src='https://via.placeholder.com/600x400?text=Imagen+No+Disponible'">
                </div>

                <div class="product-details-col">
                    
                    <div class="detail-group">
                        <h3><?php echo $lang['info_detalles']; ?></h3>
                        <p class="detail-text" id="prod-desc"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                    </div>

                    <div class="detail-group price-group">
                        <h3><?php echo $lang['info_precio']; ?></h3>
                        
                        <div style="display: flex; flex-direction: column; justify-content: center;">
                            <div>
                                <span class="price-value" id="prod-price"><?php echo number_format($producto['precio'], 2); ?></span>€
                            </div>
                            <small style="font-size: 14px; color: #777; margin-top: 2px;">
                                (<span id="prod-price-sin-iva"><?php echo number_format($producto['precio'] / 1.21, 2); ?></span>€ <?php echo $lang['info_sin_iva']; ?>)
                            </small>
                        </div>
                    </div>

                    <form action="agregarproducto.php" method="POST" id="form-carrito">
                        
                        <input type="hidden" name="id_producto" id="input-id-producto" value="<?php echo $producto['id']; ?>">

                       <div class="detail-group quantity-group">
                            <h3><?php echo $lang['info_unidades']; ?></h3>
                            
                            <div class="qty-custom-container" style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                                
                                <div class="qty-selector">
                                    <button type="button" class="qty-btn btn-menos">-</button>
                                    
                                    <input type="number" 
                                           name="cantidad" 
                                           id="input-cantidad" 
                                           value="1" 
                                           min="1" 
                                           class="qty-input"
                                           style="width: 50px; text-align: center; border: none; background: transparent; font-weight: bold; font-size: 18px; color: #333; margin: 0 5px; outline: none; padding: 0;">
                                    
                                    <button type="button" class="qty-btn btn-mas">+</button>
                                </div>

                                <a href="productomedida.php?categoria=<?php echo $producto['id_categoria']; ?>" class="btn-personaliza-pill">
                                    <?php echo $lang['info_btn_personaliza']; ?>
                                </a>
                            </div>
                        </div>

                        <div class="selectors-container">
                            <div class="custom-select-wrapper">
                                <label style="display:block; margin-bottom:5px; font-weight:600;"><?php echo $lang['info_lbl_color']; ?></label>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <select id="select-color" class="custom-select" style="flex: 1;">
                                        <?php foreach ($colores_disponibles as $color): ?>
                                            <option value="<?php echo $color; ?>" <?php echo ($color == $producto['color']) ? 'selected' : ''; ?>>
                                                <?php echo ucfirst($color); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <img id="img-color-preview" 
                                         src="" 
                                         onerror="this.style.display='none'" 
                                         style="width: 50px; height: 50px; object-fit: contain; border: 1px solid #ddd; border-radius: 5px; display: none;">
                                </div>
                            </div>

                            <div class="custom-select-wrapper">
                                <label style="display:block; margin-bottom:5px; font-weight:600;"><?php echo $lang['info_lbl_medidas']; ?></label>
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
                            <?php echo $lang['info_btn_add']; ?>
                        </button>
                    </form>

                </div>
            </div>
        </main>

        <?php sectionfooter(); ?>
    </div>

    <script src="js/auth.js"></script>
    <script src="js/infoProductos.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectColor = document.getElementById('select-color');
            const imgPreview = document.getElementById('img-color-preview');

            // Función que actualiza la imagen
            function actualizarImagenColor() {
                const val = selectColor.value;
                if (val) {
                    // Usamos trim() por si acaso, pero respetamos tildes (Marrón)
                    // Ruta relativa: imagenes/colorNombre.png
                    imgPreview.src = 'imagenes/color' + val.trim() + '.png';
                    imgPreview.style.display = 'block';
                } else {
                    imgPreview.style.display = 'none';
                }
            }

            // 1. Ejecutar al cambiar la opción
            selectColor.addEventListener('change', actualizarImagenColor);

            // 2. Ejecutar al cargar la página (para mostrar el color inicial si lo hay)
            actualizarImagenColor();
        });
    </script>
</body>
</html>