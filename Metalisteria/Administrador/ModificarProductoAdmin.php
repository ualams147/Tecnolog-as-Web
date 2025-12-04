<?php
include 'conexion.php';

// 1. VERIFICAR QUE RECIBIMOS UN ID
if (!isset($_GET['id'])) {
    // Si no hay ID, redirigimos al listado para evitar errores
    header("Location: ListadoProductosAdmin.php"); // O admin.php si usas PHP
    exit;
}

$id = $_GET['id'];
$mensaje = "";

// 2. PROCESAR EL FORMULARIO (CUANDO SE PULSA "MODIFICAR")
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['detalles'];
    $medidas = $_POST['tamanos'];
    
    // Recogemos el color (puede venir vacío si no marcan nada)
    $color = isset($_POST['colores']) ? $_POST['colores'] : '';

    // Lógica para la imagen
    $ruta_imagen = $_POST['imagen_actual']; // Por defecto, mantenemos la antigua

    // Si suben una nueva foto...
    if (isset($_FILES['nueva_imagen']) && $_FILES['nueva_imagen']['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo = basename($_FILES['nueva_imagen']['name']);
        $ruta_destino = "../imagenes/" . $nombre_archivo; // Asegúrate de que la carpeta 'img' existe
        
        if (move_uploaded_file($_FILES['nueva_imagen']['tmp_name'], $ruta_destino)) {
            $ruta_imagen = $ruta_destino;
        }
    }

    // Actualizamos la base de datos
    try {
        $sql = "UPDATE productos SET nombre=?, precio=?, descripcion=?, medidas=?, color=?, imagen_url=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nombre, $precio, $descripcion, $medidas, $color, $ruta_imagen, $id]);
        
        $mensaje = "¡Producto actualizado correctamente!";
        
        // Opcional: Redirigir tras guardar
        // header("Location: admin.php"); 
        
    } catch(PDOException $e) {
        $mensaje = "Error al guardar: " . $e->getMessage();
    }
}

// 3. OBTENER LOS DATOS ACTUALES DEL PRODUCTO
$sql = "SELECT * FROM productos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    echo "Producto no encontrado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Producto - Metalistería Fulsan</title>
    <link rel="stylesheet" href="stylesModificarProductosAdmin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="ModificarProductoAdmin">
        <!-- Header -->
        <header class="cabecera">
            <div class="container">
                <div class="logo-main">
                    <!-- CORRECCIÓN AQUÍ: Se ha completado el href y eliminado la etiqueta rota -->
                    <a href="indexAdmin.php" class="logo-main">
                        <img src="../imagenes/logo.png" alt="Logo Metalful">
                        <div class="logo-text">
                            <span> Metalisteria</span>
                            <strong>Fulsan</strong>
                        </div>
                    </a>
                </div>
                
                <nav class="nav-bar">
                    <a href="ListadoVentasAdmin.php">Ventas</a>
                    <a href="ListadoProductosAdmin.php" style="font-weight:bold; border-bottom: 2px solid currentColor;">Productos</a> 
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
            <!-- Flecha para volver (apunta a admin.php o tu listado) -->
            <a href="admin.php" class="flecha-circular">&#8592;</a>
            
            <!-- Mostramos la Referencia o el ID real -->
            <h1 class="titulo-principal"><?php echo $producto['referencia']; ?></h1>
        </div>

        <!-- Main Content -->
        <div class="container main-container">
            
            <!-- Mensaje de confirmación (si existe) -->
            <?php if(!empty($mensaje)): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; width: 100%; text-align: center;">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <!-- INICIO FORMULARIO (Importante: method="POST" y enctype para imágenes) -->
            <form method="POST" enctype="multipart/form-data" class="product-card">
                
                <!-- Columna Imagen -->
                <div class="image-column">
                    <div class="image-placeholder" style="overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <!-- Mostramos la imagen actual -->
                        <img id="preview-img" src="<?php echo $producto['imagen_url']; ?>" alt="Producto" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.src='img/sin-foto.jpg'">
                    </div>
                    
                    <!-- Input oculto para subir archivo -->
                    <input type="file" id="input-imagen" name="nueva_imagen" style="display: none;" accept="image/*" onchange="mostrarPrevisualizacion(event)">
                    
                    <!-- Input oculto para mantener la ruta vieja si no se cambia -->
                    <input type="hidden" name="imagen_actual" value="<?php echo $producto['imagen_url']; ?>">

                    <!-- Botón visual que activa el input -->
                    <div class="boton-cambiar-imagen" onclick="document.getElementById('input-imagen').click()">
                        <p>Cambiar Imagen</p>
                    </div>
                </div>

                <!-- Columna Formulario -->
                <div class="form-column">
                    
                    <div class="form-group">
                        <label class="form-label" for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" class="form-input" value="<?php echo $producto['nombre']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="precio">Precio:</label>
                        <div class="price-wrapper">
                            <input type="number" step="0.01" id="precio" name="precio" class="form-input" value="<?php echo $producto['precio']; ?>">
                            <span class="currency-symbol">€</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="detalles">Detalles:</label>
                        <textarea id="detalles" name="detalles" class="form-input" rows="4"><?php echo $producto['descripcion']; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Color:</label>
                        <div class="checkbox-group">
                            <!-- PHP comprueba si el color en BD es 'Blanco', 'Plata' o 'Marrón' y marca la casilla -->
                            <label class="checkbox-label">
                                <input type="checkbox" name="colores" value="Blanco" <?php if($producto['color'] == 'Blanco') echo 'checked'; ?>> Blanco
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="colores" value="Plata" <?php if($producto['color'] == 'Plata') echo 'checked'; ?>> Plata
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="colores" value="Marrón" <?php if($producto['color'] == 'Marrón') echo 'checked'; ?>> Marrón
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="tamanos">Tamaños disponibles:</label>
                        <input type="text" id="tamanos" name="tamanos" class="form-input" value="<?php echo $producto['medidas']; ?>">
                    </div>

                    <!-- Botones de acción DENTRO del formulario -->
                    <div class="botones-finales">
                        <div class="boton-salir">
                            <a href="admin.php">Salir</a>
                        </div>
                        
                        <!-- El botón de guardar debe ser un <button> o <input submit> -->
                        <button type="submit" class="boton-crear" style="border:none; font-family: inherit;">
                            <p>Guardar Cambios</p>
                        </button>
                    </div>

                </div>
            </form>
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
                            <a href="#" class="instagram-link">
                                <!-- SVG Instagram -->
                                <svg viewBox="0 0 24 24" fill="white"><path d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z"/></svg>
                            </a>
                        </div>
                    </div>

                    <div class="footer-links">
                        <div class="contacto-footer">
                            <h3>Contacto</h3>
                            <div class="contacto-item">
                                <span>Extrarradio Cortijo la Purisima, 2P, 18004 Granada</span>
                            </div>
                            <div class="contacto-item">
                                <span>652 921 960</span>
                            </div>
                            <div class="contacto-item">
                                <span>metalfulsan@gmail.com</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="footer-bottom">
                    <div class="politica-legal">
                        <a href="#">Aviso Legal</a> • <a href="#">Privacidad</a> • <a href="#">Cookies</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Script para selección única de Checkboxes y Previsualización -->
    <script>
        // 1. Selección única de color
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="colores"]');
            
            checkboxes.forEach(box => {
                box.addEventListener('change', function() {
                    if (this.checked) {
                        checkboxes.forEach(otherBox => {
                            if (otherBox !== this) {
                                otherBox.checked = false;
                            }
                        });
                    }
                });
            });
        });

        // 2. Previsualizar imagen al seleccionar
        function mostrarPrevisualizacion(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('preview-img');
                    img.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

</body>
</html>