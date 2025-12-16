<?php
// --- CONFIGURACIÓN DE RUTAS ---
require_once 'conexion.php';      
require_once 'CabeceraFooter.php'; 

// Verificación de conexión
if (!isset($conn)) {
    die("Error crítico: No existe la variable \$conn. Revisa tu archivo conexion.php.");
}

// --- 1. SESIÓN ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: iniciarsesion.php"); 
    exit;
}

$id_cliente = $_SESSION['usuario_id'];

// --- 2. FILTROS ---
$where = "WHERE v.id_cliente = :id_cliente";
$params = [':id_cliente' => $id_cliente];

$filtro_fecha = $_GET['fecha'] ?? '';
$filtro_estado = $_GET['estado'] ?? 'activos'; 
$filtro_categoria = $_GET['categoria'] ?? '';

// Filtro Fecha
if (!empty($filtro_fecha)) {
    $where .= " AND DATE(v.fecha) = :fecha";
    $params[':fecha'] = $filtro_fecha;
}

// Filtro Estado
if ($filtro_estado === 'activos') {
    $where .= " AND v.estado NOT IN ('Entregado', 'Cancelado')";
} elseif ($filtro_estado === 'historial') {
    $where .= " AND v.estado IN ('Entregado', 'Cancelado')";
}

// Filtro Categoría
if (!empty($filtro_categoria)) {
    $where .= " AND EXISTS (
        SELECT 1 FROM detalle_ventas dv 
        JOIN productos p ON dv.id_producto = p.id 
        JOIN categorias cat ON p.id_categoria = cat.id 
        WHERE dv.id_venta = v.id AND cat.nombre = :cat_nombre
    )";
    $params[':cat_nombre'] = $filtro_categoria;
}

// --- 3. CONSULTA ---
$sql = "SELECT v.* FROM ventas v
        $where
        ORDER BY v.fecha DESC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $mis_pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_registros = count($mis_pedidos);
} catch (PDOException $e) {
    die("Error SQL: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - Metalful</title>
    
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="css/styles.css">

    <style>
        :root {
            --primary: #293661; /* Azul oscuro */
            --secondary: #a0d2ac; /* <-- TU COLOR (Verde suave) */
            --bg-light: #f4f6f9;
            --white: #ffffff;
        }
        body.pagina-pedidos { background-color: var(--bg-light); font-family: 'Poppins', sans-serif; }
        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        .titulo-seccion { text-align: center; margin-bottom: 40px; }
        .titulo-seccion h1 { color: var(--primary); font-size: 2.5rem; margin-bottom: 10px; font-weight: 700; }
        .filtros-container { background: var(--white); padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 30px; align-items: flex-end; }
        .filtro-item { flex: 1; min-width: 200px; }
        .filtro-item label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--primary); }
        
        /* Borde al seleccionar */
        .filtro-wrapper { position: relative; display: flex; align-items: center; border: 2px solid #e0e0e0; border-radius: 8px; transition: all 0.3s ease;}
        .filtro-wrapper:focus-within { border-color: var(--secondary); box-shadow: 0 0 0 3px rgba(160, 210, 172, 0.2); }

        .filtro-wrapper input, .filtro-wrapper select { width: 100%; padding: 12px 40px 12px 15px; border: none; background: transparent; outline: none; }
        .input-icon { position: absolute; right: 15px; color: #999; pointer-events: none; }
        .btn-borrar { position: absolute; right: 35px; background: none; border: none; color: #ff4d4d; cursor: pointer; display: none; }
        .filtro-wrapper.con-valor .btn-borrar { display: block; }
        .grid-pedidos { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
        .card-pedido { background: var(--white); border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; display: flex; flex-direction: column; transition: transform 0.3s; }
        .card-pedido:hover { transform: translateY(-5px); }
        .card-header-pedido { padding: 15px 20px; background: #f8f9fa; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        .pedido-id { font-weight: 700; color: var(--primary); }
        .estado-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; }
        .estado-pendiente { background: #fff3cd; color: #856404; }
        .estado-entregado { background: #d4edda; color: #155724; }
        .estado-cancelado { background: #f8d7da; color: #721c24; }
        .estado-default { background: #e2e3e5; color: #383d41; }
        .card-body-pedido { padding: 20px; flex-grow: 1; }
        .precio-total { font-size: 1.4rem; font-weight: 700; color: var(--primary); margin-top: 15px !important; }
        
        /* Botón con tu nuevo color */
        .btn-ver-detalles { display: block; background: var(--primary); color: var(--white); text-align: center; padding: 12px; margin: 20px; margin-top: 0; border-radius: 8px; text-decoration: none; font-weight: 600; transition: background 0.3s ease; }
        .btn-ver-detalles:hover { background: var(--secondary); color: #fff; }
        
        .empty-state { grid-column: 1 / -1; text-align: center; padding: 60px; color: #888; }
        .empty-state i { font-size: 4rem; margin-bottom: 20px; color: #ddd; }

        /* ESTILOS NUEVOS PARA LA FLECHA DE VOLVER */
        .btn-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background-color: var(--secondary);
            border-radius: 50%;
            color: white;
            text-decoration: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 20px; /* Separación con el título */
        }
        .btn-back:hover {
            background-color: var(--primary);
            transform: translateX(-5px); /* Efecto de moverse a la izquierda */
        }
        .btn-back svg {
            width: 24px;
            height: 24px;
            fill: white;
        }
    </style>
</head>
<body class="pagina-pedidos">
    
    <div class="page-wrapper">
        <?php 
        if(function_exists('sectionheader')) {
            sectionheader(1); 
        } else {
            echo "<header style='background:#293661; color:white; padding:15px; text-align:center;'>METALFUL</header>";
        }
        ?>

        <main class="main-content">
            <div class="container">
                
                <a href="perfil.php" class="btn-back">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </a>

                <div class="titulo-seccion">
                    <h1>Mis Pedidos</h1>
                    <p>Gestiona y revisa el estado de tus compras</p>
                </div>

                <div class="filtros-container">
                    <div class="filtro-item">
                        <label for="filtro-fecha">Fecha del pedido</label>
                        <div class="filtro-wrapper" id="wrapper-fecha">
                            <input type="date" id="filtro-fecha" value="<?php echo $filtro_fecha; ?>" onchange="checkInput('fecha'); aplicarFiltros()">
                            <div class="input-icon"><i class="far fa-calendar-alt"></i></div>
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
                            <div class="input-icon"><i class="fas fa-tasks"></i></div>
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
                            <div class="input-icon"><i class="fas fa-filter"></i></div>
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
                                <button onclick="borrarTodo()" style="margin-top:15px; background: var(--primary); color:white; padding:10px 20px; border:none; border-radius:8px; cursor:pointer;">Restablecer Filtros</button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php foreach ($mis_pedidos as $pedido): 
                            $estado = $pedido['estado'] ?? 'Pendiente';
                            $clase_estado = 'estado-default';
                            if(stripos($estado, 'Entregado') !== false) $clase_estado = 'estado-entregado';
                            elseif(stripos($estado, 'Pendiente') !== false) $clase_estado = 'estado-pendiente';
                            elseif(stripos($estado, 'Cancelado') !== false) $clase_estado = 'estado-cancelado';
                            elseif(stripos($estado, 'Proceso') !== false) $clase_estado = 'estado-proceso';
                        ?>
                            <div class="card-pedido">
                                <div class="card-header-pedido">
                                    <span class="pedido-id">PEDIDO #<?php echo str_pad($pedido['id'], 4, '0', STR_PAD_LEFT); ?></span>
                                    <span class="estado-badge <?php echo $clase_estado; ?>"><?php echo htmlspecialchars($estado); ?></span>
                                </div>
                                <div class="card-body-pedido">
                                    <p><i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($pedido['fecha'])); ?></p>
                                    <p class="precio-total"><?php echo number_format($pedido['total'], 2); ?> €</p>
                                </div>
                                <a href="detallesventas.php?id=<?php echo $pedido['id']; ?>" class="btn-ver-detalles">
                                    Ver Detalles <i class="fas fa-arrow-right" style="margin-left:5px; font-size:12px;"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </main>

        <?php 
        if(function_exists('sectionfooter')) {
            sectionfooter(); 
        }
        ?>
    </div>

    <script>
        function checkInput(tipo) {
            let input = document.getElementById('filtro-' + tipo);
            let wrapper = document.getElementById('wrapper-' + tipo);
            if (!input && tipo === 'producto') input = document.getElementById('producto');
            if (input && wrapper) wrapper.classList.toggle('con-valor', input.value.trim() !== "");
        }
        function borrarFiltro(tipo) {
            let input = document.getElementById('filtro-' + tipo);
            if (!input && tipo === 'producto') input = document.getElementById('producto');
            if (input) { input.value = ''; checkInput(tipo); aplicarFiltros(); }
        }
        function aplicarFiltros() {
            const fecha = document.getElementById('filtro-fecha').value;
            const estado = document.getElementById('filtro-estado').value;
            const producto = document.getElementById('producto').value;
            const url = new URL(window.location.href);
            fecha ? url.searchParams.set('fecha', fecha) : url.searchParams.delete('fecha');
            estado ? url.searchParams.set('estado', estado) : url.searchParams.delete('estado');
            producto ? url.searchParams.set('categoria', producto) : url.searchParams.delete('categoria');
            window.location.href = url.toString();
        }
        function borrarTodo() {
            const url = new URL(window.location.href);
            url.search = '';
            url.searchParams.set('estado', 'activos');
            window.location.href = url.toString();
        }
        document.addEventListener("DOMContentLoaded", function () {
            checkInput('fecha'); checkInput('estado'); checkInput('producto');
        });
    </script>
</body>
</html>