<?php
// 1. SIEMPRE PRIMERO: Funciones y Sesión
include 'CabeceraFooter.php'; 

// 2. CONEXIÓN A BD
include 'conexion.php'; 

// =================================================================================
// 3. CARGA DE DATOS DINÁMICA (BASE DE DATOS RELACIONAL)
// =================================================================================
try {
    // Consulta SQL con JOINS
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

    // Arrays para JavaScript
    $datos_dinamicos = [];
    $lista_categorias = [];
    $mapa_ids_js = [];

    foreach ($resultados as $row) {
        $cat = trim($row['categoria']); 
        $mat = trim($row['material']);  
        $col = trim($row['color']);     
        $id  = $row['cat_id'];

        $catKey = strtolower($cat);

        // 1. Lista simple de categorías
        if (!in_array($cat, $lista_categorias)) {
            $lista_categorias[] = $cat;
        }

        // 2. Mapa de IDs
        $mapa_ids_js[$id] = $catKey;

        // 3. Árbol de datos
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
?>

<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['idioma']) ? $_SESSION['idioma'] : 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['medida_titulo_pag']; ?></title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/productos.css">
    <link rel="stylesheet" href="css/productomedida.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="page-wrapper">
        
        <?php sectionheader(3); ?>

        <section class="medida-hero">
            <div class="hero-container">
                <a href="productos.php" class="btn-back-hero">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </a>
                <h1 class="titulo-hero"><?php echo $lang['medida_h1']; ?></h1>
            </div>
        </section>

        <main class="medida-main container">
            
            <div class="medida-card">
                
                <div class="step-item active" id="step-1">
                    <div class="step-header" onclick="toggleStep(1)">
                        <h3 class="step-title"><?php echo $lang['medida_paso_1']; ?></h3>
                        <svg class="step-icon" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>
                    </div>
                    <div class="step-content">
                        <select id="select-producto" class="custom-select" onchange="productoSeleccionado()">
                            <option value="" disabled selected><?php echo $lang['medida_ph_sel_tipo']; ?></option>
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
                        <h3 class="step-title"><?php echo $lang['medida_paso_2']; ?></h3>
                        <svg class="step-icon" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>
                    </div>
                    <div class="step-content" style="display: flex; align-items: center; gap: 15px;">
                        <select id="select-material" class="custom-select" onchange="materialSeleccionado()" style="flex: 1; width: auto;">
                            <option value="" disabled selected><?php echo $lang['medida_ph_sel_mat']; ?></option>
                        </select>
    
                        <img id="img-material" src="" style="width: 60px; height: 60px; object-fit: contain; border: 1px solid #ddd; border-radius: 5px; display: none;">
                    </div>
                </div>

                <div class="step-item disabled" id="step-3">
                    <div class="step-header" onclick="toggleStep(3)">
                    <h3 class="step-title"><?php echo $lang['medida_paso_3']; ?></h3>
                    <svg class="step-icon" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>
                </div>

            <div class="step-content" style="display: flex; align-items: center; gap: 15px;">
                <select id="select-color" class="custom-select" onchange="colorSeleccionado()" style="flex: 1; width: auto;">
                    <option value="" disabled selected><?php echo $lang['medida_ph_sel_col']; ?></option>
                </select>
        
                <img id="img-color" src="" onerror="this.style.display='none'" style="width: 60px; height: 60px; object-fit: contain; border: 1px solid #ddd; border-radius: 5px; display: none;">
            </div>
        </div> 
         
                <div class="step-item disabled" id="step-4">
                    <div class="step-header" onclick="toggleStep(4)">
                        <h3 class="step-title"><?php echo $lang['medida_paso_4']; ?></h3>
                        <svg class="step-icon" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>
                    </div>
                    <div class="step-content">
                        <input id="input-medida" 
                               class="custom-input" 
                               type="text" 
                               placeholder="<?php echo $lang['medida_ph_input_medida']; ?>" 
                               oninput="validarInputMedida(this)" 
                               onblur="validarYFormatearMedida()">
                        <p class="error-message" id="medida-error"></p>
                    </div>
                </div>

                <div class="step-item disabled" id="step-5">
                    <div class="step-header" onclick="toggleStep(5)">
                        <h3 class="step-title"><?php echo $lang['medida_paso_5']; ?></h3>
                        <svg class="step-icon" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>
                    </div>
                    <div class="step-content">
                        <textarea id="input-detalles" class="custom-textarea" placeholder="<?php echo $lang['medida_ph_input_detalles']; ?>" oninput="verificarFinal()"></textarea>
                    </div>
                </div>

                <div id="final-action" style="display:none; opacity:0; transition: opacity 0.5s;">
                    <button type="button" class="btn-enviar" onclick="enviarPropuesta()">
                        <?php echo $lang['medida_btn_enviar']; ?>
                    </button>
                </div>

            </div>
        </main>

        <?php sectionfooter(); ?>
    </div>

    <script src="js/auth.js"></script>

    <script>
        // ======================================================
        // 1. RECEPCIÓN DE DATOS DESDE PHP
        // ======================================================
        const datosDB = <?php echo json_encode($datos_dinamicos); ?>;
        const mapaIds = <?php echo json_encode($mapa_ids_js); ?>;

        // ======================================================
        // 2. LÓGICA DE LOS DESPLEGABLES
        // ======================================================

        function productoSeleccionado() {
            const prodSelect = document.getElementById('select-producto');
            const matSelect = document.getElementById('select-material');
            const colorSelect = document.getElementById('select-color');
            const prodValue = prodSelect.value;

            // Reset visual
            const imgMat = document.getElementById('img-material');
            const imgColor = document.getElementById('img-color');
            if (imgMat) imgMat.style.display = 'none';
            if (imgColor) imgColor.style.display = 'none';

            // 1. Limpiar siguientes pasos
            matSelect.innerHTML = '<option value="" disabled selected><?php echo $lang['medida_js_sel_mat_ok']; ?></option>';
            colorSelect.innerHTML = '<option value="" disabled selected><?php echo $lang['medida_ph_sel_col']; ?></option>';

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
                    matSelect.innerHTML = '<option><?php echo $lang['medida_js_no_mat']; ?></option>';
                }
                // Desbloquear Paso 2
                document.getElementById('step-2').classList.remove('disabled');
                abrirPaso(2);
                tituloPaso(1, 'Producto: ' + prodSelect.options[prodSelect.selectedIndex].text);
            }
        }

        function materialSeleccionado() {
            const prodValue = document.getElementById('select-producto').value;
            const matSelect = document.getElementById('select-material');
            const colorSelect = document.getElementById('select-color');
            const matValue = matSelect.value;
            const imgMat = document.getElementById('img-material');
            
            if (imgMat) {
                if (matValue) {
                    const nombre = matValue.toLowerCase().trim();
                    if (nombre === 'aluminio' || nombre === 'pvc') {
                        imgMat.src = 'imagenes/' + nombre + '.png';
                        imgMat.style.display = 'block'; 
                    }   else {
                        imgMat.style.display = 'none';  
                    }
                } else {
                    imgMat.style.display = 'none';
                }
            }
            // 1. Limpiar colores
            colorSelect.innerHTML = '<option value="" disabled selected><?php echo $lang['medida_js_sel_col_ok']; ?></option>';
            // 2. Buscar colores en el array
            if (prodValue && matValue && datosDB[prodValue][matValue]) {
                const colores = datosDB[prodValue][matValue];
                colores.forEach(col => {
                    const option = document.createElement('option');
                    option.value = col;
                    option.textContent = col;
                    colorSelect.appendChild(option);
                });

            // Desbloquear Paso 3
            document.getElementById('step-3').classList.remove('disabled');
            tituloPaso(2, 'Material: ' + matValue);
        }
    }

        function colorSeleccionado() {
            const colorSelect = document.getElementById('select-color');
            const imgColor = document.getElementById('img-color');
            const val = colorSelect.value; 

            if (val) {
                let nombreLimpio = val.trim(); 
                imgColor.src = 'imagenes/color' + nombreLimpio + '.png';
                imgColor.style.display = 'block';

                const paso4 = document.getElementById('step-4');
                if (paso4) {
                    paso4.classList.remove('disabled'); 
                }

            } else {
                imgColor.style.display = 'none';
            }

            tituloPaso(3, 'Color: ' + val);
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
        // 4. VALIDACIONES (MEDIDA Y DETALLES)
        // ======================================================
        function validarInputMedida(input) {
            let valor = input.value.replace(/[^0-9xX]/g, ''); // Solo números y 'x'
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
                errorMsg.textContent = "<?php echo $lang['medida_js_min_30']; ?>";
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
            // Recoger datos
            const producto = document.getElementById('select-producto').options[document.getElementById('select-producto').selectedIndex].text;
            const material = document.getElementById('select-material').value;
            const color = document.getElementById('select-color').value;
            const medida = document.getElementById('input-medida').value;
            const detalles = document.getElementById('input-detalles').value;

            // Enviar a PHP (AJAX)
            fetch('enviar_presupuesto.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    producto: producto + " (" + material + ")",
                    color: color,
                    medida: medida,
                    detalles: detalles
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '¡Enviado!',
                        text: '<?php echo $lang['medida_js_alert_enviada']; ?>',
                        icon: 'success',
                        confirmButtonColor: '#293661'
                    }).then(() => {
                        window.location.href = 'productos.php';
                    });
                } else {
                    Swal.fire('Error', '<?php echo $lang['medida_js_alert_error']; ?>', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Error', 'Error técnico al conectar con el servidor.', 'error');
            });
        }

        // ======================================================
        // 5. AUTO-SELECCIÓN (AL CARGAR LA PÁGINA)
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