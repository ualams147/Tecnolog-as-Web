<?php
session_start();
include '../conexion.php'; 

// =================================================================================
// 1. CARGA DE DATOS DINÁMICA (BASE DE DATOS RELACIONAL)
// =================================================================================
try {
    $sql = "SELECT 
                c.id AS cat_id,
                c.nombre AS categoria, 
                m.nombre AS material, 
                p.color 
            FROM productos p
            INNER JOIN categorias c ON p.id_categoria = c.id
            INNER JOIN materiales m ON p.id_material = m.id
            WHERE p.precio > 0 
            ORDER BY c.nombre, m.nombre, p.color";

    $stmt = $conn->query($sql);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $datos_dinamicos = [];
    $lista_categorias = [];
    $mapa_ids_js = [];

    foreach ($resultados as $row) {
        $cat = trim($row['categoria']); 
        $mat = trim($row['material']);  
        $col = trim($row['color']);     
        $id  = $row['cat_id'];

        $catKey = strtolower($cat);

        if (!in_array($cat, $lista_categorias)) {
            $lista_categorias[] = $cat;
        }

        $mapa_ids_js[$id] = $catKey;

        if (!isset($datos_dinamicos[$catKey])) {
            $datos_dinamicos[$catKey] = [];
        }
        if (!isset($datos_dinamicos[$catKey][$mat])) {
            $datos_dinamicos[$catKey][$mat] = [];
        }
        if (!in_array($col, $datos_dinamicos[$catKey][$mat])) {
            $datos_dinamicos[$catKey][$mat][] = $col;
        }
    }

} catch (PDOException $e) {
    echo "Error al cargar datos: " . $e->getMessage();
    $datos_dinamicos = [];
}

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
    <link rel="stylesheet" href="../css/productomedida.css">
</head>
<body>
    <div class="page-wrapper">
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
                    <a href="productos.php">Productos</a>
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

        <section class="medida-hero">
            <div class="hero-container">
                <a href="productos.php" class="btn-back-hero">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </a>
                <h1 class="titulo-hero">Producto a medida</h1>
            </div>
        </section>

        <main class="medida-main container">
            
            <div class="medida-card">
                
                <div class="step-item active" id="step-1">
                    <div class="step-header" onclick="toggleStep(1)">
                        <h3 class="step-title">1. Selección del Producto:</h3>
                        <svg class="step-icon" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>
                    </div>
                    <div class="step-content">
                        <select id="select-producto" class="custom-select" onchange="productoSeleccionado()">
                            <option value="" disabled selected>Selecciona un tipo...</option>
                            <?php foreach ($lista_categorias as $cat): ?>
                                <option value="<?php echo htmlspecialchars(strtolower($cat)); ?>">
                                    <?php echo htmlspecialchars($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="step-item disabled" id="step-2">
                    <div class="step-header" onclick="toggleStep(2)">
                    <h3 class="step-title">2. Elige el Material:</h3>
                    <svg class="step-icon" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>
                </div>
                <div class="step-content">
                    <input type="hidden" id="select-material" onchange="materialSeleccionado()">
        
                <div class="custom-select-wrapper" id="wrapper-material">
                <div class="select-display" onclick="toggleMaterialMenu()">
                    <span id="display-text-material">Primero selecciona producto...</span>
                    <span style="font-size:12px; color:#293661;">&#9660;</span>
                </div>
                    <div class="select-options" id="listado-materiales">
                </div>
            </div>
        </div>
                                
                <div class="step-item disabled" id="step-3">
                    <div class="step-header" onclick="toggleStep(3)">
                        <h3 class="step-title">3. Elige el Color:</h3>
                        <svg class="step-icon" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>
                    </div>
                    <div class="step-content">
                        <input type="hidden" id="select-color" onchange="colorSeleccionado()">
                        
                        <div class="custom-select-wrapper" id="wrapper-color">
                            <div class="select-display" onclick="toggleColorMenu()">
                                <span id="display-text-color">Primero selecciona material...</span>
                                <span style="font-size:12px; color:#293661;">&#9660;</span>
                            </div>
                            <div class="select-options" id="listado-colores">
                                </div>
                        </div>
                    </div>
                </div>

                <div class="step-item disabled" id="step-4">
                    <div class="step-header" onclick="toggleStep(4)">
                        <h3 class="step-title">4. Tamaño del Producto:</h3>
                        <svg class="step-icon" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>
                    </div>
                    <div class="step-content">
                        <input id="input-medida" 
                               class="custom-input" 
                               type="text" 
                               placeholder="Ej: 50x100" 
                               oninput="validarInputMedida(this)" 
                               onblur="validarYFormatearMedida()">
                        <p class="error-message" id="medida-error"></p>
                    </div>
                </div>

                <div class="step-item disabled" id="step-5">
                    <div class="step-header" onclick="toggleStep(5)">
                        <h3 class="step-title">5. Otros Detalles:</h3>
                        <svg class="step-icon" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>
                    </div>
                    <div class="step-content">
                        <textarea id="input-detalles" class="custom-textarea" placeholder="Cuéntanos cualquier detalle adicional..." oninput="verificarFinal()"></textarea>
                    </div>
                </div>

                <div id="final-action" style="display:none; opacity:0; transition: opacity 0.5s;">
                    <button type="button" class="btn-enviar" onclick="enviarPropuesta()">
                        Enviar propuesta
                    </button>
                </div>

            </div>
        </main>

        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-logo-section">
                        <div class="logo-footer"><img src="../imagenes/footer.png" alt="Logo Metalful"></div>
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
                                <li><a href="#">Extrarradio Cortijo la Purisima, 2P</a></li>
                                <li><a href="tel:652921960">652 921 960</a></li>
                                <li><a href="mailto:metalfulsan@gmail.com">metalfulsan@gmail.com</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom">
                    <div class="politica-legal">
                        <a href="../aviso-legal.php">Aviso Legal</a>
                        <span>•</span>
                        <a href="../privacidad.php">Política de Privacidad</a>
                        <span>•</span>
                        <a href="../cookies.php">Política de Cookies</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="../js/auth.js"></script>

    <script>
        // ======================================================
        // 1. RECEPCIÓN DE DATOS DESDE PHP 
        // ======================================================
        const datosDB = <?php echo json_encode($datos_dinamicos); ?>;
        const mapaIds = <?php echo json_encode($mapa_ids_js); ?>;

        // --- MAPA DE COLORES PARA LA BOLITA VISUAL ---
        // Ajusta los códigos Hexadecimales según tus colores reales
        const mapaColores = {
            'blanco': '#FFFFFF',
            'negro': '#000000',
            'gris': '#808080',
            'plata': '#C0C0C0',
            'rojo': '#C62828',
            'verde': '#2E7D32',
            'azul': '#1565C0',
            'madera': '#8D6E63',
            'roble': '#A1887F',
            'roble dorado': '#Bcaaa4',
            'nogal': '#4E342E',
            'antracita': '#37474F',
            'bronce': '#8D6E63',
            'beige': '#F5F5DC'
        };

        // ======================================================
        // 2. LÓGICA DE LOS DESPLEGABLES
        // ======================================================

        function productoSeleccionado() {
            const prodSelect = document.getElementById('select-producto');
            const matSelect = document.getElementById('select-material');
            const colorInput = document.getElementById('select-color'); 
            const prodValue = prodSelect.value;

            // 1. Limpiar siguientes pasos
            matSelect.innerHTML = '<option value="" disabled selected>Selecciona un material...</option>';
            
            // Limpiar selector visual de color
            colorInput.value = "";
            document.getElementById('display-text-color').innerText = 'Primero selecciona material...';
            document.getElementById('listado-colores').innerHTML = '';

            document.getElementById('step-3').classList.add('disabled');
            document.getElementById('step-3').classList.remove('active');
            
            // 2. Comprobar si hay datos
            if (prodValue && datosDB[prodValue]) {
                const materiales = Object.keys(datosDB[prodValue]);

                if (materiales.length > 0) {
                    materiales.forEach(mat => {
                        const option = document.createElement('option');
                        option.value = mat;
                        option.textContent = mat;
                        matSelect.appendChild(option);
                    });
                } else {
                    matSelect.innerHTML = '<option>No hay materiales disponibles</option>';
                }

                document.getElementById('step-2').classList.remove('disabled');
                abrirPaso(2);
                tituloPaso(1, 'Producto: ' + prodSelect.options[prodSelect.selectedIndex].text);
            }
        }

        // --- FUNCIÓN MODIFICADA PARA GENERAR LISTA VISUAL ---
        function materialSeleccionado() {
            const prodValue = document.getElementById('select-producto').value;
            const matSelect = document.getElementById('select-material');
            const matValue = matSelect.value;
            
            const colorInput = document.getElementById('select-color');
            const displayText = document.getElementById('display-text-color');
            const listadoDiv = document.getElementById('listado-colores');

            // 1. Resetear
            colorInput.value = "";
            displayText.innerHTML = 'Selecciona un color...';
            listadoDiv.innerHTML = ''; 

            // 2. Buscar colores
            if (prodValue && matValue && datosDB[prodValue][matValue]) {
                const colores = datosDB[prodValue][matValue];
                
                colores.forEach(col => {
                    // Normalizar para buscar código hex
                    let nombreColorNorm = col.toLowerCase().trim();
                    let codigoHex = mapaColores[nombreColorNorm] || '#ddd'; // Gris si no encuentra

                    // Crear el item de la lista
                    const divItem = document.createElement('div');
                    divItem.className = 'option-item';
                    divItem.onclick = function() { seleccionarColorVisual(col, codigoHex); };
                    
                    divItem.innerHTML = `
                        <span class="color-dot" style="background-color: ${codigoHex};"></span>
                        <span>${col}</span>
                    `;
                    
                    listadoDiv.appendChild(divItem);
                });

                document.getElementById('step-3').classList.remove('disabled');
                abrirPaso(3);
                tituloPaso(2, 'Material: ' + matValue);
            }
        }

        // --- NUEVAS FUNCIONES PARA EL SELECT VISUAL ---
        function toggleColorMenu() {
            if(document.getElementById('step-3').classList.contains('disabled')) return;
            const menu = document.getElementById('listado-colores');
            const wrapper = document.getElementById('wrapper-color');
            
            if (menu.style.display === 'block') {
                menu.style.display = 'none';
                wrapper.classList.remove('open');
            } else {
                menu.style.display = 'block';
                wrapper.classList.add('open');
            }
        }

        function seleccionarColorVisual(nombreColor, codigoHex) {
            const input = document.getElementById('select-color');
            input.value = nombreColor;

            const display = document.getElementById('display-text-color');
            display.innerHTML = `<span class="color-dot" style="background-color: ${codigoHex};"></span> ${nombreColor}`;

            document.getElementById('listado-colores').style.display = 'none';
            document.getElementById('wrapper-color').classList.remove('open');

            // Disparar lógica siguiente
            colorSeleccionado();
        }

        // Cerrar menú si click fuera
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('wrapper-color');
            const menu = document.getElementById('listado-colores');
            if (wrapper && !wrapper.contains(e.target)) {
                menu.style.display = 'none';
                wrapper.classList.remove('open');
            }
        });

        // ----------------------------------------------------

        function colorSeleccionado() {
            const colorVal = document.getElementById('select-color').value;
            if (colorVal) {
                document.getElementById('step-4').classList.remove('disabled');
                abrirPaso(4);
                tituloPaso(3, 'Color: ' + colorVal);
            }
        }

        // ======================================================
        // 3. FUNCIONES VISUALES (ACORDEÓN)
        // ======================================================
        function abrirPaso(numPaso) {
            document.querySelectorAll('.step-item').forEach(el => el.classList.remove('active'));
            document.getElementById('step-' + numPaso).classList.add('active');
        }

        function toggleStep(stepNum) {
            const step = document.getElementById('step-' + stepNum);
            if (!step.classList.contains('disabled') && !step.classList.contains('active')) {
                abrirPaso(stepNum);
            }
        }

        function tituloPaso(num, texto) {
            const headerTitle = document.querySelector('#step-' + num + ' .step-title');
            const baseTitle = headerTitle.innerText.split(':')[0] + ':';
            headerTitle.innerHTML = baseTitle + ' <span style="font-weight:400; color:#666; font-size:0.9em;">' + texto.split(':')[1] + '</span>';
        }

        // ======================================================
        // 4. VALIDACIONES
        // ======================================================
        function validarInputMedida(input) {
            let valor = input.value.replace(/[^0-9xX]/g, ''); 
            input.value = valor;
        }

        function validarYFormatearMedida() {
            const input = document.getElementById('input-medida');
            const errorMsg = document.getElementById('medida-error');
            let valor = input.value.toLowerCase();

            input.classList.remove('input-error');
            errorMsg.style.display = 'none';

            if (valor.length === 0) return;
            if (!valor.includes('x')) return;

            const partes = valor.split('x');
            const ancho = parseInt(partes[0]);
            const alto = parseInt(partes[1]);

            if (ancho < 30 || alto < 30) {
                input.classList.add('input-error');
                errorMsg.textContent = "⚠️ El mínimo es 30 cm.";
                errorMsg.style.display = 'block';
                input.value = "";
                return;
            }

            if (!valor.includes('cm')) {
                input.value = ancho + "x" + alto + " cm";
            }
            verificarFinal();
        }

        function verificarFinal() {
            const inputMedida = document.getElementById('input-medida');
            const inputDetalles = document.getElementById('input-detalles');
            const actionDiv = document.getElementById('final-action');
            
            const medidaValida = inputMedida.value.includes('cm');
            
            if (medidaValida) {
                document.getElementById('step-5').classList.remove('disabled');
            } else {
                document.getElementById('step-5').classList.add('disabled');
            }

            if (medidaValida && inputDetalles.value.length > 3) {
                actionDiv.style.display = 'block';
                setTimeout(() => actionDiv.style.opacity = '1', 10);
            } else {
                actionDiv.style.opacity = '0';
                setTimeout(() => actionDiv.style.display = 'none', 300);
            }
        }

        function enviarPropuesta() {
            alert('✅ Funcionalidad de envío lista.');
        }

        // ======================================================
        // 5. AUTO-SELECCIÓN
        // ======================================================
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const idCat = urlParams.get('categoria');

            if (idCat && mapaIds[idCat]) {
                const nombreCat = mapaIds[idCat];
                const select = document.getElementById('select-producto');
                select.value = nombreCat;

                if (select.value === nombreCat) {
                    productoSeleccionado();
                }
            }
        });
    </script>
</body>
</html>