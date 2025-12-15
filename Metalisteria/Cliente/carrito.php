<?php
// 1. INCLUIR FUNCIONES (Inicia sesión automáticamente)
// ESTO DEBE IR EN LA LÍNEA 1
include '../CabeceraFooter.php'; 

// 2. CONEXIÓN A BD
include '../conexion.php'; 

// =======================================================================
// LÓGICA PHP (TU CÓDIGO ORIGINAL)
// =======================================================================

// A) ELIMINAR PRODUCTO INDIVIDUAL
if (isset($_GET['remove'])) {
    $id_borrar = $_GET['remove'];
    
    // 1. Borrar de la BD (si es cliente)
    if (isset($_SESSION['usuario']['id'])) { // Ojo: verifica si guardas el ID como ['usuario']['id'] o ['usuario_id']
        // Ajustamos para asegurar compatibilidad con tu login habitual
        $uid = $_SESSION['usuario']['id'] ?? $_SESSION['usuario_id'] ?? 0;
        
        if ($uid > 0) {
            $stmtDel = $conn->prepare("DELETE FROM carrito WHERE cliente_id = ? AND producto_id = ?");
            $stmtDel->execute([$uid, $id_borrar]);
        }
    }
    
    // 2. Borrar de la sesión
    unset($_SESSION['carrito'][$id_borrar]);
    
    header("Location: carrito.php");
    exit;
}

// B) VACIAR CARRITO COMPLETO
if (isset($_GET['vaciar'])) {
    // 1. Vaciar BD (si es cliente)
    if (isset($_SESSION['usuario']['id']) || isset($_SESSION['usuario_id'])) {
        $uid = $_SESSION['usuario']['id'] ?? $_SESSION['usuario_id'];
        $stmtVaciar = $conn->prepare("DELETE FROM carrito WHERE cliente_id = ?");
        $stmtVaciar->execute([$uid]);
    }

    // 2. Vaciar sesión
    $_SESSION['carrito'] = [];

    header("Location: carrito.php");
    exit;
}

// =======================================================================
// 2. SINCRONIZACIÓN (Cargar datos reales al entrar)
// =======================================================================

// Comprobamos si hay usuario logueado (ajusta la clave según tu login: 'usuario' array o 'usuario_id')
$usuario_logueado = isset($_SESSION['usuario']);
$uid = 0;

if ($usuario_logueado) {
    // Si tu login guarda $_SESSION['usuario'] como array:
    $uid = $_SESSION['usuario']['id'] ?? $_SESSION['usuario_id'] ?? 0;

    if ($uid > 0) {
        // Traemos todo de la BD
        $sql = "SELECT c.producto_id, c.cantidad, p.nombre, p.precio, p.imagen, p.referencia 
                FROM carrito c 
                JOIN productos p ON c.producto_id = p.id 
                WHERE c.cliente_id = :uid";
                
        $stmt = $conn->prepare($sql);
        $stmt->execute([':uid' => $uid]);
        $items_bd = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Reconstruimos la sesión para que coincida con la BD
        $_SESSION['carrito'] = []; 
        foreach ($items_bd as $item) {
            $_SESSION['carrito'][$item['producto_id']] = [
                'id' => $item['producto_id'],
                'nombre' => $item['nombre'],
                'precio' => $item['precio'],
                // Asegúrate que en la BD se llame 'imagen' o 'imagen_url' y úsalo aquí
                'imagen' => $item['imagen'] ?? $item['imagen_url'] ?? 'sin_imagen.jpg', 
                'referencia' => $item['referencia'],
                'cantidad' => $item['cantidad']
            ];
        }
    }
}

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// CALCULAR TOTALES
$total_carrito = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total_carrito += $item['precio'] * $item['cantidad'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Compra - Metalistería Fulsan</title>
    <link rel="icon" type="image/png" href="../imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/carrito.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="../js/auth.js"></script>
</head>
<body>
    <div class="visitante-carrito">
        
        <?php sectionheader(4); ?>

        <main class="carrito-section">
            <h1 class="carrito-title">Mi Compra</h1>

            <div class="carrito-container">
                
                <?php if (empty($_SESSION['carrito'])): ?>
                    <div style="text-align: center; padding: 60px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#ccc" viewBox="0 0 16 16">
                          <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                        </svg>
                        <p style="color: #666; margin-top: 20px; font-size: 18px;">Tu carrito está vacío.</p>
                        <a href="productos.php" class="btn-comprar" style="display: inline-block; margin-top: 20px; text-decoration: none;">Ver Productos</a>
                    </div>

                <?php else: ?>
                    <div class="carrito-items">
                        
                        <?php foreach ($_SESSION['carrito'] as $id => $item): ?>
                            <article class="item-card">
                                <img src="../<?php echo htmlspecialchars($item['imagen']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['nombre']); ?>" 
                                     class="item-image-placeholder" 
                                     style="object-fit: contain; height: 200px; padding: 10px;"
                                     onerror="this.src='https://via.placeholder.com/200?text=Sin+Imagen'">
                                
                                <div class="item-details">
                                    <div class="item-info">
                                        <p class="item-label">Producto:</p>
                                        <p class="item-value"><?php echo htmlspecialchars($item['nombre']); ?></p>
                                        
                                        <p class="item-label" style="font-size: 0.85em; margin-top:5px;">Detalles:</p>
                                        <p class="item-value" style="font-size: 0.85em; color: #555;">
                                            <?php echo htmlspecialchars($item['color']); ?> - <?php echo htmlspecialchars($item['medidas']); ?>
                                        </p>

                                        <p class="item-label">Precio:</p>
                                        <p class="item-value"><?php echo number_format($item['precio'], 2); ?>€</p>
                                    </div>

                                    <div class="item-actions">
                                        
                                        <div class="qty-selector">
                                            <button type="button" class="qty-btn" onclick="actualizarCantidad(<?php echo $id; ?>, 'restar')">-</button>
                                            
                                            <span class="qty-text" id="cantidad-<?php echo $id; ?>">
                                                <?php echo $item['cantidad']; ?>
                                            </span>
                                            
                                            <button type="button" class="qty-btn" onclick="actualizarCantidad(<?php echo $id; ?>, 'sumar')">+</button>
                                        </div>

                                        <a href="javascript:void(0);" onclick="confirmarBorrado(<?php echo $id; ?>)" class="btn-eliminar" title="Eliminar producto" style="text-decoration: none; display: flex; align-items: center;">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                                <path d="M17 6h5v2h-2v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V8H2V6h5V3a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v3zm1 2H6v12h12V8zm-9 3h2v6H9v-6zm4 0h2v6h-2v-6zM9 4v2h6V4H9z"/>
                                            </svg>
                                        </a>

                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>

                    </div>

                    <div class="carrito-summary">
                        <h2 class="total-text">Total: <span class="total-amount" id="precio-total-carrito"><?php echo number_format($total_carrito, 2); ?>€</span></h2>
                        
                        <div style="display:flex; gap:20px; justify-content:center; flex-wrap:wrap;">
                            
                            <a href="javascript:void(0);" onclick="confirmarVaciar()" class="btn-eliminar" style="text-decoration:none; padding: 15px; border:1px solid #dc3545; background:transparent; color:#dc3545; font-weight:600; border-radius: 12px;">
                                Vaciar Carrito
                            </a>

                            <?php if (isset($_SESSION['usuario_id'])): ?>
                                <a href="datosEnvio.php" class="btn-comprar" style="text-align:center; text-decoration:none; display:block;">
                                    Tramitar Pedido
                                </a>
                            <?php else: ?>
                                <a href="eresCliente.php" class="btn-comprar" style="text-align:center; text-decoration:none; display:block;">
                                    Comprar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </main>

        <?php sectionfooter(); ?>
    </div>

    <script>
    
    // 1. FUNCIÓN PARA ACTUALIZAR CANTIDAD (AJAX)
    function actualizarCantidad(idProducto, accion) {
        // Llamada al archivo PHP en segundo plano
        fetch('ApiCarrito.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                accion: 'actualizar',
                id: idProducto,
                modo: accion
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                // A. Actualizar número individual
                const spanCantidad = document.getElementById('cantidad-' + idProducto);
                if (spanCantidad) spanCantidad.innerText = data.nuevaCantidad;

                // B. Actualizar precio total
                const spanTotal = document.getElementById('precio-total-carrito');
                if (spanTotal) spanTotal.innerText = data.nuevoTotal + '€';

                // C. Actualizar badge del menú
                const menuBadge = document.getElementById('badge-menu');
                if (menuBadge) {
                    menuBadge.innerText = data.totalItems;
                    // Si es 0, ocultarlo, si no, mostrarlo
                    menuBadge.style.display = (data.totalItems > 0) ? 'inline-block' : 'none';
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // 2. FUNCIÓN PARA ELIMINAR UN PRODUCTO (SWEETALERT)
    function confirmarBorrado(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Vas a quitar este producto de tu compra.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#293661',
            confirmButtonText: 'Sí, quitar',
            cancelButtonText: 'Cancelar',
            background: '#f4f4f4',
            color: '#333'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "carrito.php?remove=" + id;
            }
        })
    }

    // 3. FUNCIÓN PARA VACIAR TODO (SWEETALERT)
    function confirmarVaciar() {
        Swal.fire({
            title: '¿Vaciar carrito?',
            text: "Se eliminarán todos los productos.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#293661',
            confirmButtonText: 'Sí, vaciar todo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "carrito.php?vaciar=true";
            }
        })
    }
    </script>

</body>
</html>