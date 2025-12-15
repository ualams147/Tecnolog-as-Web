<?php
// 1. INCLUIR CONEXIÓN A BASE DE DATOS
// Asegúrate de que esta ruta es correcta. Si tu archivo está en otra carpeta, ajustalo.
include '../Config/conexion.php'; 
// (O como se llame tu archivo de conexión. La variable de conexión suele ser $con, $link o $conexion)

// Inicialización de variables de filtros (para que no den error abajo)
$filtro_fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$filtro_cliente = isset($_GET['cliente']) ? $_GET['cliente'] : '';

// =======================================================================
// LÓGICA PARA LA GRÁFICA (ÚLTIMOS 6 MESES REALES)
// =======================================================================

// 1. Generar los últimos 6 meses vacíos (para que salgan aunque no haya ventas)
$meses_espanol = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
$datos_grafica = [];

for ($i = 5; $i >= 0; $i--) {
    // Calculamos fecha restando meses desde hoy
    $timestamp = strtotime("-$i months");
    $mes_num = date('n', $timestamp); // 1 al 12
    $anio = date('Y', $timestamp);
    $clave = date('Y-m', $timestamp); // Ej: "2023-10" (Usado para comparar con la BD)
    
    // Estructura base inicializada a 0
    $datos_grafica[$clave] = [
        'etiqueta' => $meses_espanol[$mes_num - 1], // Nombre del mes en español
        'ingresos' => 0,
        'cantidad' => 0
    ];
}

// 2. Consulta a la Base de Datos
// IMPORTANTE: Cambia 'ventas', 'fecha' y 'precio' por los nombres reales de tu tabla
if (isset($conexion)) { // Verificamos que existe la conexión
    
    // Esta consulta agrupa por Año-Mes y suma el dinero y cuenta los pedidos
    $sql = "SELECT 
                DATE_FORMAT(fecha, '%Y-%m') as mes_anio, 
                SUM(precio) as total_dinero, 
                COUNT(*) as total_ventas 
            FROM ventas 
            WHERE fecha >= DATE_SUB(NOW(), INTERVAL 6 MONTH) 
            GROUP BY mes_anio";

    $resultado = mysqli_query($conexion, $sql);

    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $mes_bd = $fila['mes_anio']; // Ej: "2023-10"
            
            // Si este mes existe en nuestro array de últimos 6 meses, actualizamos los datos
            if (isset($datos_grafica[$mes_bd])) {
                $datos_grafica[$mes_bd]['ingresos'] = $fila['total_dinero'];
                $datos_grafica[$mes_bd]['cantidad'] = $fila['total_ventas'];
            }
        }
    }
}

// 3. Separar los datos en arrays simples para Chart.js
$etiquetas_finales = [];
$data_ingresos_final = [];
$data_cantidad_final = [];

foreach ($datos_grafica as $dato) {
    $etiquetas_finales[] = $dato['etiqueta'];
    $data_ingresos_final[] = $dato['ingresos'];
    $data_cantidad_final[] = $dato['cantidad'];
}
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
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="inicio-administrador">
        
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
                            <option value="rejas">Rejas</option>
                        </select>
                        
                        <div class="input-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        </div>

                        <button type="button" class="btn-borrar" onclick="borrarFiltro('producto')">×</button>
                    </div>
                </div>

            </div>
        </div>

        <div class="diagrama-section">
            <div class="grafica-container" style="position: relative; height: 400px; padding: 20px; background: white;">
                <canvas id="graficaVentas"></canvas>
            </div>
        </div>

        <div class="cards-grid">
            <a href="../Administrador/ListadoProductosAdmin.php" class="card">
                <img src="../imagenes/4.png" alt="Productos" class="card-icon">
                <h3>Productos</h3>
            </a>

            <a href="../Administrador/CrearProductoAdmin.php" class="card">
                <img src="../imagenes/2.png" alt="Crear Producto" class="card-icon">
                <h3>Crear Producto</h3>
            </a>

            <a href="../Administrador/ListadoClientesAdmin.php" class="card">
                <img src="../imagenes/3.png" alt="Clientes" class="card-icon">
                <h3>Clientes</h3>
            </a>

            <a href="../Administrador/ListadoVentasAdmin.php" class="card">
                <img src="../imagenes/5.png" alt="Ventas" class="card-icon">
                <h3>Ventas</h3>
            </a>
        </div>
        
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
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" /></svg>
                                <a href="https://www.google.com/maps/place//data=!4m2!3m1!1s0xd71fd00684554b1:0xef4e70ab821a7762?sa=X&ved=1t:8290&ictx=111" target="_blank">Extrarradio Cortijo la Purisima, 2P, 18004 Granada</a>
                            </div>
                            <div class="contacto-item">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" /></svg>
                                <a href="tel:652921960">652 921 960</a>
                            </div>
                            <div class="contacto-item">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" /></svg>
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
        // --- 1. CONFIGURACIÓN GRÁFICA (Chart.js) ---
        // Pasamos los datos de PHP a variables de JavaScript
        const etiquetasChart = <?php echo json_encode($etiquetas_finales); ?>;
        const datosIngresos = <?php echo json_encode($data_ingresos_final); ?>;
        const datosCantidad = <?php echo json_encode($data_cantidad_final); ?>;

        document.addEventListener("DOMContentLoaded", function () {
            // Inicializar Gráfica
            const ctx = document.getElementById('graficaVentas').getContext('2d');
            
            // Crear degradado azul corporativo
            let gradiente = ctx.createLinearGradient(0, 0, 0, 400);
            gradiente.addColorStop(0, 'rgba(41, 54, 97, 0.9)'); // Azul fuerte arriba
            gradiente.addColorStop(1, 'rgba(41, 54, 97, 0.4)'); // Azul suave abajo

            new Chart(ctx, {
                type: 'bar', // Tipo principal: Barras
                data: {
                    labels: etiquetasChart,
                    datasets: [
                        {
                            label: 'Ingresos (€)',
                            data: datosIngresos,
                            backgroundColor: gradiente,
                            borderColor: '#293661',
                            borderWidth: 1,
                            borderRadius: 6,
                            order: 2,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Nº Pedidos',
                            data: datosCantidad,
                            type: 'line', // Línea combinada
                            borderColor: '#a0d2ac', // Verde corporativo
                            backgroundColor: '#a0d2ac',
                            borderWidth: 3,
                            pointBackgroundColor: 'white',
                            pointBorderColor: '#a0d2ac',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            tension: 0.4, // Curva suave
                            order: 1,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Evolución de Ingresos y Pedidos (Últimos 6 Meses)',
                            font: { size: 18, family: "'Poppins', sans-serif", weight: '600' },
                            color: '#293661',
                            padding: { bottom: 20 }
                        },
                        legend: { position: 'bottom' },
                        tooltip: {
                            backgroundColor: 'rgba(41, 54, 97, 0.9)',
                            padding: 10,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            type: 'linear',
                            position: 'left',
                            title: { display: true, text: 'Ingresos (€)' },
                            grid: { color: '#f0f0f0' }
                        },
                        y1: {
                            beginAtZero: true,
                            type: 'linear',
                            position: 'right',
                            title: { display: true, text: 'Cantidad' },
                            grid: { drawOnChartArea: false } // Ocultar rejilla derecha para limpieza
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // --- 2. RESTO DE FUNCIONALIDADES DE INICIO ---
            // (Revisar inputs, menú móvil, etc.)
            checkInput('fecha');
            checkInput('cliente');
            checkInput('producto');

            const botonMenu = document.querySelector(".boton-menu-lateral");
            const menuLateral = document.getElementById("menu-lateral");
            if (botonMenu && menuLateral) {
                botonMenu.addEventListener("click", () => menuLateral.classList.toggle("oculto"));
            }
        });

        // --- 3. FUNCIONES DE INTERFAZ Y FILTROS ---
        
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
                    wrapper.classList.remove('con-valor'); // Muestra el Icono
                }
            }
        }

        function borrarFiltro(tipo) {
            let input = document.getElementById('filtro-' + tipo);
            if (!input) input = document.getElementById(tipo);

            if (input) {
                input.value = '';
                checkInput(tipo); // Restaurar icono visualmente
                
                if (tipo === 'cliente') {
                    document.getElementById('sugerencias-cliente').style.display = 'none';
                    aplicarFiltros();
                } else {
                    aplicarFiltros(); 
                }
            }
        }

        // --- Autocompletado Simple para Cliente ---
        // (Nota: En una app real, esto haría peticiones AJAX a la base de datos)
        // Aquí simulamos que si escribes algo y das enter, filtra.
        
        function mostrarSugerencias() {
            // Lógica de autocompletado visual
            const input = document.getElementById('filtro-cliente');
            const wrapper = document.getElementById('wrapper-cliente');
            const lista = document.getElementById('sugerencias-cliente');
            
            if (input.value.length > 0) {
                // Aquí podrías mostrar div con resultados si tuvieras la lista cargada
                // Por ahora, solo activamos la X
            } else {
                lista.style.display = 'none';
            }
        }

        function checkEnter(event) {
            if (event.key === "Enter") {
                aplicarFiltros();
            }
        }

        // --- Aplicar Filtros (Recargar URL) ---
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

        // Cerrar autocompletado al clicar fuera
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('wrapper-cliente');
            const lista = document.getElementById('sugerencias-cliente');
            if (lista && wrapper && !wrapper.contains(e.target)) {
                lista.style.display = 'none';
            }
        });
    </script>
</body>
</html>