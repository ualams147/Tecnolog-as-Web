<?php
include '../conexion.php';
include '../CabeceraFooter.php';

// --- 1. VERIFICACIÓN DE SESIÓN ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CORRECCIÓN: Usamos 'usuario_id' que es como lo guardas en tu Login
if (!isset($_SESSION['usuario_id'])) {
    // Si no está logueado, redirigir al login
    header("Location: ../Cliente/index.php"); // O IniciarSesion.php si ese es tu login directo
    exit;
}

$id_cliente = $_SESSION['usuario_id']; // <--- CORREGIDO AQUÍ TAMBIÉN

// --- 2. LÓGICA DE FILTRADO ---
$where = "WHERE v.id_cliente = :id_cliente";
$params = [':id_cliente' => $id_cliente];

$filtro_fecha = $_GET['fecha'] ?? '';
$filtro_estado = $_GET['estado'] ?? 'activos'; 
$filtro_categoria = $_GET['categoria'] ?? '';

// Filtro por Fecha
if (!empty($filtro_fecha)) {
    $where .= " AND DATE(v.fecha) = :fecha";
    $params[':fecha'] = $filtro_fecha;
}

// Filtro por Estado
if ($filtro_estado === 'activos') {
    $where .= " AND v.estado NOT IN ('Entregado', 'Cancelado')";
} elseif ($filtro_estado === 'historial') {
    $where .= " AND v.estado IN ('Entregado', 'Cancelado')";
}

// Filtro por Categoría
if (!empty($filtro_categoria)) {
    $where .= " AND EXISTS (
        SELECT 1 FROM detalle_ventas dv 
        JOIN productos p ON dv.id_producto = p.id 
        JOIN categorias cat ON p.id_categoria = cat.id 
        WHERE dv.id_venta = v.id AND cat.nombre = :cat_nombre
    )";
    $params[':cat_nombre'] = $filtro_categoria;
}

// --- 3. CONSULTA PRINCIPAL ---
$sql = "SELECT v.* FROM ventas v
        $where
        ORDER BY v.fecha DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$mis_pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_registros = count($mis_pedidos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - Metalful</title>
    <link rel="icon" type="image/png" href="../imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../css/styles.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="pagina-pedidos">
    
    <div class="page-wrapper">
        <?php sectionheader(1); ?>

        <main class="main-content">
            <div class="container">
                
                <div class="titulo-seccion">
                    <h1>Mis Pedidos</h1>
                    <p>Gestiona y revisa el estado de tus compras</p>
                </div>

                <div class="filtros-container">
                    
                    <div class="filtro-item">
                        <label for="filtro-fecha">Fecha del pedido</label>
                        <div class="filtro-wrapper" id="wrapper-fecha">
                            <input type="date" id="filtro-fecha" value="<?php echo $filtro_fecha; ?>" onchange="checkInput('fecha'); aplicarFiltros()">
                            <div class="input-icon">
                                <i class="far fa-calendar-alt"></i>
                            </div>
                            <button type="button" class="btn-borrar" onclick="borrarFiltro('fecha')">×</button>
                        </div>
                    </div>

                    <div class="filtro-item">
                        <label for="filtro-estado">Estado del pedido</label>
                        <div class="filtro-wrapper con-valor" id="wrapper-estado">
                            <select id="filtro-estado" onchange="checkInput('estado'); aplicarFiltros()">
                                <option value="activos" <?php echo $filtro_estado == 'activos' ? 'selected' : ''; ?>>Pedidos Activos</option>
                                <option value="historial" <?php echo $filtro_estado == 'historial' ? 'selected' : ''; ?>>Historial (Entregados)</option>
                                <option value="todos" <?php echo $filtro_estado == 'todos' ? 'selected' : ''; ?>>Todos los pedidos</option>
                            </select>
                            <div class="input-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                        </div>
                    </div>

                    <div class="filtro-item">
                        <label for="producto">Categoría</label>
                        <div class="filtro-wrapper" id="wrapper-producto">
                            <select id="producto" name="producto" onchange="checkInput('producto'); aplicarFiltros()"> 
                                <option value="" selected>Todas</option>
                                <option value="ventanas" <?php echo $filtro_categoria == 'ventanas' ? 'selected' : ''; ?>>Ventanas</option>
                                <option value="puertas" <?php echo $filtro_categoria == 'puertas' ? 'selected' : ''; ?>>Puertas</option>
                                <option value="barandillas" <?php echo $filtro_categoria == 'barandillas' ? 'selected' : ''; ?>>Barandillas</option>
                                <option value="otros" <?php echo $filtro_categoria == 'otros' ? 'selected' : ''; ?>>Otros</option>
                            </select>
                            <div class="input-icon">
                                <i class="fas fa-filter"></i>
                            </div>
                            <button type="button" class="btn-borrar" onclick="borrarFiltro('producto')">×</button>
                        </div>
                    </div>
                </div>

                <div class="grid-pedidos">
                    <?php if ($total_registros == 0): ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <h3>No se encontraron pedidos</h3>
                            <p>No tienes pedidos que coincidan con los filtros seleccionados.</p>
                            <?php if(!empty($filtro_fecha) || $filtro_estado != 'activos' || !empty($filtro_categoria)): ?>
                                <button onclick="borrarTodo()" style="margin-top:15px; background:#293661; color:white; padding:8px 15px; border:none; border-radius:5px; cursor:pointer;">Restablecer Filtros</button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php foreach ($mis_pedidos as $pedido): 
                            // Lógica visual para el estado
                            $estado = $pedido['estado'] ?? 'Pendiente';
                            $clase_estado = 'estado-default';
                            if(stripos($estado, 'Entregado') !== false) $clase_estado = 'estado-entregado';
                            elseif(stripos($estado, 'Pendiente') !== false) $clase_estado = 'estado-pendiente';
                            elseif(stripos($estado, 'Cancelado') !== false) $clase_estado = 'estado-cancelado';
                            elseif(stripos($estado, 'Proceso') !== false) $clase_estado = 'estado-proceso';
                        ?>
                            <div class="card-pedido">
                                <div class="card-header-pedido">
                                    <span class="pedido-id">#<?php echo str_pad($pedido['id'], 4, '0', STR_PAD_LEFT); ?></span>
                                    <span class="estado-badge <?php echo $clase_estado; ?>"><?php echo $estado; ?></span>
                                </div>
                                <div class="card-body-pedido">
                                    <p><i class="far fa-calendar"></i> <?php echo date('d/m/Y', strtotime($pedido['fecha'])); ?></p>
                                    <p class="precio-total"><?php echo number_format($pedido['total'], 2); ?> €</p>
                                </div>
                                <a href="../Cliente/DetallesPedido.php?id=<?php echo $pedido['id']; ?>" class="btn-ver-detalles">
                                    Ver Detalles <i class="fas fa-arrow-right" style="margin-left:5px; font-size:12px;"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </main>

        <?php sectionfooter(); ?>
    </div>

    <script>
        function checkInput(tipo) {
            let input = document.getElementById('filtro-' + tipo);
            let wrapper = document.getElementById('wrapper-' + tipo);
            
            if (!input && tipo === 'producto') input = document.getElementById('producto');
            
            if (input && wrapper) {
                if (input.value.trim() !== "") {
                    wrapper.classList.add('con-valor');
                } else {
                    wrapper.classList.remove('con-valor');
                }
            }
        }

        function borrarFiltro(tipo) {
            let input = document.getElementById('filtro-' + tipo);
            if (!input && tipo === 'producto') input = document.getElementById('producto');

            if (input) {
                input.value = '';
                checkInput(tipo);
                aplicarFiltros();
            }
        }

        function aplicarFiltros() {
            const fecha = document.getElementById('filtro-fecha').value;
            const estado = document.getElementById('filtro-estado').value;
            const producto = document.getElementById('producto').value;

            const url = new URL(window.location.href);
            
            if (fecha) url.searchParams.set('fecha', fecha); else url.searchParams.delete('fecha');
            if (estado) url.searchParams.set('estado', estado); else url.searchParams.delete('estado');
            if (producto) url.searchParams.set('categoria', producto); else url.searchParams.delete('categoria');

            window.location.href = url.toString();
        }

        function borrarTodo() {
            const url = new URL(window.location.href);
            url.search = '';
            url.searchParams.set('estado', 'activos');
            window.location.href = url.toString();
        }

        document.addEventListener("DOMContentLoaded", function () {
            checkInput('fecha');
            checkInput('estado');
            checkInput('producto');
        });
    </script>
</body>
</html>