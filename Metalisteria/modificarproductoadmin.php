<?php
include 'conexion.php';

// 1. VERIFICAR QUE RECIBIMOS UN ID
if (!isset($_GET['id'])) {
    // Si no hay ID, redirigimos al listado para evitar errores
    header("Location: ListadoProductosAdmin.php"); 
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
        // Ruta destino corregida (sin ../)
        $ruta_destino = "imagenes/" . $nombre_archivo; 
        
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
        
        // Redirigir tras guardar (Ruta corregida)
        header("Location: ListadoProductosAdmin.php"); 
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

// --- LÓGICA DE VISUALIZACIÓN DE IMAGEN ---
// Limpiamos la ruta que viene de la BD por si tiene ".." antiguos
$ruta_bd = $producto['imagen_url'];
$ruta_foto = str_replace('../', '', $ruta_bd); 

// Si está vacía o no existe, usamos la de por defecto (sin ../)
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
                    <a href="indexAdmin.php" class="logo-main">
                        <img src="imagenes/logo.png" alt="Logo Metalful">
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
                    <a href="index.php">Cerrar Sesión</a>
                </div>
            </div>
        </header>

        <div class="titulo-section">
            <div class="degradado"></div>
            <div class="recuadro-fondo"></div> 
            <a href="ListadoProductosAdmin.php" class="flecha-circular">&#8592;</a>
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
                        <label class="form-label">Tamaños disponibles:</label>
                        
                        <div id="tamanos-container" class="tamanos-container">
                            </div>
                        
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
            // --- 1. Lógica de Checkboxes de Color (Igual que antes) ---
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

            // --- LÓGICA DE MEDIDAS (Actualizada) ---
            const tamanosContainer = document.getElementById('tamanos-container');
            const inputAncho = document.getElementById('input-ancho');
            const inputAlto = document.getElementById('input-alto');
            const btnAdd = document.getElementById('btn-add-tamano');
            const hiddenInput = document.getElementById('tamanos-final');

            // Función crear etiqueta
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

            // Actualizar el input oculto para la BD
            function updateHiddenInput() {
                const tags = Array.from(tamanosContainer.querySelectorAll('.tamano-chip'))
                                        .map(t => t.innerText.replace('✕', '').trim());
                hiddenInput.value = tags.join(', ');
            }

            // VALIDACIÓN Y AÑADIDO
            // --- 3. Lógica VALIDACIÓN Y AÑADIDO (Modificada) ---
            function addMedida() {
                const ancho = parseInt(inputAncho.value);
                const alto = parseInt(inputAlto.value);
                const errorDiv = document.getElementById('mensaje-error-js');

                // Función auxiliar para mostrar el error
                function mostrarError(mensaje) {
                    errorDiv.textContent = mensaje;
                    errorDiv.style.display = 'block';
                    // Opcional: Hacer scroll hacia arriba para ver el mensaje si la pantalla es pequeña
                    errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Opcional: Ocultar automáticamente tras 5 segundos
                    setTimeout(() => {
                        errorDiv.style.display = 'none';
                    }, 5000);
                }

                // Función para ocultar error (si todo sale bien)
                function ocultarError() {
                    errorDiv.style.display = 'none';
                }

                // 1. Validar que sean números
                if (isNaN(ancho) || isNaN(alto)) {
                    mostrarError("⚠️ Por favor, introduce números válidos en Ancho y Alto.");
                    return;
                }

                // 2. RESTRICCIÓN: Mínimo 30cm
                if (ancho < 30 || alto < 30) {
                    mostrarError("⚠️ Las medidas no pueden ser menores de 30 cm.");
                    return;
                }

                // 3. Formato estandarizado
                const nuevaMedida = `${ancho}x${alto}`;

                // 4. Comprobar duplicados
                const actuales = hiddenInput.value.split(', ').map(t => t.trim());
                if (actuales.includes(nuevaMedida)) {
                    mostrarError("⚠️ Esta medida ya existe en la lista.");
                    return;
                }

                // Si llegamos aquí, todo está BIEN:
                ocultarError(); // Quitamos el error si lo había
                createTag(nuevaMedida);
                updateHiddenInput();
                inputAncho.value = '';
                inputAlto.value = '';
                inputAncho.focus();
            }

            btnAdd.addEventListener('click', addMedida);

            // Permitir añadir pulsando Enter en el campo de Alto
            inputAlto.addEventListener('keyup', (e) => {
                if (e.key === 'Enter') addMedida();
            });

            // Cargar datos iniciales (desde PHP)
            if (hiddenInput.value) {
                const existentes = hiddenInput.value.split(',');
                existentes.forEach(medida => {
                    if (medida.trim()) createTag(medida.trim());
                });
            }
        });

        // --- 3. Previsualización de imagen ---
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
            // 1. Seleccionamos el formulario
            const formulario = document.getElementById('form-modificar');

            // 2. Comprobamos si los campos requeridos están llenos (HTML5 validation)
            if (!formulario.checkValidity()) {
                // Si falta algo, dejamos que el navegador muestre los errores rojos
                formulario.reportValidity();
                return; 
            }

            // 3. Si todo está relleno, lanzamos la alerta
            Swal.fire({
                title: 'Guardar cambios',
                text: "¿Estás seguro de que quieres actualizar los datos de este producto?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#293661', // Tu azul corporativo para SI
                cancelButtonColor: '#6c757d', // Gris para NO (seguir editando)
                confirmButtonText: 'Sí, guardar cambios',
                cancelButtonText: 'No, seguir editando',
                background: '#fff',
                // Estilo opcional para que quede más integrado
                customClass: {
                    popup: 'mi-alerta-redondeada'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // 4. Si dice que SÍ, enviamos el formulario manualmente
                    formulario.submit();
                }
                // Si dice que NO, no hacemos nada y el cuadro se cierra solo.
            });
        }

        // --- NUEVA FUNCIÓN PARA EL BOTÓN SALIR ---
        function confirmarSalida() {
            Swal.fire({
                title: '¿Salir sin guardar?',
                text: "Se perderán los cambios que no hayas guardado.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',     // Rojo para indicar "Salir/Peligro"
                cancelButtonColor: '#293661',   // Azul para "Me quedo"
                confirmButtonText: 'Sí, salir',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'swal2-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Si confirma, entonces sí redirigimos manualmente
                    window.location.href = 'ListadoProductosAdmin.php';
                }
            });
        }

    </script>

</body>
</html>