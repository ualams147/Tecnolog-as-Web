<?php
include 'conexion.php';

// 1. VERIFICAR ID DE VENTA
if (!isset($_GET['id'])) {
    header("Location: listadoventasadmin.php");
    exit;
}

$id_venta = $_GET['id'];


// --- LÓGICA PHP PARA CAMBIAR EL ESTADO (TOGGLE) ---
if (isset($_POST['nuevo_estado'])) {
    $nuevo_estado = $_POST['nuevo_estado']; // Recibimos 'Entregado' o 'Pendiente'
    
    // Validación básica
    if ($nuevo_estado == 'Entregado' || $nuevo_estado == 'Pendiente') {
        $sql_update = "UPDATE ventas SET estado = ? WHERE id = ?";
        $stmt_upd = $conn->prepare($sql_update);
        if ($stmt_upd->execute([$nuevo_estado, $id_venta])) {
            // Recargamos la página para ver el cambio
            header("Location: detallesventas.php?id=$id_venta&updated=1");
            exit;
        }
    }
}

// 2. OBTENER DATOS DE LA VENTA
$sql_venta = "SELECT v.*, c.id as id_cliente, c.nombre as nombre_cli, c.apellidos as apellidos_cli, c.email 
              FROM ventas v 
              JOIN clientes c ON v.id_cliente = c.id 
              WHERE v.id = ?";
$stmt = $conn->prepare($sql_venta);
$stmt->execute([$id_venta]);
$venta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venta) {
    echo "Venta no encontrada.";
    exit;
}

// 3. DETERMINAR EL ESTADO DEL BOTÓN (Lógica Visual)
// Si está 'Entregado', el botón debe servir para volver a 'Pendiente'
// Si está 'Pendiente', el botón debe servir para pasar a 'Entregado'
$estado_actual = $venta['estado'];
$es_entregado = (strtolower($estado_actual) === 'entregado');

if ($es_entregado) {
    $texto_boton = "Marcar Pendiente";
    $estado_objetivo = "Pendiente";
    $icono_js = "warning"; // Icono de advertencia para volver atrás
    $texto_confirmacion_js = "¿Volver a poner el pedido como Pendiente?";
} else {
    $texto_boton = "Marcar Entregado";
    $estado_objetivo = "Entregado";
    $icono_js = "question"; // Icono de pregunta normal
    $texto_confirmacion_js = "¿Marcar el pedido como Entregado?";
}

// 4. OBTENER PRODUCTOS
$sql_detalles = "SELECT d.*, p.nombre as nombre_prod 
                 FROM detalle_ventas d 
                 JOIN productos p ON d.id_producto = p.id 
                 WHERE d.id_venta = ?";
$stmt_det = $conn->prepare($sql_detalles);
$stmt_det->execute([$id_venta]);
$productos_venta = $stmt_det->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles Venta #<?php echo $venta['id']; ?> - Metalisteria Fulsan</title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/administrador.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .contenedor-pie-factura {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #eee;
        }

        /* BOTÓN AZUL - Mismo estilo siempre, solo cambia el texto */
        .boton-azul-fulsan {
            background: #293661 !important;
            border: 2px solid rgba(41, 54, 97, 0.6) !important;
            width: 298px;
            height: 72px;
            border-radius: 20px; 
            box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease;
            outline: none;
        }

        .boton-azul-fulsan:hover {
            transform: translateY(-5px);
            background: #1f2849 !important; /* Un poco más oscuro al hover */
        }

        .boton-azul-fulsan span {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            font-size: 22px;
            color: white;
        }

        .etiqueta-estado-actual {
            margin-top: 10px;
            text-align: center;
            font-size: 14px;
            color: #555;
            font-weight: 600;
        }

        @media (max-width: 992px) {
            .contenedor-pie-factura {
                flex-direction: column-reverse;
                align-items: center;
                gap: 30px;
            }
            .boton-azul-fulsan { width: 100%; max-width: 298px; }
            .total-row { align-items: center !important; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="DetallesVentas">
        <header class="cabecera">
            <div class="container">
                <div class="logo-main">
                    <a href="indexadmin.php" class="logo-main">
                        <img src="imagenes/logo.png" alt="Logo Metalful">
                        <div class="logo-text"><span> Metalisteria</span><strong>Fulsan</strong></div>
                    </a>
                </div>
                <nav class="nav-bar">
                    <a href="listadoventasadmin.php" style="font-weight:bold; border-bottom: 2px solid currentColor;">Ventas</a>
                    <a href="listadoproductosadmin.php">Productos</a>
                    <a href="listadoclientesadmin.php">Clientes</a>
                </nav>
                <div class="log-out"><a href="index.php">Cerrar Sesión</a></div>
            </div>
        </header>

        <div class="titulo-section">
            <div class="degradado"></div>
            <div class="recuadro-fondo"></div> 
            
            <a href="listadoventasadmin.php" class="flecha-circular" style="display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18L9 12L15 6" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>

            <h1 class="titulo-principal">Venta #<?php echo str_pad($venta['id'], 4, '0', STR_PAD_LEFT); ?></h1>
        </div>

        <div class="container main-container">
            <div class="details-card">
                <div class="client-row">
                    <span class="label-text">Cliente:</span>
                    <input type="text" class="input-display" readonly value="<?php echo $venta['nombre_cli'] . ' ' . $venta['apellidos_cli']; ?>">
                </div>

                <div class="products-section">
                    <span class="label-text">Productos del pedido:</span>
                    <div class="products-list-box">
                        <?php foreach($productos_venta as $item): ?>
                            <div class="product-item-card">
                                <div class="product-info-line">
                                    <span class="product-info-label">Nombre Producto:</span>
                                    <span class="product-info-value"><?php echo $item['nombre_prod']; ?></span>
                                </div>
                                <div class="product-info-line">
                                    <span class="product-info-label">Unidades:</span>
                                    <span class="product-info-value"><?php echo $item['cantidad']; ?></span>
                                </div>
                                <div class="product-info-line">
                                    <span class="product-info-label">Subtotal (IVA Inc.):</span>
                                    <span class="product-info-value" style="font-weight:700;"><?php echo number_format($item['subtotal'], 2); ?> €</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="contenedor-pie-factura">
                    <div class="bloque-accion-izquierda">
                        <form id="formCambiarEstado" method="POST">
                            <input type="hidden" name="nuevo_estado" value="<?php echo $estado_objetivo; ?>">
                            
                            <button type="button" class="boton-azul-fulsan" onclick="confirmarCambio()">
                                <span><?php echo $texto_boton; ?></span>
                            </button>
                        </form>
                        
                        <div class="etiqueta-estado-actual">
                            Estado actual: <span style="color: #293661;"><?php echo $venta['estado']; ?></span>
                        </div>
                    </div>

                    <div class="total-row" style="display: flex; flex-direction: column; align-items: flex-end; gap: 5px;">
                        <span class="total-label" style="font-size: 1.5em; font-weight:700; color: #293661;">
                            Importe Total: <?php echo number_format($venta['total'], 2); ?> €
                        </span>
                        <span style="color: #555;">Base Imponible: <?php echo number_format($venta['total'] / 1.21, 2); ?> €</span>
                        <span style="color: #888; font-size: 0.9em;">IVA (21%): <?php echo number_format($venta['total'] - ($venta['total'] / 1.21), 2); ?> €</span>
                    </div>
                </div>
            </div>

            <div class="botones-finales">
                <div class="boton-salir">
                    <a href="javascript:void(0);" onclick="confirmarSalida()">Salir</a>
                </div>
                <div class="boton-usuario">
                    <a href="modificardatoscliente.php?id=<?php echo $venta['id_cliente']; ?>">Ir al usuario</a>
                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-logo-section">
                        <div class="logo-footer"><img src="imagenes/footer.png" alt="Logo Metalful"></div>
                    </div>
                    </div>
            </div>
        </footer>
    </div>

    <script>
        // Función dinámica según el estado
        function confirmarCambio() {
            Swal.fire({
                title: '<?php echo $texto_confirmacion_js; ?>',
                text: "El estado se actualizará en la base de datos.",
                icon: '<?php echo $icono_js; ?>',
                showCancelButton: true,
                confirmButtonColor: '#293661',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formCambiarEstado').submit();
                }
            });
        }

        function confirmarSalida() {
            window.location.href = 'listadoventasadmin.php';
        }

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('updated')) {
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: 'El estado del pedido ha cambiado correctamente.',
                confirmButtonColor: '#293661',
                timer: 2000
            });
        }
    </script>
</body>
</html>