<?php
include 'conexion.php';

// --- 1. LÓGICA DE BORRADO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_eliminar'])) {
    $idBorrar = $_POST['id_eliminar'];
    try {
        // Al borrar la venta, se borran los detalles por el ON DELETE CASCADE
        $stmt = $conn->prepare("DELETE FROM ventas WHERE id = ?");
        $stmt->execute([$idBorrar]);
        header("Location: ListadoVentasAdmin.php");
        exit;
    } catch(PDOException $e) {
        // Error silencioso
    }
}

// --- 2. LÓGICA DE FILTRADO ---
$where = "WHERE 1=1";
$params = [];

// Filtros persistentes
$filtro_fecha = $_GET['fecha'] ?? '';
$filtro_cliente = $_GET['cliente'] ?? '';
$filtro_producto = $_GET['producto'] ?? '';

// A) Filtro Fecha
if (!empty($filtro_fecha)) {
    $where .= " AND DATE(v.fecha) = :fecha";
    $params[':fecha'] = $filtro_fecha;
}

// B) Filtro Cliente
if (!empty($filtro_cliente)) {
    $where .= " AND (c.nombre LIKE :cliente OR c.apellidos LIKE :cliente)";
    $params[':cliente'] = "%" . $filtro_cliente . "%";
}

// C) Filtro Producto (Opcional - Filtra ventas que contengan productos de esa categoría)
if (!empty($filtro_producto)) {
    $where .= " AND EXISTS (
        SELECT 1 FROM detalle_ventas dv 
        JOIN productos p ON dv.id_producto = p.id 
        JOIN categorias cat ON p.id_categoria = cat.id 
        WHERE dv.id_venta = v.id AND cat.nombre = :cat_nombre
    )";
    $params[':cat_nombre'] = $filtro_producto;
}

// --- 3. CONSULTA PRINCIPAL ---
$sql = "SELECT v.*, c.nombre as nombre_cli, c.apellidos as apellidos_cli 
        FROM ventas v
        JOIN clientes c ON v.id_cliente = c.id
        $where
        ORDER BY v.fecha DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_registros = count($ventas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listados Ventas Admin - Metalful</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stylesListadoVentasAdmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Estilos auxiliares para JS y estado vacío */
        .item-venta { display: none; } /* Oculto para paginación */
        
        .empty-state-ventas {
            text-align: center; padding: 50px; display: flex; flex-direction: column; align-items: center;
            background: white; border-radius: 15px; border: 2px dashed #e0e0e0; width: 100%;
        }
        .empty-state-ventas i { font-size: 60px; color: #ccc; margin-bottom: 20px; }
        .empty-state-ventas h3 { color: #293661; margin-bottom: 10px; }
        .empty-state-ventas p { color: #666; }

        /* Estilo Botón Ver Más */
        .contenedor-ver-mas { display: flex; justify-content: center; margin-top: 30px; width: 100%; }
        .btn-ver-mas {
            background-color: white; color: #293661; border: 2px solid #293661;
            padding: 10px 40px; border-radius: 25px; font-family: 'Poppins'; font-weight: 600;
            cursor: pointer; transition: 0.3s;
        }
        .btn-ver-mas:hover { background-color: #293661; color: white; }
        
        /* Ocultar botón borrar filtro por defecto */
        .btn-borrar { display: none; }
        .filtro-wrapper input:not(:placeholder-shown) ~ .btn-borrar,
        .filtro-wrapper select:not([value=""]) ~ .btn-borrar { display: block; }
    </style>
</head>
<body>
    <div class="ListadoVentasAdmin">
        <!-- Cabecera -->
        <header class="cabecera">
            <div class="container">
                <div class="logo-main">
                    <a href="indexAdmin.php" class="logo-main">
                        <img src="../imagenes/logo.png" alt="Logo Metalful">
                        <div class="logo-text">
                            <span> Metalisteria</span>
                            <strong>Fulsan</strong>
                        </div>
                    </a>
                </div>
                
                <nav class="nav-bar">
                    <a href="ListadoVentasAdmin.php" style="font-weight:bold; border-bottom: 2px solid currentColor;">Ventas</a> 
                    <a href="ListadoProductosAdmin.php">Productos</a>
                    <a href="ListadoClientesAdmin.php">Clientes</a>
                </nav>

                <div class="log-out">
                    <a href="../Visitante/index.php">Cerrar Sesión</a>
                </div>

            </div>
        </header>

        
        <!-- Título con filtros dentro -->
        <div class="titulo-section">
            <div class="degradado"></div>
            <div class="recuadro-fondo"></div>
            <h1 class="titulo-principal">Listado Ventas</h1>

            <!-- Filtros -->
            <div class="filtros-en-titulo">
                <!-- Filtro Fecha -->
                <div class="filtro-item">
                    <label for="fecha">
                        <span class="icon"><i class="far fa-calendar-alt" style="font-size:20px;"></i></span> Fecha
                    </label>
                    <div class="filtro-wrapper">
                        <input type="date" id="filtro-fecha" value="<?php echo $filtro_fecha; ?>" onchange="aplicarFiltros()">
                        <button type="button" class="btn-borrar" onclick="borrarFiltro('fecha')">&times;</button>
                    </div>
                </div>

                <!-- Filtro Cliente -->
                <div class="filtro-item">
                    <label for="cliente">
                        <span class="icon"><i class="far fa-user" style="font-size:20px;"></i></span> Cliente
                    </label>
                    <div class="filtro-wrapper">
                        <input type="text" id="filtro-cliente" placeholder="Buscar..." value="<?php echo htmlspecialchars($filtro_cliente); ?>" onkeypress="checkEnter(event)">
                        <button type="button" class="btn-borrar" onclick="borrarFiltro('cliente')">&times;</button>
                    </div>
                </div>

                <!-- Filtro Producto (Categoría) -->
                <div class="filtro-item">
                    <label for="producto">
                        <span class="icon"><i class="fas fa-list" style="font-size:20px;"></i></span> Categoría
                    </label>
                    <div class="filtro-wrapper">
                        <select id="filtro-producto" onchange="aplicarFiltros()">
                            <option value="">Todas</option>
                            <option value="Ventanas" <?php if($filtro_producto == 'Ventanas') echo 'selected'; ?>>Ventanas</option>
                            <option value="Puertas" <?php if($filtro_producto == 'Puertas') echo 'selected'; ?>>Puertas</option>
                            <option value="Barandillas" <?php if($filtro_producto == 'Barandillas') echo 'selected'; ?>>Barandillas</option>
                        </select>
                        <button type="button" class="btn-borrar" onclick="borrarFiltro('producto')">&times;</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menú lateral y listado -->
        <div class="productos-layout">
            <button class="boton-menu-lateral">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="white" viewBox="0 0 24 24">
                    <path d="M3 6h18M3 12h18M3 18h18" stroke="white" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>

            <div class="crear-producto" id="menu-lateral">
                <a href="ListadoProductosAdmin.php" class="menu-item">Productos</a>
                <a href="CrearProductoAdmin.php" class="menu-item">Crear Producto</a>
                <a href="ListadoClientesAdmin.php" class="menu-item">Listado de clientes</a>
                <a href="ListadoVentasAdmin.php" class="menu-item">Listado de ventas</a>
            </div>

            <div class="cuadro-fondo">
                <p class="header-tabla">Ventas Realizadas</p>

                <?php if ($total_registros == 0): ?>
                    
                    <!-- ESTADO VACÍO -->
                    <div class="empty-state-ventas">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>No hay ventas registradas</h3>
                        <p>No se han encontrado ventas con los filtros actuales.</p>
                        <?php if(!empty($filtro_fecha) || !empty($filtro_cliente) || !empty($filtro_producto)): ?>
                             <button onclick="borrarTodo()" style="margin-top:15px; background:#293661; color:white; padding:8px 15px; border:none; border-radius:5px; cursor:pointer;">Limpiar Filtros</button>
                        <?php endif; ?>
                    </div>

                <?php else: ?>

                    <div id="lista-ventas">
                        <?php foreach ($ventas as $venta): ?>
                            
                            <!-- TARJETA VENTA -->
                            <div class="venta item-venta">
                                
                                <!-- Botón Borrar -->
                                <form method="POST" action="" style="position: absolute; top: 10px; left: 10px;">
                                    <input type="hidden" name="id_eliminar" value="<?php echo $venta['id']; ?>">
                                    <button type="submit" class="boton-eliminar" title="Eliminar Venta" onclick="return confirm('¿Eliminar venta #<?php echo $venta['id']; ?>?');">✖</button>
                                </form>

                                <div class="venta-info">
                                    <p><strong>ID Venta:</strong> #<?php echo str_pad($venta['id'], 4, '0', STR_PAD_LEFT); ?></p>
                                    <p><strong>Cliente:</strong> <?php echo $venta['nombre_cli'] . ' ' . $venta['apellidos_cli']; ?></p>
                                    <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($venta['fecha'])); ?></p>
                                    <p><strong>Total:</strong> <?php echo number_format($venta['total'], 2); ?> €</p>
                                </div>
                                
                                <!-- Enlace a detalles -->
                                <a href="DetallesVentas.php?id=<?php echo $venta['id']; ?>" class="boton-detalles">
                                    <p>Ver detalles</p>
                                </a>
                            </div>

                        <?php endforeach; ?>
                    </div>

                    <!-- BOTÓN VER MÁS -->
                    <div class="contenedor-ver-mas">
                        <button id="btn-cargar-mas" class="btn-ver-mas">Ver más ventas</button>
                    </div>

                <?php endif; ?>
            </div>
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
                        <a href="#">Aviso Legal</a>
                        <span>•</span>
                        <a href="#">Política de Privacidad</a>
                        <span>•</span>
                        <a href="#">Política de Cookies</a>
                    </div>
                </div>
            </div>
        </footer>
    </div> 
    
    <!-- SCRIPT PAGINACIÓN Y FILTROS -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Menú Lateral
            const botonMenu = document.querySelector(".boton-menu-lateral");
            const menuLateral = document.getElementById("menu-lateral");
            if(botonMenu) {
                botonMenu.addEventListener("click", () => menuLateral.classList.toggle("oculto"));
            }

            // Paginación
            const items = document.querySelectorAll('.item-venta');
            const btnCargar = document.getElementById('btn-cargar-mas');
            const iniciales = 5; 
            const porCarga = 5;
            let visibles = iniciales;

            function actualizarVista() {
                if (items.length === 0) return;
                items.forEach((item, index) => {
                    if (index < visibles) item.style.display = 'flex'; // Flex para mantener diseño tarjeta
                    else item.style.display = 'none';
                });
                if (btnCargar) {
                    btnCargar.style.display = (visibles >= items.length) ? 'none' : 'inline-block';
                }
            }

            actualizarVista();

            if (btnCargar) {
                btnCargar.addEventListener('click', function() {
                    visibles += porCarga;
                    actualizarVista();
                });
            }
        });

        // Funciones de Filtro
        function aplicarFiltros() {
            const fecha = document.getElementById('filtro-fecha').value;
            const cliente = document.getElementById('filtro-cliente').value;
            const producto = document.getElementById('filtro-producto').value;
            
            const url = new URL(window.location.href);
            
            if (fecha) url.searchParams.set('fecha', fecha); else url.searchParams.delete('fecha');
            if (cliente) url.searchParams.set('cliente', cliente); else url.searchParams.delete('cliente');
            if (producto) url.searchParams.set('producto', producto); else url.searchParams.delete('producto');

            window.location.href = url.toString();
        }

        function checkEnter(event) {
            if (event.key === "Enter") aplicarFiltros();
        }

        function borrarFiltro(tipo) {
            document.getElementById('filtro-' + tipo).value = '';
            aplicarFiltros();
        }

        function borrarTodo() {
            window.location.href = 'ListadoVentasAdmin.php';
        }
    </script>
</body>
</html>