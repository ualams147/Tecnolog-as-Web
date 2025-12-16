<?php
// pedidosactivos.php

// --- CONFIGURACIÓN Y CONEXIÓN ---
require_once 'conexion.php';      
require_once 'CabeceraFooter.php'; // Aquí se carga $lang

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

// Filtro Estado (Mantenemos lógica interna en español para BD)
if ($filtro_estado === 'activos') {
    $where .= " AND v.estado NOT IN ('Entregado', 'Cancelado')";
} elseif ($filtro_estado === 'historial') {
    $where .= " AND v.estado IN ('Entregado', 'Cancelado')";
}

// --- FILTRO CATEGORÍA ---
if (!empty($filtro_categoria)) {
    if ($filtro_categoria == 'otros') {
        $where .= " AND EXISTS (
            SELECT 1 FROM detalle_ventas dv 
            JOIN productos p ON dv.id_producto = p.id 
            JOIN categorias cat ON p.id_categoria = cat.id 
            WHERE dv.id_venta = v.id 
            AND cat.nombre NOT LIKE '%Ventana%' 
            AND cat.nombre NOT LIKE '%Puerta%' 
            AND cat.nombre NOT LIKE '%Barandilla%'
        )";
    } else {
        $where .= " AND EXISTS (
            SELECT 1 FROM detalle_ventas dv 
            JOIN productos p ON dv.id_producto = p.id 
            JOIN categorias cat ON p.id_categoria = cat.id 
            WHERE dv.id_venta = v.id AND cat.nombre LIKE :cat_nombre
        )";
        $params[':cat_nombre'] = '%' . $filtro_categoria . '%';
    }
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
<html lang="<?php echo isset($_SESSION['idioma']) ? $_SESSION['idioma'] : 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['pedidos_titulo']; ?> - Metalistería Fulsan</title>
    
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="css/pedidosActivos.css">
    
</head>

<body>

    <div class="visitante-producto-detalle">
        
        <?php 
        if(function_exists('sectionheader')) {
            sectionheader(1); 
        } 
        ?>

        <section class="product-hero">
            <div class="container hero-content">
                <a href="perfil.php" class="btn-back">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </a>
                
                <h1 class="product-title-main"><?php echo $lang['pedidos_titulo']; ?></h1>
            </div>
        </section>

        <main class="product-main container">
            <div class="orders-container-card">
                
                <div class="filtros-container">
                    
                    <div class="filtro-item">
                        <label for="filtro-fecha"><?php echo $lang['pedidos_filtro_fecha']; ?></label>
                        <div class="filtro-wrapper" id="wrapper-fecha">
                            <input type="date" id="filtro-fecha" value="<?php echo $filtro_fecha; ?>" onchange="checkInput('fecha'); aplicarFiltros()">
                            <button type="button" class="btn-borrar" onclick="borrarFiltro('fecha')">×</button>
                            <div class="input-icon"><i class="far fa-calendar-alt"></i></div>
                        </div>
                    </div>

                    <div class="filtro-item">
                        <label for="filtro-estado"><?php echo $lang['pedidos_filtro_estado']; ?></label>
                        <div class="filtro-wrapper" id="wrapper-estado">
                            <select id="filtro-estado" onchange="checkInput('estado'); aplicarFiltros()">
                                <option value="activos" <?php echo $filtro_estado == 'activos' ? 'selected' : ''; ?>><?php echo $lang['pedidos_opcion_activos']; ?></option>
                                <option value="historial" <?php echo $filtro_estado == 'historial' ? 'selected' : ''; ?>><?php echo $lang['pedidos_opcion_historial']; ?></option>
                                <option value="todos" <?php echo ($filtro_estado == 'todos') ? 'selected' : ''; ?>><?php echo $lang['pedidos_opcion_todos']; ?></option>
                            </select>
                            
                            <button type="button" class="btn-borrar" onclick="borrarFiltro('estado')">×</button>
                            <div class="input-icon"><i class="fas fa-chevron-down"></i></div>
                        </div>
                    </div>

                    <div class="filtro-item">
                        <label for="producto"><?php echo $lang['pedidos_filtro_cat']; ?></label>
                        <div class="filtro-wrapper" id="wrapper-producto">
                            <select id="producto" onchange="checkInput('producto'); aplicarFiltros()"> 
                                <option value="" selected><?php echo $lang['pedidos_cat_todas']; ?></option>
                                <option value="ventanas" <?php echo $filtro_categoria == 'ventanas' ? 'selected' : ''; ?>><?php echo $lang['pedidos_cat_ventanas']; ?></option>
                                <option value="puertas" <?php echo $filtro_categoria == 'puertas' ? 'selected' : ''; ?>><?php echo $lang['pedidos_cat_puertas']; ?></option>
                                <option value="barandillas" <?php echo $filtro_categoria == 'barandillas' ? 'selected' : ''; ?>><?php echo $lang['pedidos_cat_barandillas']; ?></option>
                                <option value="otros" <?php echo $filtro_categoria == 'otros' ? 'selected' : ''; ?>><?php echo $lang['pedidos_cat_otros']; ?></option>
                            </select>
                            <button type="button" class="btn-borrar" onclick="borrarFiltro('producto')">×</button>
                            <div class="input-icon"><i class="fas fa-chevron-down"></i></div>
                        </div>
                    </div>
                </div>

                <div class="grid-pedidos">
                    <?php if ($total_registros == 0): ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <h3><?php echo $lang['pedidos_vacio_titulo']; ?></h3>
                            <p><?php echo $lang['pedidos_vacio_texto']; ?></p>
                            <?php if(!empty($filtro_fecha) || $filtro_estado != 'activos' || !empty($filtro_categoria)): ?>
                                <button onclick="borrarTodo()" style="margin-top:15px; background: #293661; color:white; padding:10px 20px; border:none; border-radius:8px; cursor:pointer;">
                                    <?php echo $lang['pedidos_btn_ver_todos']; ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php foreach ($mis_pedidos as $pedido): 
                            // Lógica de traducción de Estados
                            $estado_db = $pedido['estado'] ?? 'Pendiente';
                            $clase_estado = 'estado-default';
                            $texto_estado = $estado_db; // Fallback al texto original

                            // Asignación de clase y texto traducido según coincidencia
                            if(stripos($estado_db, 'Entregado') !== false) {
                                $clase_estado = 'estado-entregado';
                                $texto_estado = $lang['estado_entregado'];
                            } elseif(stripos($estado_db, 'Pendiente') !== false) {
                                $clase_estado = 'estado-pendiente';
                                $texto_estado = $lang['estado_pendiente'];
                            } elseif(stripos($estado_db, 'Cancelado') !== false) {
                                $clase_estado = 'estado-cancelado';
                                $texto_estado = $lang['estado_cancelado'];
                            } elseif(stripos($estado_db, 'Proceso') !== false) {
                                $clase_estado = 'estado-proceso';
                                $texto_estado = $lang['estado_proceso'];
                            } elseif(stripos($estado_db, 'Enviado') !== false) {
                                $clase_estado = 'estado-proceso'; // Reutilizamos estilo proceso para enviado
                                $texto_estado = $lang['estado_enviado'];
                            }
                        ?>
                            <div class="card-pedido">
                                <div>
                                    <div class="card-header-pedido">
                                        <span class="pedido-id">#<?php echo str_pad($pedido['id'], 5, '0', STR_PAD_LEFT); ?></span>
                                        <span class="estado-badge <?php echo $clase_estado; ?>"><?php echo htmlspecialchars($texto_estado); ?></span>
                                    </div>
                                    <div class="card-body-pedido">
                                        <p><i class="far fa-calendar-alt"></i> &nbsp; <?php echo date('d/m/Y', strtotime($pedido['fecha'])); ?></p>
                                        <span class="precio-total"><?php echo number_format($pedido['total'], 2); ?> €</span>
                                    </div>
                                </div>
                                <a href="detallesproductos.php?id=<?php echo $pedido['id']; ?>" class="btn-ver-detalles">
                                    <?php echo $lang['pedidos_btn_detalles']; ?>
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
    // El JS se mantiene igual ya que maneja IDs y valores internos (activos, historial, etc)
    // que no necesitan traducción para funcionar, solo visualmente en los options (ya traducidos arriba)
    function checkInput(tipo) {
        let input = document.getElementById('filtro-' + tipo);
        let wrapper = document.getElementById('wrapper-' + tipo);
        
        // Fallback para categoría/producto
        if (!input && tipo === 'producto') input = document.getElementById('producto');
        if (!wrapper && tipo === 'producto') wrapper = document.getElementById('wrapper-producto');

        if (input && wrapper) {
            let val = input.value;
            let mostrarX = val.trim() !== "" && val !== "todos";
            
            if (mostrarX) {
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
            if (tipo === 'estado') {
                input.value = 'todos';
            } else {
                input.value = ''; 
            }
            
            checkInput(tipo); 
            aplicarFiltros(); 
        }
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
        checkInput('fecha'); 
        checkInput('estado'); 
        checkInput('producto'); 
    });
</script>
</body>
</html>