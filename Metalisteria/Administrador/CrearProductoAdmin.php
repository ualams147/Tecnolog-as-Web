<?php
include 'conexion.php';

// 1. PROCESAR EL FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recoger datos del formulario
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['detalles'];
    $medidas = $_POST['tamanos'];
    $id_categoria = $_POST['categoria']; 
    
    // NUEVO: Recoger el stock (si está vacío ponemos 0)
    $stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;
    
    $color = isset($_POST['colores']) ? $_POST['colores'] : '';

    // Valores por defecto
    $referencia = 'REF-' . rand(1000, 9999); 
    $id_material = 1; // 1 = Aluminio (Por defecto)

    // Lógica de Imagen
    $ruta_imagen = '../imagenes/producto-sin-imagen.png'; 
    
    if (isset($_FILES['nueva_imagen']) && $_FILES['nueva_imagen']['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo = basename($_FILES['nueva_imagen']['name']);
        $ruta_destino = "../imagenes/" . $nombre_archivo;
        
        if (move_uploaded_file($_FILES['nueva_imagen']['tmp_name'], $ruta_destino)) {
            $ruta_imagen = $ruta_destino;
        }
    }

    try {
        // Insertar en la Base de Datos
        $sql = "INSERT INTO productos (referencia, nombre, descripcion, precio, color, medidas, imagen_url, id_material, id_categoria, stock) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        // Fíjate que $stock es la última variable del array
        $stmt->execute([$referencia, $nombre, $descripcion, $precio, $color, $medidas, $ruta_imagen, $id_material, $id_categoria, $stock]);
        
        // Redirigir al listado (admin.php) si todo sale bien
        header("Location: ListadoProductosAdmin.php");
        exit;
        
    } catch(PDOException $e) {
        echo "<script>alert('Error al guardar: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto - Metalistería Fulsan</title>
    <link rel="stylesheet" href="../css/stylesCrearProductosAdmin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="CrearProductoAdmin">
        <!-- Header -->
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
            <a href="ListadoProductosAdmin.php" class="flecha-circular">&#8592;</a>
            <h1 class="titulo-principal">Nuevo Producto</h1>
        </div>

        <!-- Main Content -->
        <div class="container main-container">
            
            <!-- FORMULARIO CONECTADO -->
            <form method="POST" enctype="multipart/form-data" class="product-card">
                
                <!-- Columna Imagen -->
                <div class="image-column">
                    <div class="image-placeholder" style="overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <img id="preview-img" src="" style="display: none; max-width: 100%; max-height: 100%;">
                    </div>
                    
                    <input type="file" id="input-imagen" name="nueva_imagen" accept="image/*" style="display: none;" onchange="verImagen(event)">
                    
                    <div class="boton-cambiar-imagen" onclick="document.getElementById('input-imagen').click()">
                        <p>Establecer Imagen</p>
                    </div>
                </div>

                <!-- Columna Formulario -->
                <div class="form-column">
                    
                    <div class="form-group">
                        <label class="form-label" for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="precio">Precio:</label>
                        <div class="price-wrapper">
                            <input type="number" step="0.01" id="precio" name="precio" class="form-input">
                            <span class="currency-symbol">€</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="detalles">Detalles:</label>
                        <input type="text" id="detalles" name="detalles" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Colores disponibles:</label>
                        <div class="checkbox-group">
                            <label class="checkbox-label"><input type="checkbox" name="colores" value="Blanco"> Blanco</label>
                            <label class="checkbox-label"><input type="checkbox" name="colores" value="Plata"> Plata</label>
                            <label class="checkbox-label"><input type="checkbox" name="colores" value="Marrón"> Marrón</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="tamanos">Tamaños disponibles:</label>
                        <input type="text" id="tamanos" name="tamanos" class="form-input">
                    </div>

                    <!-- DESPLEGABLE DE CATEGORÍAS -->
                    <div class="form-group">
                        <label class="form-label" for="categoria">Categoría:</label>
                        <select id="categoria" name="categoria" class="form-input" style="cursor: pointer;">
                            <option value="1">Ventanas</option>
                            <option value="2">Balcones</option>
                            <option value="3">Rejas</option>
                            <option value="4">Escaleras</option>
                            <option value="5">Barandillas</option>
                            <option value="6">Pérgolas</option>
                        </select>
                    </div>

                    <!-- NUEVO: CAMPO DE STOCK -->
                    <div class="form-group">
                        <label class="form-label" for="stock">Stock:</label>
                        <input type="number" id="stock" name="stock" class="form-input" placeholder="Cantidad en stock" min="0">
                    </div>

                    <!-- Botones de acción -->
                    <div class="botones-finales">
                        <div class="boton-salir">
                            <a href="ListadoProductosAdmin.php">Salir</a>
                        </div>
                        
                        <button type="submit" class="boton-crear" style="border: 2px solid rgba(41, 54, 97, 0.6); font-family: inherit;">
                            <p>Crear producto</p>
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
                        <a href="#aviso-legal">Aviso Legal</a> • <a href="#privacidad">Privacidad</a> • <a href="#cookies">Cookies</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        function verImagen(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('preview-img');
                    img.src = e.target.result;
                    img.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="colores"]');
            checkboxes.forEach(box => {
                box.addEventListener('change', function() {
                    if (this.checked) {
                        checkboxes.forEach(otherBox => {
                            if (otherBox !== this) otherBox.checked = false;
                        });
                    }
                });
            });
        });
    </script>

</body>
</html>