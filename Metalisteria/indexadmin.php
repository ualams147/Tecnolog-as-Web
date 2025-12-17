<?php
include 'conexion.php'; 
require 'seguridad_admin.php'; 

// 1. CAPTURA DE FILTROS
$filtro_fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$filtro_cliente = isset($_GET['cliente']) ? $_GET['cliente'] : '';
$filtro_producto = isset($_GET['producto']) ? $_GET['producto'] : '';

// 2. CONSTRUCCIÓN DEL "WHERE" DINÁMICO
$condiciones = ["1=1"];
$params = [];

if (!empty($filtro_fecha)) {
    $condiciones[] = "DATE(v.fecha) >= :fecha";
    $params[':fecha'] = $filtro_fecha;
} else {
    $condiciones[] = "v.fecha >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
}

if (!empty($filtro_cliente)) {
    $condiciones[] = "(cl.nombre LIKE :cliente OR cl.apellidos LIKE :cliente)";
    $params[':cliente'] = "%" . $filtro_cliente . "%";
}

if (!empty($filtro_producto)) {
    $condiciones[] = "EXISTS (
        SELECT 1 FROM detalle_ventas dv2 
        JOIN productos p2 ON dv2.id_producto = p2.id 
        JOIN categorias c2 ON p2.id_categoria = c2.id 
        WHERE dv2.id_venta = v.id AND c2.nombre = :cat_nombre
    )";
    $params[':cat_nombre'] = $filtro_producto;
}

$sql_where = " WHERE " . implode(" AND ", $condiciones);

// --- A) LÓGICA GRÁFICA 1: EVOLUCIÓN ---
$meses_espanol = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
$datos_evolucion = [];

// Rellenar meses (solo si no hay un filtro de fecha muy específico)
if (empty($filtro_fecha)) {
    for ($i = 5; $i >= 0; $i--) {
        $timestamp = strtotime("-$i months");
        $mes_num = date('n', $timestamp);
        $clave = date('Y-m', $timestamp); 
        $datos_evolucion[$clave] = [
            'etiqueta' => $meses_espanol[$mes_num - 1],
            'ingresos' => 0, 'cantidad' => 0
        ];
    }
}

try {
    $sql_evolucion = "SELECT 
                        DATE_FORMAT(v.fecha, '%Y-%m') as mes_anio, 
                        SUM(v.total) as total_dinero, 
                        COUNT(v.id) as total_ventas 
                    FROM ventas v
                    LEFT JOIN clientes cl ON v.id_cliente = cl.id
                    $sql_where
                    GROUP BY mes_anio ORDER BY mes_anio ASC";

    $stmt = $conn->prepare($sql_evolucion);
    $stmt->execute($params);

    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $mes_bd = $fila['mes_anio'];
        if (!isset($datos_evolucion[$mes_bd])) {
            $partes = explode('-', $mes_bd);
            $datos_evolucion[$mes_bd] = ['etiqueta' => $meses_espanol[intval($partes[1])-1], 'ingresos' => 0, 'cantidad' => 0];
        }
        $datos_evolucion[$mes_bd]['ingresos'] = $fila['total_dinero'];
        $datos_evolucion[$mes_bd]['cantidad'] = $fila['total_ventas'];
    }
} catch (PDOException $e) { }

$labels_evolucion = []; $data_ingresos = []; $data_cantidad = [];
foreach ($datos_evolucion as $dato) {
    $labels_evolucion[] = $dato['etiqueta'];
    $data_ingresos[] = $dato['ingresos'];
    $data_cantidad[] = $dato['cantidad'];
}

// --- B) LÓGICA GRÁFICA 2: CATEGORÍAS ---
$labels_categorias = []; $data_categorias = [];
try {
    $sql_cat = "SELECT c.nombre as categoria, SUM(dv.cantidad) as total_vendido
                FROM detalle_ventas dv
                JOIN ventas v ON dv.id_venta = v.id
                LEFT JOIN clientes cl ON v.id_cliente = cl.id
                JOIN productos p ON dv.id_producto = p.id
                JOIN categorias c ON p.id_categoria = c.id
                $sql_where
                GROUP BY c.nombre ORDER BY total_vendido DESC";
    $stmt_cat = $conn->prepare($sql_cat);
    $stmt_cat->execute($params);
    while ($fila = $stmt_cat->fetch(PDO::FETCH_ASSOC)) {
        $labels_categorias[] = $fila['categoria'];
        $data_categorias[] = $fila['total_vendido'];
    }
    if (empty($labels_categorias)) { $labels_categorias = ['Sin Datos']; $data_categorias = [0]; }
} catch (PDOException $e) { }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Metalful</title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/administrador.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Sincronización manual por si el CSS difiere */
        .ListadoVentasAdmin .filtros-en-titulo { bottom: 60px; }
    </style>
</head>
<body>
    <div class="ListadoVentasAdmin"> <header class="cabecera">
            <div class="container">
                <div class="logo-main">
                    <a href="indexadmin.php" class="logo-main">
                        <img src="imagenes/logo.png" alt="Logo">
                        <div class="logo-text"><span> Metalisteria</span><strong>Fulsan</strong></div>
                    </a>
                </div>
                <nav class="nav-bar">
                    <a href="listadoventasadmin.php">Ventas</a>
                    <a href="listadoproductosadmin.php">Productos</a>
                    <a href="listadoclientesadmin.php">Clientes</a>
                </nav>
                <div class="log-out"><a href="index.php">Cerrar Sesión</a></div>
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
                        <input type="text" id="filtro-cliente" placeholder="Buscar..." value="<?php echo htmlspecialchars($filtro_cliente); ?>" autocomplete="off" oninput="checkInput('cliente')" onkeypress="checkEnter(event)">
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
                            <option value="ventanas" <?php if($filtro_producto == 'ventanas') echo 'selected'; ?>>Ventanas</option>
                            <option value="puertas" <?php if($filtro_producto == 'puertas') echo 'selected'; ?>>Puertas</option>
                            <option value="barandillas" <?php if($filtro_producto == 'barandillas') echo 'selected'; ?>>Barandillas</option>
                            <option value="otros" <?php if($filtro_producto == 'otros') echo 'selected'; ?>>Otros</option>
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
            <h2 class="titulo-estadisticas">
                <?php echo (!empty($filtro_fecha) || !empty($filtro_cliente) || !empty($filtro_producto)) ? "Resultados filtrados" : "Estadísticas de los últimos 6 meses"; ?>
            </h2>
            <div class="dashboard-grid">
                <div class="grafica-container"><canvas id="graficaVentas"></canvas></div>
                <div class="grafica-container"><canvas id="graficaCategorias"></canvas></div>
            </div>
        </div>

        <div class="cards-grid">
            <a href="listadoproductosadmin.php" class="card"><img src="imagenes/4.png" alt="P" class="card-icon"><h3>Productos</h3></a>
            <a href="crearproductoadmin.php" class="card"><img src="imagenes/2.png" alt="C" class="card-icon"><h3>Crear Producto</h3></a>
            <a href="listadoclientesadmin.php" class="card"><img src="imagenes/3.png" alt="Cl" class="card-icon"><h3>Clientes</h3></a>
            <a href="listadoventasadmin.php" class="card"><img src="imagenes/5.png" alt="V" class="card-icon"><h3>Ventas</h3></a>
        </div>
        
        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-logo-section">
                        <div class="logo-footer">
                            <img src="imagenes/footer.png" alt="Logo Metalful">
                        </div>
                        <div class="redes">
                            <a href="https://www.instagram.com/metalfulsansl/" target="_blank" class="instagram-link">
                                <svg viewBox="0 0 24 24" fill="white">
                                    <path d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="footer-links">
                        <div class="contacto-footer">
                            <h3>Contacto</h3>
                            <div class="contacto-item">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                <a href="https://maps.google.com" target="_blank">Extrarradio Cortijo la Purisima, 2P, 18004 Granada</a>
                            </div>

                            <div class="contacto-item">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                                </svg>
                                <a href="tel:652921960">652 921 960</a>
                            </div>

                            <div class="contacto-item">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
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
        Chart.defaults.font.family = "'Poppins', sans-serif";
        const etiquetasEvolucion = <?php echo json_encode($labels_evolucion); ?>;
        const datosIngresos = <?php echo json_encode($data_ingresos); ?>;
        const datosCantidad = <?php echo json_encode($data_cantidad); ?>;
        const etiquetasCategorias = <?php echo json_encode($labels_categorias); ?>;
        const datosCategorias = <?php echo json_encode($data_categorias); ?>;

        document.addEventListener("DOMContentLoaded", function () {
            // Gráfica 1
            const ctx1 = document.getElementById('graficaVentas').getContext('2d');
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: etiquetasEvolucion,
                    datasets: [
                        { label: 'Ingresos (€)', data: datosIngresos, backgroundColor: 'rgba(41, 54, 97, 0.8)', borderColor: '#293661', borderWidth: 1, borderRadius: 5, yAxisID: 'y' },
                        { label: 'Nº Pedidos', data: datosCantidad, type: 'line', borderColor: '#a0d2ac', borderWidth: 3, tension: 0.4, yAxisID: 'y1' }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true }, y1: { position: 'right', beginAtZero: true, grid: { display: false } } } }
            });

            // Gráfica 2
            const ctx2 = document.getElementById('graficaCategorias').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: { labels: etiquetasCategorias, datasets: [{ data: datosCategorias, backgroundColor: ['#293661', '#a0d2ac', '#5c6bc0', '#81c784', '#1f2849'] }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '70%' }
            });

            checkInput('fecha'); checkInput('cliente'); checkInput('producto');
        });

        function checkInput(tipo) {
            let input = document.getElementById('filtro-' + tipo) || document.getElementById(tipo);
            let wrapper = document.getElementById('wrapper-' + tipo);
            if (input && wrapper) {
                if (input.value.trim() !== "") wrapper.classList.add('con-valor');
                else wrapper.classList.remove('con-valor');
            }
        }

        function borrarFiltro(tipo) {
            let input = document.getElementById('filtro-' + tipo) || document.getElementById(tipo);
            if (input) { input.value = ''; aplicarFiltros(); }
        }

        function aplicarFiltros() {
            const f = document.getElementById('filtro-fecha').value;
            const c = document.getElementById('filtro-cliente').value;
            const p = document.getElementById('producto').value;
            const url = new URL(window.location.href);
            if (f) url.searchParams.set('fecha', f); else url.searchParams.delete('fecha');
            if (c) url.searchParams.set('cliente', c); else url.searchParams.delete('cliente');
            if (p) url.searchParams.set('producto', p); else url.searchParams.delete('producto');
            window.location.href = url.toString();
        }

        function checkEnter(e) { if (e.key === "Enter") aplicarFiltros(); }
    </script>
</body>
</html>