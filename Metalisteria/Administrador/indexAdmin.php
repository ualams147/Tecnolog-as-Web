<?php
// IMPORTANTE: Inicializar variables vacías para evitar errores en el value="" de los inputs
$filtro_fecha = '';
$filtro_cliente = '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Administrador - Metalful</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/administrador.css">

</head>
<body>
    <div class="inicio-administrador">
        <!-- Cabecera -->
        <header class="cabecera">
            <div class="container">
                <div class="logo-main">
                    <img src="../imagenes/logo.png" alt="Logo Metalful">
                    <div class="logo-text">
                        <span> Metalisteria</span>
                        <strong>Fulsan</strong>
                    </div>
                </div>
                
                <nav class="nav-bar">
                    <a href="../Administrador/ListadoVentasAdmin.php">Ventas</a>
                    <a href="../Administrador/ListadoProductosAdmin.php">Productos</a>
                    <a href="../Administrador/ListadoClientesAdmin.php">Clientes</a>
                </nav>

                <div class="log-out">
                    <a href="../Visitante/index.php">Cerrar Sesión</a>
                </div>

            </div>
        </header>

        <!-- Título -->
        <div class="titulo-section">
            <div class="degradado"></div>
            <div class="recuadro-fondo"></div>
            
            <h1 class="titulo-principal">Resumen de ventas</h1>
            
            <div class="filtros-en-titulo">
                
                <div class="filtro-item">
                    <label for="filtro-fecha">Fecha de registro</label>
                    <div class="filtro-wrapper" id="wrapper-fecha">
                        <input type="date" id="filtro-fecha" value="<?php echo $filtro_fecha; ?>" onchange="checkInput('fecha'); aplicarFiltros()">
                        
                        <div class="input-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>

                        <button type="button" class="btn-borrar" onclick="borrarFiltro('fecha')">×</button>
                    </div>
                </div>

                <div class="filtro-item">
                    <label for="filtro-cliente">Nombre cliente</label>
                    <div class="filtro-wrapper" id="wrapper-cliente">
                        <input type="text" id="filtro-cliente" placeholder="Buscar..." 
                               value="<?php echo htmlspecialchars($filtro_cliente); ?>" 
                               autocomplete="off"
                               oninput="checkInput('cliente'); mostrarSugerencias()" 
                               onkeypress="checkEnter(event)">
                        
                        <div class="input-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>

                        <button type="button" class="btn-borrar" onclick="borrarFiltro('cliente')">×</button>

                        <div id="sugerencias-cliente" class="lista-autocompletado"></div>
                    </div>
                </div>

                <div class="filtro-item">
                    <label for="producto">Categoría de productos</label>
                    <div class="filtro-wrapper" id="wrapper-producto">
                        <select id="producto" name="producto" onchange="checkInput('producto'); aplicarFiltros()"> 
                            <option value="" selected>Todos</option>
                            <option value="ventanas">Ventanas</option>
                            <option value="puertas">Puertas</option>
                        </select>
                        
                        <div class="input-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        </div>

                        <button type="button" class="btn-borrar" onclick="borrarFiltro('producto')">×</button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Diagrama -->
        <div class="diagrama-section">
            <div class="grafica-container">
                <img src="https://figma-alpha-api.s3.us-west-2.amazonaws.com/images/43b4ec6b-81ae-2575-06d8-e6f73047a1b4" alt="Gráfica de ventas" class="grafica-image">
                <p class="grafica-title">Número de ventas en el último Mes</p>
            </div>
        </div>

        <!-- Opciones -->
        <div class="cards-grid">
            <!-- Productos -->
            <a href="../Administrador/ListadoProductosAdmin.php" class="card">
                <img src="../imagenes/4.png" alt="Productos" class="card-icon">
                <h3>Productos</h3>
            </a>

            <!-- Crear Producto -->
            <a href="../Administrador/CrearProductoAdmin.php" class="card">
                <img src="../imagenes/2.png" alt="Crear Producto" class="card-icon">
                <h3>Crear Producto</h3>
            </a>

            <!-- Clientes -->
            <a href="../Administrador/ListadoClientesAdmin.php" class="card">
                <img src="../imagenes/3.png" alt="Clientes" class="card-icon">
                <h3>Clientes</h3>
            </a>

            <!-- Ventas -->
            <a href="../Administrador/ListadoVentasAdmin.php" class="card">
                <img src="../imagenes/5.png" alt="Ventas" class="card-icon">
                <h3>Ventas</h3>
            </a>
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
                            <a href="https://www.instagram.com/metalfulsansl/" target="_blank" class="instagram-link">
                                <svg viewBox="0 0 24 24" fill="white">
                                    <path d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="footer-links">
                        <div class="contacto-footer">
                            <h3>Contacto</h3>
                            <div class="contacto-item">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                </svg>
                                <a href="https://www.google.com/maps/place//data=!4m2!3m1!1s0xd71fd00684554b1:0xef4e70ab821a7762?sa=X&ved=1t:8290&ictx=111" target="_blank">Extrarradio Cortijo la Purisima, 2P, 18004 Granada</a>
                            </div>
                            <div class="contacto-item">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                                </svg>
                                <a href="tel:652921960">652 921 960</a>
                            </div>
                            <div class="contacto-item">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                                </svg>
                                <a href="mailto:metalfulsan@gmail.com">metalfulsan@gmail.com</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="footer-bottom">
                    <div class="politica-legal">
                        <a href="#aviso-legal">Aviso Legal</a>
                        <span>•</span>
                        <a href="#privacidad">Política de Privacidad</a>
                        <span>•</span>
                        <a href="#cookies">Política de Cookies</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script>
        // ==========================================
        // 1. VARIABLES GLOBALES
        // ==========================================
        let datosVentas = []; // Almacena los datos para el buscador rápido
        let btnCargarElement = null;
        let visibleCount = 5; // Cuántas se ven al principio
        const loadStep = 5;   // Cuántas se suman al dar "Ver más"

        // ==========================================
        // 2. FUNCIONES VISUALES (ICONO VS CRUZ)
        // ==========================================
        function checkInput(tipo) {
            let input = document.getElementById('filtro-' + tipo);
            let wrapper = document.getElementById('wrapper-' + tipo);
            
            // Fallback por si el ID es diferente (ej. select producto)
            if (!input) input = document.getElementById(tipo);
            if (!wrapper && input) wrapper = document.getElementById('wrapper-' + tipo);

            if (input && wrapper) {
                if (input.value.trim() !== "") {
                    wrapper.classList.add('con-valor'); // Muestra la X
                } else {
                    wrapper.classList.remove('con-valor'); // Muestra la Lupa/Icono
                }
            }
        }

        // ==========================================
        // 3. LÓGICA DE BORRADO (BOTÓN X)
        // ==========================================
        function borrarFiltro(tipo) {
            let input = document.getElementById('filtro-' + tipo);
            if (!input) input = document.getElementById(tipo);

            if (input) {
                input.value = '';
                checkInput(tipo); // Restaurar icono visualmente
                
                if (tipo === 'cliente') {
                    // Si es cliente, limpiamos sugerencias y restauramos lista completa por JS
                    document.getElementById('sugerencias-cliente').style.display = 'none';
                    restaurarListadoCompleto(); 
                } else {
                    // Si es fecha o producto, recargamos la página (PHP)
                    aplicarFiltros(); 
                }
            }
        }

        // ==========================================
        // 4. LÓGICA DE AUTOCOMPLETADO (CLIENTE)
        // ==========================================
        function mostrarSugerencias() {
            const input = document.getElementById('filtro-cliente');
            const texto = input.value.toLowerCase().trim();
            const contenedor = document.getElementById('sugerencias-cliente');
            
            contenedor.innerHTML = ''; // Limpiar lista anterior

            // Si está vacío, ocultar lista y mostrar todo normal
            if (texto.length === 0) {
                contenedor.style.display = 'none';
                restaurarListadoCompleto();
                return;
            }

            // Buscar coincidencias en memoria (evitando duplicados)
            const nombresVistos = new Set();
            const coincidencias = datosVentas.filter(venta => {
                const coincide = venta.nombreCliente.toLowerCase().includes(texto);
                if (coincide && !nombresVistos.has(venta.nombreCliente)) {
                    nombresVistos.add(venta.nombreCliente);
                    return true;
                }
                return false;
            });

            // Dibujar sugerencias
            if (coincidencias.length > 0) {
                coincidencias.forEach(venta => {
                    const div = document.createElement('div');
                    div.classList.add('item-sugerencia');
                    div.innerHTML = `<strong>${venta.nombreCliente}</strong>`;
                    
                    // Al hacer click, seleccionamos ese cliente
                    div.addEventListener('click', () => {
                        seleccionarCliente(venta.nombreCliente);
                    });
                    
                    contenedor.appendChild(div);
                });
                contenedor.style.display = 'block';
            } else {
                contenedor.style.display = 'none';
            }
        }

        function seleccionarCliente(nombreCliente) {
            // 1. Poner valor en el input
            const input = document.getElementById('filtro-cliente');
            input.value = nombreCliente;
            checkInput('cliente'); // Activar la X

            // 2. Ocultar lista desplegable
            document.getElementById('sugerencias-cliente').style.display = 'none';

            // 3. Filtrar las tarjetas de abajo
            const todasLasVentas = document.querySelectorAll('.item-venta');
            
            todasLasVentas.forEach(div => {
                // Buscamos el nombre dentro de la tarjeta (2º párrafo)
                const infoCliente = div.querySelector('.venta-info p:nth-child(2)').innerText;
                
                if (infoCliente.includes(nombreCliente)) {
                    div.style.display = 'flex';
                } else {
                    div.style.display = 'none';
                }
            });

            // Ocultar botón "Ver más" porque estamos filtrando
            if(btnCargarElement) btnCargarElement.style.display = 'none';
        }

        // ==========================================
        // 5. LÓGICA DE PAGINACIÓN (VER MÁS)
        // ==========================================
        function restaurarListadoCompleto() {
            const todasLasVentas = document.querySelectorAll('.item-venta');
            
            todasLasVentas.forEach((div, index) => {
                if (index < visibleCount) div.style.display = 'flex';
                else div.style.display = 'none';
            });

            // Controlar visibilidad del botón "Ver más"
            if(btnCargarElement) {
                if (visibleCount >= todasLasVentas.length) {
                    btnCargarElement.style.display = 'none';
                } else {
                    btnCargarElement.style.display = 'flex'; // o block
                }
            }
        }

        // ==========================================
        // 6. FILTROS DE URL (PHP - FECHA/PRODUCTO)
        // ==========================================
        function aplicarFiltros() {
            const fecha = document.getElementById('filtro-fecha').value;
            const producto = document.getElementById('producto').value;
            const inputCliente = document.getElementById('filtro-cliente');
            const cliente = inputCliente ? inputCliente.value : ''; 

            const url = new URL(window.location.href);
            
            // Actualizamos la URL sin borrar los otros parámetros
            if (fecha) url.searchParams.set('fecha', fecha); else url.searchParams.delete('fecha');
            if (producto) url.searchParams.set('producto', producto); else url.searchParams.delete('producto');
            if (cliente) url.searchParams.set('cliente', cliente); else url.searchParams.delete('cliente');

            window.location.href = url.toString();
        }

        function checkEnter(event) {
            if (event.key === "Enter") {
                // Búsqueda manual por texto
                const texto = document.getElementById('filtro-cliente').value.toLowerCase();
                document.getElementById('sugerencias-cliente').style.display = 'none';
                
                const todas = document.querySelectorAll('.item-venta');
                todas.forEach(div => {
                    if (div.innerText.toLowerCase().includes(texto)) div.style.display = 'flex';
                    else div.style.display = 'none';
                });
                if(btnCargarElement) btnCargarElement.style.display = 'none';
            }
        }

        function borrarTodo() {
            window.location.href = '../Administrador/ListadoVentasAdmin.php';
        }

        // Cerrar autocompletado si se hace click fuera
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('wrapper-cliente');
            const lista = document.getElementById('sugerencias-cliente');
            if (lista && !wrapper.contains(e.target)) {
                lista.style.display = 'none';
            }
        });

        // ==========================================
        // 7. INICIALIZACIÓN AL CARGAR
        // ==========================================
        document.addEventListener("DOMContentLoaded", function () {
            // A. Revisar si hay filtros aplicados para mostrar las X
            checkInput('fecha');
            checkInput('cliente');
            checkInput('producto');

            // B. Menú Lateral
            const botonMenu = document.querySelector(".boton-menu-lateral");
            const menuLateral = document.getElementById("menu-lateral");
            if (botonMenu && menuLateral) {
                botonMenu.addEventListener("click", () => menuLateral.classList.toggle("oculto"));
            }

            // C. Preparar datos para el buscador (Scraping del DOM)
            const itemsDOM = document.querySelectorAll('.item-venta');
            btnCargarElement = document.getElementById('btn-cargar-mas');

            itemsDOM.forEach(div => {
                // Extraemos el nombre del cliente de la tarjeta HTML
                const pCliente = div.querySelector('.venta-info p:nth-child(2)');
                let nombreLimpio = "Cliente Desconocido";
                
                if (pCliente) {
                    // Quitamos la etiqueta "Cliente:" para guardar solo el nombre
                    nombreLimpio = pCliente.innerText.replace('Cliente:', '').trim();
                }

                datosVentas.push({
                    nombreCliente: nombreLimpio,
                    elemento: div
                });
            });

            // D. Ejecutar paginación inicial
            restaurarListadoCompleto();

            // E. Evento Botón Cargar Más
            if (btnCargarElement) {
                btnCargarElement.addEventListener('click', function(e) {
                    e.preventDefault();
                    visibleCount += loadStep;
                    restaurarListadoCompleto();
                });
            }
        });
    </script>
</body>
</html>
