<?php
include 'conexion.php';  

// 1. CAPTURA DE FILTROS
$filtro_fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$filtro_cliente = isset($_GET['cliente']) ? $_GET['cliente'] : '';
$filtro_producto = isset($_GET['producto']) ? $_GET['producto'] : '';

// 2. CONSTRUCCIÓN DEL "WHERE" DINÁMICO
// Por defecto: últimos 6 meses si no hay filtro de fecha específico
$condiciones = [];
$params = [];

// A) Filtro Fecha
if (!empty($filtro_fecha)) {
    // Si el usuario elige una fecha específica, mostramos datos A PARTIR de esa fecha (o solo ese mes, según prefieras. Aquí >= fecha)
    $condiciones[] = "DATE(v.fecha) >= :fecha";
    $params[':fecha'] = $filtro_fecha;
} else {
    // Si no hay filtro, por defecto los últimos 6 meses
    $condiciones[] = "v.fecha >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
}

// B) Filtro Cliente (Búsqueda por nombre/apellidos)
if (!empty($filtro_cliente)) {
    // Necesitamos hacer JOIN con clientes en las consultas de gráficas
    $condiciones[] = "(cl.nombre LIKE :cliente OR cl.apellidos LIKE :cliente)";
    $params[':cliente'] = "%" . $filtro_cliente . "%";
}

// C) Filtro Categoría (Producto)
if (!empty($filtro_producto) && $filtro_producto !== 'Todos') {
    // Subconsulta para filtrar ventas que contengan productos de esa categoría
    $condiciones[] = "EXISTS (
        SELECT 1 FROM detalle_ventas dv2 
        JOIN productos p2 ON dv2.id_producto = p2.id 
        JOIN categorias c2 ON p2.id_categoria = c2.id 
        WHERE dv2.id_venta = v.id AND c2.nombre = :cat_nombre
    )";
    $params[':cat_nombre'] = $filtro_producto;
}

// Unimos todas las condiciones con "AND"
$sql_where = " WHERE " . implode(" AND ", $condiciones);


// --- A) LÓGICA GRÁFICA 1: EVOLUCIÓN (Ingresos y Pedidos) ---
$meses_espanol = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
$datos_evolucion = [];

// Inicializamos array vacío (para rellenar solo lo que traiga la BD si hay filtros estrictos)
// Opcional: Si hay filtro de fecha, no rellenamos los 6 meses vacíos, solo mostramos lo que hay.
if (empty($filtro_fecha)) {
    for ($i = 5; $i >= 0; $i--) {
        $timestamp = strtotime("-$i months");
        $mes_num = date('n', $timestamp);
        $clave = date('Y-m', $timestamp); 
        $datos_evolucion[$clave] = [
            'etiqueta' => $meses_espanol[$mes_num - 1] . " " . date('Y', $timestamp),
            'ingresos' => 0,
            'cantidad' => 0
        ];
    }
}

if (isset($conn)) {
    try {
        // Consulta Gráfica 1 CON FILTROS
        $sql_evolucion = "SELECT 
                            DATE_FORMAT(v.fecha, '%Y-%m') as mes_anio, 
                            SUM(v.total) as total_dinero, 
                            COUNT(v.id) as total_ventas 
                        FROM ventas v
                        LEFT JOIN clientes cl ON v.id_cliente = cl.id
                        $sql_where
                        GROUP BY mes_anio
                        ORDER BY mes_anio ASC";

        $stmt = $conn->prepare($sql_evolucion);
        $stmt->execute($params);

        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mes_bd = $fila['mes_anio'];
            
            // Si hay filtro de fecha específico, creamos la entrada si no existe
            if (!isset($datos_evolucion[$mes_bd])) {
                $partes = explode('-', $mes_bd); // [2023, 10]
                $mes_idx = intval($partes[1]) - 1;
                $datos_evolucion[$mes_bd] = [
                    'etiqueta' => $meses_espanol[$mes_idx] . " " . $partes[0],
                    'ingresos' => 0,
                    'cantidad' => 0
                ];
            }
            
            $datos_evolucion[$mes_bd]['ingresos'] = $fila['total_dinero'];
            $datos_evolucion[$mes_bd]['cantidad'] = $fila['total_ventas'];
        }
    } catch (PDOException $e) { /* Silencio */ }
}

// Preparar arrays JS Gráfica 1
$labels_evolucion = [];
$data_ingresos = [];
$data_cantidad = [];
foreach ($datos_evolucion as $dato) {
    $labels_evolucion[] = $dato['etiqueta'];
    $data_ingresos[] = $dato['ingresos'];
    $data_cantidad[] = $dato['cantidad'];
}

// --- B) LÓGICA GRÁFICA 2: CATEGORÍAS (Donut) ---
$labels_categorias = [];
$data_categorias = [];

if (isset($conn)) {
    try {
        // Consulta Gráfica 2 CON FILTROS
        $sql_cat = "SELECT c.nombre as categoria, SUM(dv.cantidad) as total_vendido
                    FROM detalle_ventas dv
                    JOIN ventas v ON dv.id_venta = v.id
                    LEFT JOIN clientes cl ON v.id_cliente = cl.id
                    JOIN productos p ON dv.id_producto = p.id
                    JOIN categorias c ON p.id_categoria = c.id
                    $sql_where
                    GROUP BY c.nombre
                    ORDER BY total_vendido DESC";

        $stmt_cat = $conn->prepare($sql_cat);
        $stmt_cat->execute($params);

        if ($stmt_cat->rowCount() > 0) {
            while ($fila = $stmt_cat->fetch(PDO::FETCH_ASSOC)) {
                $labels_categorias[] = $fila['categoria'];
                $data_categorias[] = $fila['total_vendido'];
            }
        } else {
            $labels_categorias = ['Sin Datos'];
            $data_categorias = [1]; // Dummy para que salga algo
        }
    } catch (PDOException $e) {
        $labels_categorias = ['Error'];
        $data_categorias = [1];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Administrador - Metalful</title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/administrador.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="inicio-administrador">
        
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
                    <a href="listadoproductosadmin.php">Productos</a>
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
            
            <h1 class="titulo-principal">Resumen de ventas</h1>
            
            <div class="filtros-en-titulo">
                <div class="filtro-item">
                    <label for="filtro-fecha">Fecha de registro</label>
                    <div class="filtro-wrapper" id="wrapper-fecha">
                        <input type="date" id="filtro-fecha" value="<?php echo $filtro_fecha; ?>" onchange="checkInput('fecha'); aplicarFiltros()">
                        <div class="input-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg></div>
                        <button type="button" class="btn-borrar" onclick="borrarFiltro('fecha')">×</button>
                    </div>
                </div>

                <div class="filtro-item">
                    <label for="filtro-cliente">Nombre cliente</label>
                    <div class="filtro-wrapper" id="wrapper-cliente">
                        <input type="text" id="filtro-cliente" placeholder="Buscar..." value="<?php echo htmlspecialchars($filtro_cliente); ?>" autocomplete="off" oninput="checkInput('cliente'); mostrarSugerencias()" onkeypress="checkEnter(event)">
                        <div class="input-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></div>
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
                        <div class="input-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg></div>
                        <button type="button" class="btn-borrar" onclick="borrarFiltro('producto')">×</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="diagrama-section">
            
            <h2 class="titulo-estadisticas">
                <?php 
                if (!empty($filtro_fecha) || !empty($filtro_cliente) || !empty($filtro_producto)) {
                    echo "Resultados filtrados";
                } else {
                    echo "Estadísticas de los últimos 6 meses";
                }
                ?>
            </h2>

            <div class="dashboard-grid">
                <div class="grafica-container">
                    <canvas id="graficaVentas"></canvas>
                </div>
                <div class="grafica-container">
                    <canvas id="graficaCategorias"></canvas>
                </div>
            </div>
        </div>

        <div class="cards-grid">
            <a href="listadoproductosadmin.php" class="card">
                <img src="imagenes/4.png" alt="Productos" class="card-icon">
                <h3>Productos</h3>
            </a>
            <a href="crearproductoadmin.php" class="card">
                <img src="imagenes/2.png" alt="Crear Producto" class="card-icon">
                <h3>Crear Producto</h3>
            </a>
            <a href="listadoclientesadmin.php" class="card">
                <img src="imagenes/3.png" alt="Clientes" class="card-icon">
                <h3>Clientes</h3>
            </a>
            <a href="listadoventasadmin.php" class="card">
                <img src="imagenes/5.png" alt="Ventas" class="card-icon">
                <h3>Ventas</h3>
            </a>
        </div>
        
        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-logo-section">
                        <div class="logo-footer"><img src="imagenes/footer.png" alt="Logo Metalful"></div>
                        <div class="redes">
                            <a href="https://www.instagram.com/metalfulsansl/" target="_blank" class="instagram-link"><svg viewBox="0 0 24 24" fill="white"><path d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z" /></svg></a>
                        </div>
                    </div>
                    <div style="color:white; text-align:center;">&copy; Metalistería Fulsan</div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // CONFIGURACIÓN FUENTE GLOBAL
        Chart.defaults.font.family = "'Poppins', sans-serif";

        // DATOS DESDE PHP (Ahora ya filtrados)
        const etiquetasEvolucion = <?php echo json_encode($labels_evolucion); ?>;
        const datosIngresos = <?php echo json_encode($data_ingresos); ?>;
        const datosCantidad = <?php echo json_encode($data_cantidad); ?>;
        
        const etiquetasCategorias = <?php echo json_encode($labels_categorias); ?>;
        const datosCategorias = <?php echo json_encode($data_categorias); ?>;

        document.addEventListener("DOMContentLoaded", function () {
            
            // --- GRÁFICA 1: EVOLUCIÓN ---
            const ctx1 = document.getElementById('graficaVentas').getContext('2d');
            let gradiente = ctx1.createLinearGradient(0, 0, 0, 400);
            gradiente.addColorStop(0, 'rgba(41, 54, 97, 0.9)');
            gradiente.addColorStop(1, 'rgba(41, 54, 97, 0.4)');

            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: etiquetasEvolucion,
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
                            type: 'line',
                            borderColor: '#a0d2ac',
                            backgroundColor: '#a0d2ac',
                            borderWidth: 3,
                            pointBackgroundColor: 'white',
                            pointRadius: 5,
                            tension: 0.4,
                            order: 1,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: { display: true, text: 'Evolución de Ventas', font: { size: 16, weight: '600' }, color: '#293661' },
                        legend: { position: 'bottom' }
                    },
                    scales: {
                        y: { type: 'linear', position: 'left', beginAtZero: true, grid: { color: '#f0f0f0' } },
                        y1: { type: 'linear', position: 'right', beginAtZero: true, grid: { drawOnChartArea: false } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // --- GRÁFICA 2: CATEGORÍAS ---
            const ctx2 = document.getElementById('graficaCategorias').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: etiquetasCategorias,
                    datasets: [{
                        data: datosCategorias,
                        backgroundColor: ['#293661', '#a0d2ac', '#5c6bc0', '#81c784', '#1f2849', '#b2dfdb'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: { display: true, text: 'Distribución por Categoría', font: { size: 16, weight: '600' }, color: '#293661' },
                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 15 } }
                    },
                    cutout: '65%'
                }
            });

            // Inicializar inputs visuales
            checkInput('fecha'); checkInput('cliente'); checkInput('producto');
        });

        // --- FUNCIONES FILTROS ---
        function checkInput(tipo) {
            let input = document.getElementById('filtro-' + tipo);
            let wrapper = document.getElementById('wrapper-' + tipo);
            if (!input) input = document.getElementById(tipo);
            if (!wrapper && input) wrapper = document.getElementById('wrapper-' + tipo);
            if (input && wrapper) {
                if (input.value.trim() !== "") wrapper.classList.add('con-valor');
                else wrapper.classList.remove('con-valor');
            }
        }

        function borrarFiltro(tipo) {
            let input = document.getElementById('filtro-' + tipo);
            if (!input) input = document.getElementById(tipo);
            if (input) {
                input.value = '';
                checkInput(tipo);
                aplicarFiltros();
            }
        }

        function aplicarFiltros() {
            const fecha = document.getElementById('filtro-fecha').value;
            const producto = document.getElementById('producto').value;
            const inputCliente = document.getElementById('filtro-cliente');
            const cliente = inputCliente ? inputCliente.value : ''; 
            const url = new URL(window.location.href);
            
            if (fecha) url.searchParams.set('fecha', fecha); else url.searchParams.delete('fecha');
            if (producto) url.searchParams.set('producto', producto); else url.searchParams.delete('producto');
            if (cliente) url.searchParams.set('cliente', cliente); else url.searchParams.delete('cliente');
            window.location.href = url.toString();
        }

        function checkEnter(event) { if (event.key === "Enter") aplicarFiltros(); }
    </script>
</body>
</html>