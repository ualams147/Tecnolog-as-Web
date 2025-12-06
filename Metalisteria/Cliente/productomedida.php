<?php
session_start();
include '../conexion.php'; 

// 1. CONSULTA: Traemos todos los productos (variantes) para los desplegables
try {
    $sql = "SELECT DISTINCT referencia FROM productos ORDER BY referencia";
    $stmt = $conn->query($sql);
    $referencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Traemos todos los datos para llenar los desplegables
    $sql_all = "SELECT id, nombre, referencia, color, medidas, precio, imagen_url, descripcion, stock FROM productos ORDER BY referencia";
    $stmt_all = $conn->query($sql_all);
    $todos_productos = $stmt_all->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo "Error al cargar productos: " . $e->getMessage();
    die();
}

// Lógica del contador del menú
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
    <title>Producto a Medida - Metalistería Fulsan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/productos.css">
    
    <style>
        .producto-medida-container {
            min-height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
        }

        .medida-card {
            background: white;
            border-radius: 12px;
            padding: 50px 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            max-width: 600px;
            width: 100%;
        }

        .medida-title {
            text-align: center;
            font-size: 2.2em;
            font-weight: 700;
            color: #1a3a52;
            margin-bottom: 50px;
            font-family: 'Poppins', sans-serif;
        }

        .form-group-medida {
            margin-bottom: 30px;
        }

        .form-group-medida label {
            display: block;
            font-size: 1em;
            font-weight: 500;
            color: #1a3a52;
            margin-bottom: 12px;
            font-family: 'Poppins', sans-serif;
        }

        .form-group-medida select {
            width: 100%;
            padding: 16px 16px;
            border: 1px solid #d0d0d0;
            border-radius: 8px;
            font-size: 1em;
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            color: #555;
            cursor: pointer;
            transition: all 0.3s ease;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%231a3a52' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 20px;
            padding-right: 40px;
        }

        .form-group-medida select:hover {
            border-color: #a0d2ac;
            box-shadow: 0 0 8px rgba(160, 210, 172, 0.2);
        }

        .form-group-medida select:focus {
            outline: none;
            border-color: #a0d2ac;
            box-shadow: 0 0 12px rgba(160, 210, 172, 0.4);
        }

        .product-info-medida {
            background: #f5f5f5;
            border-radius: 8px;
            padding: 20px;
            margin-top: 40px;
            display: none;
        }

        .product-info-medida.active {
            display: block;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 0.95em;
        }

        .info-row strong {
            color: #1a3a52;
        }

        .info-row span {
            color: #666;
        }

        .price-info {
            font-size: 1.4em !important;
            font-weight: 700 !important;
            color: #a0d2ac !important;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #d0d0d0;
        }

        .btn-agregar-medida {
            width: 100%;
            padding: 14px;
            margin-top: 30px;
            background-color: #a0d2ac;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.05em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            display: none;
        }

        .btn-agregar-medida.active {
            display: block;
        }

        .btn-agregar-medida:hover {
            background-color: #8bc29a;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(160, 210, 172, 0.4);
        }

        .btn-agregar-medida:active {
            transform: translateY(0);
        }

        .error-message {
            color: #e74c3c;
            font-size: 0.9em;
            margin-top: 5px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        @media (max-width: 768px) {
            .medida-card {
                padding: 30px 20px;
            }

            .medida-title {
                font-size: 1.8em;
                margin-bottom: 35px;
            }

            .form-group-medida {
                margin-bottom: 20px;
            }
        }
    </style>

    <div id="data-productos" 
         data-productos='<?php echo json_encode($todos_productos); ?>'
         style="display:none;">
    </div>
</head>
<body>
    <div class="visitante-producto-medida">
        
        <header class="cabecera">
            <header class="cabecera">
            <?php sectionheader(3); ?>
        </header>

        <section class="product-hero">
            <div class="container hero-content">
                <a href="productos.php" class="btn-back">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </a>
                <h1 class="product-title-main">Producto a Medida</h1>
            </div>
        </section>

        <main class="container producto-medida-container">
            <div class="medida-card">
                <h2 class="medida-title">Personaliza tu Producto</h2>
                
                <form id="form-producto-medida" action="agregarproducto.php" method="POST">
                    
                    <!-- Selección del Producto -->
                    <div class="form-group-medida">
                        <label for="select-producto">Selección del Producto:</label>
                        <select id="select-producto" name="producto" required>
                            <option value="">-- Elige un producto --</option>
                            <?php
                            // Obtenemos referencias únicas
                            $referencias_unicas = array_unique(array_column($todos_productos, 'referencia'));
                            foreach ($referencias_unicas as $ref) {
                                // Obtenemos el primer producto de cada referencia para mostrar el nombre
                                $producto_ref = array_filter($todos_productos, function($p) use ($ref) {
                                    return $p['referencia'] === $ref;
                                });
                                $producto_ref = reset($producto_ref);
                                echo '<option value="' . htmlspecialchars($ref) . '">' . htmlspecialchars($producto_ref['nombre']) . '</option>';
                            }
                            ?>
                        </select>
                        <div class="error-message" id="error-producto">Por favor, selecciona un producto</div>
                    </div>

                    <!-- Selección del Color -->
                    <div class="form-group-medida">
                        <label for="select-color">Elige el Color:</label>
                        <select id="select-color" name="color" required disabled>
                            <option value="">-- Primero elige un producto --</option>
                        </select>
                        <div class="error-message" id="error-color">Por favor, selecciona un color</div>
                    </div>

                    <!-- Selección de la Medida -->
                    <div class="form-group-medida">
                        <label for="select-medida">Tamaño del Producto:</label>
                        <select id="select-medida" name="medida" required disabled>
                            <option value="">-- Primero elige producto y color --</option>
                        </select>
                        <div class="error-message" id="error-medida">Por favor, selecciona un tamaño</div>
                    </div>

                    <!-- Información del Producto Seleccionado -->
                    <div class="product-info-medida" id="product-info">
                        <div class="info-row">
                            <strong>Descripción:</strong>
                            <span id="info-descripcion">-</span>
                        </div>
                        <div class="info-row">
                            <strong>Stock Disponible:</strong>
                            <span id="info-stock">-</span>
                        </div>
                        <div class="info-row">
                            <strong>Referencia:</strong>
                            <span id="info-referencia">-</span>
                        </div>
                        <div class="info-row price-info">
                            <strong>Precio:</strong>
                            <span id="info-precio">-</span>
                        </div>
                    </div>

                    <!-- Input Hidden para el ID del producto -->
                    <input type="hidden" id="id-producto-seleccionado" name="id_producto" value="">

                    <!-- Cantidad -->
                    <div class="form-group-medida" id="cantidad-group" style="display: none;">
                        <label for="input-cantidad">Unidades:</label>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <button type="button" class="qty-btn btn-menos" style="padding: 8px 12px; background: #e8e8e8; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">-</button>
                            <input type="number" 
                                   id="input-cantidad" 
                                   name="cantidad"
                                   value="1" 
                                   min="1" 
                                   style="width: 60px; padding: 8px; text-align: center; border: 1px solid #d0d0d0; border-radius: 4px;">
                            <button type="button" class="qty-btn btn-mas" style="padding: 8px 12px; background: #e8e8e8; border: none; border-radius: 4px; cursor: pointer; font-weight: 600;">+</button>
                        </div>
                    </div>

                    <!-- Botón Agregar al Carrito -->
                    <button type="submit" class="btn-agregar-medida" id="btn-agregar">
                        Agregar al Carrito
                    </button>
                </form>
            </div>
        </main>

        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-logo-section">
                        <div class="logo-footer">
                            <img src="../imagenes/logo.png" alt="Logo Metalfulsán">
                        </div>
                        <div class="redes">
                            <a href="https://www.instagram.com/metalfulsansl/" target="_blank" class="instagram-link">
                                <svg viewBox="0 0 24 24" fill="white">
                                    <path d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="footer-links">
                        <div class="enlaces-rapidos">
                            <h3>Enlaces rápidos</h3>
                            <ul>
                                <li><a href="conocenos.php">Conócenos</a></li>
                                <li><a href="productos.php">Productos</a></li>
                                <li><a href="IniciarSesion.php">Mi perfil</a></li>
                            </ul>
                        </div>

                        <div class="contacto-footer">
                            <h3>Contacto</h3>
                            <ul>
                                <li>
                                    <svg viewBox="0 0 24 24">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                    </svg>
                                    <a href="https://www.google.com/maps/place//data=!4m2!3m1!1s0xd71fd00684554b1:0xef4e70ab821a7762?sa=X&ved=1t:8290&ictx=111" target="_blank">
                                        Extrarradio Cortijo la Purisima, 2P, 18004 Granada
                                    </a>
                                </li>
                                <li>
                                    <svg viewBox="0 0 24 24">
                                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                                    </svg>
                                    <a href="tel:652921960">652 921 960</a>
                                </li>
                                <li>
                                    <svg viewBox="0 0 24 24">
                                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                                    </svg>
                                    <a href="mailto:metalfulsan@gmail.com">metalfulsan@gmail.com</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="footer-bottom">
                    <ul class="footer-links-bottom">
                        <li><a href="aviso-legal.php">Aviso Legal</a></li>
                        <li><a href="privacidad.php">Política de Privacidad</a></li>
                        <li><a href="cookies.php">Política de Cookies</a></li>
                    </ul>
                </div>
            </div>
        </footer>

    </div>

    <script>
        // Datos de productos desde PHP
        const productosData = JSON.parse(document.getElementById('data-productos').getAttribute('data-productos'));
        
        // Elementos del DOM
        const selectProducto = document.getElementById('select-producto');
        const selectColor = document.getElementById('select-color');
        const selectMedida = document.getElementById('select-medida');
        const inputCantidad = document.getElementById('input-cantidad');
        const btnMenos = document.querySelector('.btn-menos');
        const btnMas = document.querySelector('.btn-mas');
        const formMedida = document.getElementById('form-producto-medida');
        const productInfo = document.getElementById('product-info');
        const btnAgregar = document.getElementById('btn-agregar');
        const cantidadGroup = document.getElementById('cantidad-group');
        const idProductoInput = document.getElementById('id-producto-seleccionado');

        // Eventos
        selectProducto.addEventListener('change', actualizarColores);
        selectColor.addEventListener('change', actualizarMedidas);
        selectMedida.addEventListener('change', mostrarProductoSeleccionado);
        
        btnMenos.addEventListener('click', (e) => {
            e.preventDefault();
            if (inputCantidad.value > 1) {
                inputCantidad.value = parseInt(inputCantidad.value) - 1;
            }
        });

        btnMas.addEventListener('click', (e) => {
            e.preventDefault();
            const max = parseInt(inputCantidad.max) || 999;
            if (inputCantidad.value < max) {
                inputCantidad.value = parseInt(inputCantidad.value) + 1;
            }
        });

        formMedida.addEventListener('submit', (e) => {
            e.preventDefault();
            if (validarFormulario()) {
                formMedida.submit();
            }
        });

        // Función para actualizar colores disponibles
        function actualizarColores() {
            const referencia = selectProducto.value;
            selectColor.innerHTML = '<option value="">-- Elige un color --</option>';
            selectMedida.innerHTML = '<option value="">-- Primero elige producto y color --</option>';
            productInfo.classList.remove('active');
            btnAgregar.classList.remove('active');
            cantidadGroup.style.display = 'none';

            if (referencia) {
                const coloresUnicos = [...new Set(
                    productosData
                        .filter(p => p.referencia === referencia)
                        .map(p => p.color)
                )];

                coloresUnicos.forEach(color => {
                    const option = document.createElement('option');
                    option.value = color;
                    option.textContent = color;
                    selectColor.appendChild(option);
                });

                selectColor.disabled = false;
            } else {
                selectColor.disabled = true;
                selectMedida.disabled = true;
            }
        }

        // Función para actualizar medidas disponibles
        function actualizarMedidas() {
            const referencia = selectProducto.value;
            const color = selectColor.value;
            selectMedida.innerHTML = '<option value="">-- Elige un tamaño --</option>';
            productInfo.classList.remove('active');
            btnAgregar.classList.remove('active');
            cantidadGroup.style.display = 'none';

            if (referencia && color) {
                const medidasUnicas = [...new Set(
                    productosData
                        .filter(p => p.referencia === referencia && p.color === color)
                        .map(p => p.medidas)
                )];

                medidasUnicas.forEach(medida => {
                    const option = document.createElement('option');
                    option.value = medida;
                    option.textContent = medida;
                    selectMedida.appendChild(option);
                });

                selectMedida.disabled = false;
            } else {
                selectMedida.disabled = true;
            }
        }

        // Función para mostrar información del producto seleccionado
        function mostrarProductoSeleccionado() {
            const referencia = selectProducto.value;
            const color = selectColor.value;
            const medida = selectMedida.value;

            if (referencia && color && medida) {
                const productoSeleccionado = productosData.find(p => 
                    p.referencia === referencia && 
                    p.color === color && 
                    p.medidas === medida
                );

                if (productoSeleccionado) {
                    // Actualizar información
                    document.getElementById('info-descripcion').textContent = productoSeleccionado.descripcion || '-';
                    document.getElementById('info-stock').textContent = productoSeleccionado.stock + ' unidades';
                    document.getElementById('info-referencia').textContent = productoSeleccionado.referencia;
                    document.getElementById('info-precio').textContent = parseFloat(productoSeleccionado.precio).toFixed(2) + '€';

                    // Actualizar máximo de cantidad
                    inputCantidad.max = productoSeleccionado.stock;
                    inputCantidad.value = 1;

                    // Actualizar ID del producto
                    idProductoInput.value = productoSeleccionado.id;

                    // Mostrar información y botón
                    productInfo.classList.add('active');
                    btnAgregar.classList.add('active');
                    cantidadGroup.style.display = 'block';

                    // Limpiar errores
                    document.querySelectorAll('.error-message').forEach(msg => msg.classList.remove('show'));
                }
            } else {
                productInfo.classList.remove('active');
                btnAgregar.classList.remove('active');
                cantidadGroup.style.display = 'none';
            }
        }

        // Función para validar formulario
        function validarFormulario() {
            let esValido = true;
            
            document.querySelectorAll('.error-message').forEach(msg => msg.classList.remove('show'));

            if (!selectProducto.value) {
                document.getElementById('error-producto').classList.add('show');
                esValido = false;
            }

            if (!selectColor.value) {
                document.getElementById('error-color').classList.add('show');
                esValido = false;
            }

            if (!selectMedida.value) {
                document.getElementById('error-medida').classList.add('show');
                esValido = false;
            }

            if (!idProductoInput.value) {
                esValido = false;
            }

            return esValido;
        }
    </script>
</body>
</html>
