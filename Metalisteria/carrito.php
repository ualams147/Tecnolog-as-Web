<?php
// carrito.php

// 1. INCLUIR FUNCIONES Y SESIÓN
include 'CabeceraFooter.php'; 

// 2. CONEXIÓN A BASE DE DATOS
include 'conexion.php'; 

// =======================================================================
// LÓGICA PHP (BACKEND)
// =======================================================================

// --- Obtener ID del usuario ---
$uid = 0;
if (isset($_SESSION['usuario'])) {
    if (is_array($_SESSION['usuario'])) {
        $uid = $_SESSION['usuario']['id'] ?? 0;
    } else {
        $uid = $_SESSION['usuario_id'] ?? 0;
    }
}

// A) ELIMINAR PRODUCTO (Actualizado para borrar por ID de línea de carrito)
// Esto es importante: ahora borramos por la fila del carrito, no por el ID del producto,
// para no borrar dos ventanas iguales con medidas distintas.
if (isset($_GET['remove'])) {
    $id_linea_carrito = $_GET['remove'];
    
    // 1. Borrar de la BD (si está logueado)
    if ($uid > 0) {
        // Al borrar de 'carrito', la tabla 'carrito_personalizados' se borra sola gracias al CASCADE de la BD
        $stmtDel = $conn->prepare("DELETE FROM carrito WHERE id = ? AND cliente_id = ?");
        $stmtDel->execute([$id_linea_carrito, $uid]);
    }
    
    // 2. Borrar de la sesión visual
    if(isset($_SESSION['carrito'][$id_linea_carrito])){
        unset($_SESSION['carrito'][$id_linea_carrito]);
    }
    
    header("Location: carrito.php");
    exit;
}

// B) VACIAR CARRITO
if (isset($_GET['vaciar'])) {
    if ($uid > 0) {
        $stmtVaciar = $conn->prepare("DELETE FROM carrito WHERE cliente_id = ?");
        $stmtVaciar->execute([$uid]);
    }
    $_SESSION['carrito'] = [];
    header("Location: carrito.php");
    exit;
}

// =======================================================================
// 2. SINCRONIZACIÓN Y CARGA DE DATOS
// =======================================================================

// Variable para bloquear el botón de compra si hay cosas pendientes
$bloqueo_compra = false; 

if ($uid > 0) {
    // CONSULTA AVANZADA: 
    // Unimos la tabla 'carrito' con 'productos' Y TAMBIÉN con 'carrito_personalizados' (LEFT JOIN)
    // para traer las medidas y el estado si existen.
    $sql = "SELECT c.id as carrito_id, c.producto_id, c.cantidad, c.es_personalizado,
                   p.nombre, p.precio as precio_base, p.imagen_url, p.referencia, p.color as color_base, p.medidas as medidas_base,
                   cp.medidas as medidas_pers, cp.color as color_pers, cp.detalles, cp.estado as estado_pers, cp.precio_final
            FROM carrito c 
            JOIN productos p ON c.producto_id = p.id 
            LEFT JOIN carrito_personalizados cp ON c.id = cp.carrito_id
            WHERE c.cliente_id = :uid";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute([':uid' => $uid]);
    $items_bd = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Reiniciamos la sesión para asegurar que coincide con la BD
    $_SESSION['carrito'] = []; 
    
    foreach ($items_bd as $item) {
        // --- LÓGICA DE ESTADO Y PRECIO ---
        $precio_real = $item['precio_base'];
        $estado = 'normal'; // estados: normal, pendiente, aprobado, rechazado

        // Si el producto está marcado como personalizado en la BD:
        if ($item['es_personalizado'] == 1) {
            $estado = $item['estado_pers']; // viene de la tabla carrito_personalizados
            
            if ($estado == 'pendiente') {
                $precio_real = 0; // Ponemos 0 para que no sume al total
                $bloqueo_compra = true; // BLOQUEAMOS la compra porque falta precio
            } elseif ($estado == 'aprobado' && $item['precio_final'] !== null) {
                $precio_real = $item['precio_final']; // Usamos el precio que puso el admin
            } elseif ($estado == 'rechazado') {
                 $bloqueo_compra = true; // Tampoco dejamos comprar cosas rechazadas
            }
        }

        // Guardamos en sesión usando el ID DEL CARRITO como clave (importante para diferenciar productos iguales)
        $_SESSION['carrito'][$item['carrito_id']] = [
            'id_linea' => $item['carrito_id'], 
            'producto_id' => $item['producto_id'],
            'nombre' => $item['nombre'],
            'precio' => $precio_real,
            'imagen' => $item['imagen_url'], 
            'referencia' => $item['referencia'],
            'cantidad' => $item['cantidad'],
            
            // Datos extra
            'es_personalizado' => $item['es_personalizado'],
            'estado_pers' => $estado,
            // Si es personalizado, usamos los datos de la tabla 'cp', si no, los de 'p'
            'color' => ($item['es_personalizado'] == 1) ? $item['color_pers'] : $item['color_base'],
            'medidas' => ($item['es_personalizado'] == 1) ? $item['medidas_pers'] : $item['medidas_base'],
            'detalles' => $item['detalles'] ?? ''
        ];
    }
}

// Inicializar sesión vacía si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// CALCULAR TOTALES (Sumar solo lo que tenga precio real)
$total_carrito = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total_carrito += $item['precio'] * $item['cantidad'];
}
?>

<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['idioma']) ? $_SESSION['idioma'] : 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['carrito_titulo_pag']; ?></title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/carrito.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .btn-vaciar-custom {
            text-decoration: none; padding: 15px; border: 1px solid #dc3545; background: transparent;
            color: #dc3545; font-weight: 600; border-radius: 12px; transition: all 0.3s ease;
            display: inline-flex; align-items: center; justify-content: center; cursor: pointer;
        }
        .btn-vaciar-custom:hover { background-color: #dc3545; color: #ffffff; box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3); }

        /* Estilos para etiquetas de estado */
        .badge-pers { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-left: 5px; }
        .badge-custom { background-color: #eeca00; color: #fff; }
        .badge-pending { background-color: #ff9800; color: #fff; } /* Naranja */
        .badge-approved { background-color: #4caf50; color: #fff; } /* Verde */
        .badge-rejected { background-color: #f44336; color: #fff; } /* Rojo */
        
        .alert-bloqueo {
            background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba;
            padding: 15px; border-radius: 8px; margin-top: 15px; text-align: center;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="visitante-carrito">
        
        <?php sectionheader(4); ?>

        <main class="carrito-section">
            <h1 class="carrito-title"><?php echo $lang['carrito_h1']; ?></h1>

            <div class="carrito-container">
                
                <?php if (empty($_SESSION['carrito'])): ?>
                    <div style="text-align: center; padding: 60px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#ccc" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                        </svg>
                        <p style="color: #666; margin-top: 20px; font-size: 18px;"><?php echo $lang['carrito_vacio_msg']; ?></p>
                        <a href="productos.php" class="btn-comprar" style="display: inline-block; margin-top: 20px; text-decoration: none;"><?php echo $lang['carrito_ver_productos']; ?></a>
                    </div>

                <?php else: ?>
                    <div class="carrito-items">
                        
                        <?php foreach ($_SESSION['carrito'] as $key => $item): ?>
                            <?php 
                                $ruta_img = str_replace('../', '', $item['imagen']);
                                if(empty($ruta_img)) $ruta_img = "imagenes/producto-sin-imagen.png";
                                
                                // Variables de control
                                $es_pers = ($item['es_personalizado'] == 1);
                                $estado = $item['estado_pers'] ?? 'normal';
                            ?>
                            
                            <article class="item-card" style="<?php echo ($estado == 'rechazado') ? 'opacity: 0.7; border: 1px solid #ffcccc;' : ''; ?>">
                                <img src="<?php echo htmlspecialchars($ruta_img); ?>" 
                                     alt="<?php echo htmlspecialchars($item['nombre']); ?>" 
                                     class="item-image-placeholder" 
                                     style="object-fit: contain; height: 200px; padding: 10px;"
                                     onerror="this.src='imagenes/producto-sin-imagen.png'">
                                
                                <div class="item-details">
                                    <div class="item-info">
                                        <p class="item-label">
                                            <?php echo $lang['carrito_producto_lbl']; ?>
                                            <?php if($es_pers): ?>
                                                <span class="badge-pers badge-custom"><?php echo $lang['carrito_etiqueta_personalizado']; ?></span>
                                            <?php endif; ?>
                                        </p>
                                        
                                        <p class="item-value" style="font-weight:600;">
                                            <?php echo htmlspecialchars($item['nombre']); ?>
                                        </p>
                                        
                                        <?php if($es_pers && $estado == 'pendiente'): ?>
                                            <div style="margin-top:5px;">
                                                <span class="badge-pers badge-pending"><i class="fas fa-clock"></i> <?php echo $lang['carrito_pendiente_revision']; ?></span>
                                            </div>
                                        <?php elseif($es_pers && $estado == 'rechazado'): ?>
                                            <div style="margin-top:5px;">
                                                <span class="badge-pers badge-rejected"><i class="fas fa-times-circle"></i> <?php echo $lang['carrito_estado_rechazado']; ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <p class="item-label" style="font-size: 0.85em; margin-top:10px;"><?php echo $lang['carrito_detalles_lbl']; ?></p>
                                        <p class="item-value" style="font-size: 0.85em; color: #555;">
                                            <?php if($es_pers): ?>
                                                <strong><?php echo $lang['carrito_medidas']; ?>:</strong> <?php echo htmlspecialchars($item['medidas']); ?> <br>
                                                <strong>Color:</strong> <?php echo htmlspecialchars($item['color']); ?>
                                                <?php if(!empty($item['detalles'])): ?>
                                                    <br><em>"<?php echo htmlspecialchars($item['detalles']); ?>"</em>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php echo htmlspecialchars($item['color'] ?? 'N/A'); ?> - <?php echo htmlspecialchars($item['medidas'] ?? 'N/A'); ?>
                                            <?php endif; ?>
                                        </p>

                                        <p class="item-label"><?php echo $lang['carrito_precio_lbl']; ?></p>
                                        <p class="item-value">
                                            <?php 
                                            // Si está pendiente, no mostramos 0.00€, mostramos texto
                                            if ($es_pers && $estado == 'pendiente') {
                                                echo '<span style="color:#e65100; font-style:italic;">' . $lang['carrito_precio_pendiente'] . '</span>';
                                            } else {
                                                echo number_format($item['precio'], 2) . '€';
                                            }
                                            ?>
                                        </p>
                                    </div>

                                    <div class="item-actions">
                                        
                                        <?php if(!$es_pers || $estado == 'aprobado'): ?>
                                            <div class="qty-selector">
                                                <button type="button" class="qty-btn" onclick="actualizarCantidad(<?php echo $key; ?>, 'restar')">-</button>
                                                <span class="qty-text" id="cantidad-<?php echo $key; ?>"><?php echo $item['cantidad']; ?></span>
                                                <button type="button" class="qty-btn" onclick="actualizarCantidad(<?php echo $key; ?>, 'sumar')">+</button>
                                            </div>
                                        <?php else: ?>
                                            <div style="flex-grow:1;"></div>
                                        <?php endif; ?>

                                        <a href="javascript:void(0);" onclick="confirmarBorrado(<?php echo $key; ?>)" class="btn-eliminar" title="<?php echo $lang['carrito_eliminar_title']; ?>" style="text-decoration: none; display: flex; align-items: center; margin-left: auto;">
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
                        <h2 class="total-text"><?php echo $lang['carrito_total']; ?> <span class="total-amount" id="precio-total-carrito"><?php echo number_format($total_carrito, 2); ?>€</span></h2>
                        
                        <?php if($bloqueo_compra): ?>
                            <div class="alert-bloqueo">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo $lang['carrito_msg_bloqueo']; ?>
                            </div>
                        <?php endif; ?>

                        <div style="display:flex; gap:20px; justify-content:center; flex-wrap:wrap; margin-top:20px;">
                            
                            <a href="javascript:void(0);" onclick="confirmarVaciar()" class="btn-vaciar-custom">
                                <?php echo $lang['carrito_btn_vaciar']; ?>
                            </a>

                            <?php if ($uid > 0): ?>
                                <?php if (!$bloqueo_compra): ?>
                                    <a href="datosenvio.php" class="btn-comprar" style="text-align:center; text-decoration:none; display:block;">
                                        <?php echo $lang['carrito_btn_tramitar']; ?>
                                    </a>
                                <?php else: ?>
                                    <button class="btn-comprar" style="background:#ccc; cursor:not-allowed;" disabled>
                                        <?php echo $lang['carrito_btn_tramitar']; ?>
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="iniciarsesion.php?origen=compra" class="btn-comprar" style="text-align:center; text-decoration:none; display:block;">
                                    <?php echo $lang['carrito_btn_comprar']; ?>
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
    // JS básico para actualizar cantidad y borrar
    function actualizarCantidad(idLinea, accion) {
        // Asegúrate de que 'apicarrito.php' sepa manejar el ID de la línea del carrito
        fetch('apicarrito.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                accion: 'actualizar',
                id: idLinea, 
                modo: accion
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                const spanCantidad = document.getElementById('cantidad-' + idLinea);
                if (spanCantidad) spanCantidad.innerText = data.nuevaCantidad;
                
                const spanTotal = document.getElementById('precio-total-carrito');
                if (spanTotal) spanTotal.innerText = parseFloat(data.nuevoTotal).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '€';
            } else {
                if(data.reload) location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function confirmarBorrado(id) {
        Swal.fire({
            title: '<?php echo $lang['swal_borrar_titulo']; ?>',
            text: "<?php echo $lang['swal_borrar_texto']; ?>",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#293661',
            confirmButtonText: '<?php echo $lang['swal_borrar_si']; ?>',
            cancelButtonText: '<?php echo $lang['swal_cancelar']; ?>'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "carrito.php?remove=" + id;
            }
        })
    }

    function confirmarVaciar() {
        Swal.fire({
            title: '<?php echo $lang['swal_vaciar_titulo']; ?>',
            text: "<?php echo $lang['swal_vaciar_texto']; ?>",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#293661',
            confirmButtonText: '<?php echo $lang['swal_vaciar_si']; ?>',
            cancelButtonText: '<?php echo $lang['swal_cancelar']; ?>'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "carrito.php?vaciar=true";
            }
        })
    }
    </script>
</body>
</html>