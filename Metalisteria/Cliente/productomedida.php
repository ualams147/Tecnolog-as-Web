<?php
session_start();
// include '../conexion.php'; 

// Lógica del carrito para el Header
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
                    <a href="carrito.php">Carrito</a>
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
                        <h3 class="step-title">Selección del Producto:</h3>
                        <svg class="step-icon" viewBox="0 0 24 24">
                            <path d="M7 10l5 5 5-5z"/>
                        </svg>
                    </div>
                    <div class="step-content">
                        <select id="select-producto" class="custom-select" onchange="productoSeleccionado()">
                            <option value="" disabled selected>Selecciona un tipo...</option>
                            <option value="puertas">Puertas</option>
                            <option value="ventanas">Ventanas</option>
                            <option value="barandillas">Barandillas</option>
                            <option value="otras">Otras estructuras</option>
                        </select>
                    </div>
                </div>

                <div class="step-item disabled" id="step-2">
                    <div class="step-header" onclick="toggleStep(2)">
                        <h3 class="step-title">Elije el Color:</h3>
                        <svg class="step-icon" viewBox="0 0 24 24">
                            <path d="M7 10l5 5 5-5z"/>
                        </svg>
                    </div>
                    <div class="step-content">
                        <select id="select-color" class="custom-select" onchange="colorSeleccionado()">
                            <option value="" disabled selected>Primero selecciona producto...</option>
                        </select>
                    </div>
                </div>

                <div class="step-item disabled" id="step-3">
                    <div class="step-header" onclick="toggleStep(3)">
                        <h3 class="step-title">Tamaño del Producto:</h3>
                        <svg class="step-icon" viewBox="0 0 24 24">
                            <path d="M7 10l5 5 5-5z"/>
                        </svg>
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

                <div class="step-item disabled" id="step-4">
                    <div class="step-header" onclick="toggleStep(4)">
                        <h3 class="step-title">Otros Detalles:</h3>
                        <svg class="step-icon" viewBox="0 0 24 24">
                            <path d="M7 10l5 5 5-5z"/>
                        </svg>
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
                                <li><svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg><a href="https://www.google.com/maps/place//data=!4m2!3m1!1s0xd71fd00684554b1:0xef4e70ab821a7762?sa=X&ved=1t:8290&ictx=111" target="_blank">Extrarradio Cortijo la Purisima, 2P</a></li>
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

    <script>
        const datosProductos = {
            "puertas": ["Blanco Mate", "Negro Forja", "Gris Antracita", "Efecto Madera"],
            "ventanas": ["Blanco Nuclear", "Aluminio Natural", "Bronce", "Verde Carruaje"],
            "barandillas": ["Acero Inoxidable", "Negro Mate", "Oro Viejo"],
            "otras": ["A consultar", "Hierro bruto", "Galvanizado"]
        };

        function productoSeleccionado() {
            const prodSelect = document.getElementById('select-producto');
            const colorSelect = document.getElementById('select-color');
            const prodValue = prodSelect.value;

            if (prodValue) {
                const colores = datosProductos[prodValue];
                colorSelect.innerHTML = '<option value="" disabled selected>Selecciona un color...</option>';
                colores.forEach(color => {
                    const option = document.createElement('option');
                    option.value = color;
                    option.textContent = color;
                    colorSelect.appendChild(option);
                });

                document.getElementById('step-2').classList.remove('disabled');
                document.getElementById('step-1').classList.remove('active');
                document.getElementById('step-2').classList.add('active');
                document.querySelector('#step-1 .step-title').innerHTML = 
                    'Producto: <span style="font-weight:400; color:#666;">' + prodSelect.options[prodSelect.selectedIndex].text + '</span>';
            }
        }

        function colorSeleccionado() {
            const colorSelect = document.getElementById('select-color');
            if (colorSelect.value) {
                document.getElementById('step-3').classList.remove('disabled');
                document.getElementById('step-2').classList.remove('active');
                document.getElementById('step-3').classList.add('active');
                document.querySelector('#step-2 .step-title').innerHTML = 
                    'Color: <span style="font-weight:400; color:#666;">' + colorSelect.options[colorSelect.selectedIndex].text + '</span>';
            }
        }

        // --- FUNCIONES VISUALES DE ERROR ---
        function mostrarErrorMedida(mensaje) {
            const input = document.getElementById('input-medida');
            const errorMsg = document.getElementById('medida-error');
            input.classList.add('input-error');
            errorMsg.textContent = mensaje;
            errorMsg.style.display = 'block';
        }

        function limpiarErrorMedida() {
            const input = document.getElementById('input-medida');
            const errorMsg = document.getElementById('medida-error');
            input.classList.remove('input-error');
            errorMsg.style.display = 'none';
        }

        // --- VALIDACIÓN EN TIEMPO REAL (INPUT) ---
        function validarInputMedida(input) {
            // 1. Limpieza inicial (solo números y x)
            let valor = input.value.replace(/[^0-9xX]/g, '');
            input.value = valor;

            // 2. Lógica para detectar < 30 MIENTRAS escribes
            let partes = valor.toLowerCase().split('x');
            let ancho = parseInt(partes[0]);
            let alto = partes.length > 1 ? parseInt(partes[1]) : null;

            let hayError = false;

            // Comprobamos el primer número (ANCHO)
            if (!isNaN(ancho) && ancho < 30) {
                hayError = true;
            } else if (!isNaN(ancho) && ancho >= 30) {
                // Si el ancho ya es >= 30, comprobamos el ALTO si existe
                if (partes.length > 1 && partes[1] !== "") {
                     if (!isNaN(alto) && alto < 30) {
                         hayError = true;
                     }
                }
            }

            if (hayError) {
                mostrarErrorMedida("⚠️ El mínimo es 30 cm.");
            } else {
                limpiarErrorMedida();
            }
        }

        // --- VALIDACIÓN AL TERMINAR (BLUR) ---
        function validarYFormatearMedida() {
            const input = document.getElementById('input-medida');
            let valor = input.value.toLowerCase();

            if (valor.length === 0) {
                limpiarErrorMedida();
                return;
            }

            const partes = valor.split('x');

            // 1. Formato
            if (partes.length !== 2 || partes[0] === '' || partes[1] === '') {
                // Si está incompleto al salir, damos error (a menos que esté vacío)
                // Pero si el usuario solo escribió "50", asumimos que no acabó.
                // Si queremos ser estrictos:
                // mostrarErrorMedida("⚠️ Formato incorrecto (Ej: 50x100)");
                return; 
            }

            const ancho = parseInt(partes[0]);
            const alto = parseInt(partes[1]);

            // 2. Mínimo (Confirmación final)
            if (ancho < 30 || alto < 30) {
                mostrarErrorMedida("⚠️ El mínimo es 30 cm.");
                input.value = ""; 
                return;
            }

            // 3. Todo OK -> Añadir cm
            limpiarErrorMedida();
            input.value = ancho + "x" + alto + " cm";
            
            verificarFinal();
        }
        // -----------------------------------------------

        function toggleStep(stepNum) {
            const step = document.getElementById('step-' + stepNum);
            if (!step.classList.contains('disabled')) {
                if (!step.classList.contains('active')) {
                    document.querySelectorAll('.step-item').forEach(el => el.classList.remove('active'));
                    step.classList.add('active');
                }
            }
        }

        function verificarFinal() {
            const inputMedida = document.getElementById('input-medida');
            const inputDetalles = document.getElementById('input-detalles');
            const actionDiv = document.getElementById('final-action');
            
            const medidaValida = inputMedida.value.includes('cm');

            if (medidaValida) {
                document.getElementById('step-4').classList.remove('disabled');
            } else {
                document.getElementById('step-4').classList.add('disabled');
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
            const inputMedida = document.getElementById('input-medida').value;
            const inputDetalles = document.getElementById('input-detalles').value;
            const producto = document.getElementById('select-producto').value;
            const color = document.getElementById('select-color').value;
            const btn = document.querySelector('.btn-enviar');

            if (inputMedida.length > 3 && inputDetalles.length > 3) {
                
                const textoOriginal = btn.innerText;
                btn.innerText = "Enviando...";
                btn.disabled = true;
                btn.style.opacity = "0.7";

                fetch('enviarpresupuesto.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        producto: producto,
                        color: color,
                        medida: inputMedida,
                        detalles: inputDetalles
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ ¡Propuesta enviada con éxito!\n\nProducto: ' + producto + '\nColor: ' + color + '\nMedidas: ' + inputMedida + '\nDetalles: ' + inputDetalles);
                        location.reload(); 
                    } else {
                        alert('❌ Hubo un error al enviar.');
                        btn.innerText = textoOriginal;
                        btn.disabled = false;
                        btn.style.opacity = "1";
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('❌ Error de conexión con enviarpresupuesto.php');
                    btn.innerText = textoOriginal;
                    btn.disabled = false;
                    btn.style.opacity = "1";
                });

            } else {
                alert('⚠️ Completa todos los campos');
            }
        }
    </script>
</body>
</html>