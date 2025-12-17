<?php
include 'conexion.php';

// 1. VERIFICAR QUE RECIBIMOS UN ID
if (!isset($_GET['id'])) {
    header("Location: listadoproductosadmin.php"); 
    exit;
}

$id = $_GET['id'];
$mensaje = "";

// 2. PROCESAR EL FORMULARIO (CUANDO SE PULSA "MODIFICAR")
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    // --- CAMBIO 1: Recogemos la categoría del formulario ---
    $id_categoria = $_POST['id_categoria'];
    // -------------------------------------------------------
    $precio = $_POST['precio'];
    $descripcion = $_POST['detalles'];
    $medidas = $_POST['tamanos'];
    
    $color = $_POST['color'] ?? '';

    // Lógica para la imagen
    $ruta_imagen = $_POST['imagen_actual']; 

    if (isset($_FILES['nueva_imagen']) && $_FILES['nueva_imagen']['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo = basename($_FILES['nueva_imagen']['name']);
        $ruta_destino = "imagenes/" . $nombre_archivo; 
        
        if (move_uploaded_file($_FILES['nueva_imagen']['tmp_name'], $ruta_destino)) {
            $ruta_imagen = $ruta_destino;
        }
    }

    // Actualizamos la base de datos
    try {
        // --- CAMBIO 2: Añadimos id_categoria a la sentencia SQL ---
        $sql = "UPDATE productos SET nombre=?, id_categoria=?, precio=?, descripcion=?, medidas=?, color=?, imagen_url=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        // Importante: El orden de las variables debe coincidir con los ?
        $stmt->execute([$nombre, $id_categoria, $precio, $descripcion, $medidas, $color, $ruta_imagen, $id]);
        // ----------------------------------------------------------
        
        $mensaje = "¡Producto actualizado correctamente!";
        
        header("Location: listadoproductosadmin.php"); 
        exit;
        
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

// --- CAMBIO 3: OBTENER TODAS LAS CATEGORÍAS PARA EL DESPLEGABLE ---
// Esto nos sirve para rellenar el <select> más abajo
$stmt_cat = $conn->query("SELECT * FROM categorias ORDER BY nombre ASC");
$categorias = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
// ------------------------------------------------------------------

// --- LÓGICA DE VISUALIZACIÓN DE IMAGEN ---
$ruta_bd = $producto['imagen_url'];
$ruta_foto = str_replace('../', '', $ruta_bd); 

if (empty($ruta_foto)) {
    $ruta_foto = 'imagenes/producto-sin-imagen.png';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Producto - Metalistería Fulsan</title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="stylesheet" href="css/administrador.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="ModificarProductoAdmin">
        <header class="cabecera">
            <div class="container">
                <div class="logo-main">
                    <a href="indexadmin.php" class="logo-main">
                        <img src="imagenes/logo.png" alt="Logo Metalful">
                        <div class="logo-text">
                            <span> Metalisteria</span>
                            <strong>Fulsan</strong>
                        </div>
                    </a>
                </div>
                
                <nav class="nav-bar">
                    <a href="listadoventasadmin.php">Ventas</a>
                    <a href="listadoproductosadmin.php" style="font-weight:bold; border-bottom: 2px solid currentColor;">Productos</a> 
                    <a href="listadoclientesadmin.php">Clientes</a>
                </nav>

                <div class="log-out">
                    <a href="index.php">Cerrar Sesión</a>
                </div>
            </div>
        </header>

        <div class="titulo-section">
            <div class="degradado"></div>
            <div class="recuadro-fondo"></div> 
            
            <a href="listadoproductosadmin.php" class="flecha-circular" style="display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18L9 12L15 6" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>

            <h1 class="titulo-principal" style="font-weight: bold;">Modificar Producto</h1>
        </div>

        <div class="container main-container">
            
            <?php if(!empty($mensaje)): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; width: 100%; text-align: center;">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            
            <div id="mensaje-error-js" class="alerta-error" style="display: none;"></div>

            <form id="form-modificar" method="POST" enctype="multipart/form-data" class="product-card">
                
                <div class="image-column">
                    <div class="image-placeholder" style="overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <img id="preview-img" src="<?php echo $ruta_foto; ?>" alt="Producto" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.src='imagenes/producto-sin-imagen.png'">
                    </div>
                    
                    <input type="file" id="input-imagen" name="nueva_imagen" style="display: none;" accept="image/*" onchange="mostrarPrevisualizacion(event)">
                    
                    <input type="hidden" name="imagen_actual" value="<?php echo $producto['imagen_url']; ?>">

                    <div class="boton-cambiar-imagen" onclick="document.getElementById('input-imagen').click()">
                        <p>Cambiar Imagen</p>
                    </div>
                </div>

                <div class="form-column">
                    
                    <div class="form-group">
                        <label class="form-label" for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" class="form-input" value="<?php echo $producto['nombre']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="categoria">Categoría:</label>
                        <select name="id_categoria" id="categoria" class="custom-select" required>
                            <option value="">-- Seleccione categoría --</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo ($cat['id'] == $producto['id_categoria']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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
                        <label class="form-label" for="color">Color:</label>
                        <input type="text" 
                            id="color" 
                            name="color" 
                            class="form-input" 
                            value="<?php echo htmlspecialchars($producto['color']); ?>" 
                            placeholder="Ej: Blanco, Rojo, Madera, Plata..." >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tamaños disponibles:</label>
                        
                        <div id="tamanos-container" class="tamanos-container"></div>
                        
                        <div class="input-group-medidas">
                            <div class="medida-input-wrapper">
                                <input type="number" id="input-ancho" class="form-input input-corto" placeholder="Ancho" min="30">
                                <span class="medida-label">cm</span>
                            </div>

                            <span class="separador-x">✕</span>

                            <div class="medida-input-wrapper">
                                <input type="number" id="input-alto" class="form-input input-corto" placeholder="Alto" min="30">
                                <span class="medida-label">cm</span>
                            </div>
                            
                            <button type="button" id="btn-add-tamano" class="btn-add-medida">
                                Añadir
                            </button>
                        </div>

                        <p class="nota-medidas">* La medida mínima es de 30 cm.</p>
                        
                        <input type="hidden" id="tamanos-final" name="tamanos" value="<?php echo htmlspecialchars($producto['medidas'] ?? ''); ?>">
                    </div>

                    <div class="botones-finales">
                        <div class="boton-salir">
                            <a href="javascript:void(0);" onclick="confirmarSalida()">Salir</a>
                        </div>
                        
                        <div class="boton-modificar">
                            <button type="button" name="actualizar" onclick="confirmarModificacion()">
                                Guardar cambios
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>

        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-logo-section">
                        <div class="logo-footer">
                            <img src="imagenes/footer.png" alt="Logo Metalful">
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
                        <a href="#">Aviso Legal</a> • <a href="#">Privacidad</a> • <a href="#">Cookies</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- LÓGICA DE MEDIDAS ---
            const tamanosContainer = document.getElementById('tamanos-container');
            const inputAncho = document.getElementById('input-ancho');
            const inputAlto = document.getElementById('input-alto');
            const btnAdd = document.getElementById('btn-add-tamano');
            const hiddenInput = document.getElementById('tamanos-final');

            function createTag(text) {
                const tag = document.createElement('span');
                tag.classList.add('tamano-chip');
                tag.innerHTML = `${text} <span class="remove-tag">✕</span>`;
                
                tag.querySelector('.remove-tag').addEventListener('click', () => {
                    tag.remove();
                    updateHiddenInput();
                });
                tamanosContainer.appendChild(tag);
            }

            function updateHiddenInput() {
                const tags = Array.from(tamanosContainer.querySelectorAll('.tamano-chip'))
                                                .map(t => t.innerText.replace('✕', '').trim());
                hiddenInput.value = tags.join(', ');
            }

            function addMedida() {
                const ancho = parseInt(inputAncho.value);
                const alto = parseInt(inputAlto.value);
                const errorDiv = document.getElementById('mensaje-error-js');

                function mostrarError(mensaje) {
                    errorDiv.textContent = mensaje;
                    errorDiv.style.display = 'block';
                    errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => { errorDiv.style.display = 'none'; }, 5000);
                }

                function ocultarError() {
                    errorDiv.style.display = 'none';
                }

                if (isNaN(ancho) || isNaN(alto)) {
                    mostrarError("⚠️ Por favor, introduce números válidos en Ancho y Alto.");
                    return;
                }

                if (ancho < 30 || alto < 30) {
                    mostrarError("⚠️ Las medidas no pueden ser menores de 30 cm.");
                    return;
                }

                const nuevaMedida = `${ancho}x${alto}`;
                const actuales = hiddenInput.value.split(', ').map(t => t.trim());
                
                if (actuales.includes(nuevaMedida)) {
                    mostrarError("⚠️ Esta medida ya existe en la lista.");
                    return;
                }

                ocultarError();
                createTag(nuevaMedida);
                updateHiddenInput();
                inputAncho.value = '';
                inputAlto.value = '';
                inputAncho.focus();
            }

            btnAdd.addEventListener('click', addMedida);

            inputAlto.addEventListener('keyup', (e) => {
                if (e.key === 'Enter') addMedida();
            });

            if (hiddenInput.value) {
                const existentes = hiddenInput.value.split(',');
                existentes.forEach(medida => {
                    if (medida.trim()) createTag(medida.trim());
                });
            }
        });

        // --- Previsualización de imagen ---
        function mostrarPrevisualizacion(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-img').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function confirmarModificacion() {
            const formulario = document.getElementById('form-modificar');
            if (!formulario.checkValidity()) {
                formulario.reportValidity();
                return; 
            }

            Swal.fire({
                title: 'Guardar cambios',
                text: "¿Estás seguro de que quieres actualizar los datos de este producto?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#293661', 
                cancelButtonColor: '#6c757d', 
                confirmButtonText: 'Sí, guardar cambios',
                cancelButtonText: 'No, seguir editando',
                background: '#fff',
                customClass: {
                    popup: 'mi-alerta-redondeada'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    formulario.submit();
                }
            });
        }

        function confirmarSalida() {
            Swal.fire({
                title: '¿Salir sin guardar?',
                text: "Se perderán los cambios que no hayas guardado.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',     
                cancelButtonColor: '#293661',   
                confirmButtonText: 'Sí, salir',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'swal2-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'listadoproductosadmin.php';
                }
            });
        }
    </script>
</body>
</html>