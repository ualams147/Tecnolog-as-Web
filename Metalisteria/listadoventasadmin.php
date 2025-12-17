<?php
include 'conexion.php';

// --- CONTROL DE SESIÓN E IDIOMA ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$lang = [];
if(isset($_SESSION['idioma']) && file_exists("idiomas/" . $_SESSION['idioma'] . ".php")) {
    include "idiomas/" . $_SESSION['idioma'] . ".php";
} else {
    include "idiomas/es.php";
}

// --- 1. CONSULTA DE NOTIFICACIONES (PROPUESTAS PENDIENTES) ---
$propuestas_pendientes = 0;
try {
    $sql_count = "SELECT COUNT(*) as total FROM carrito_personalizados WHERE estado = 'pendiente'";
    $stmt_count = $conn->query($sql_count);
    $propuestas_pendientes = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
} catch(Exception $e) { }

// --- 2. LÓGICA PARA ELIMINAR VENTA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_eliminar'])) {
    $idBorrar = $_POST['id_eliminar'];
    try {
        $stmt = $conn->prepare("DELETE FROM ventas WHERE id = ?");
        $stmt->execute([$idBorrar]);
        header("Location: listadoventasadmin.php");
        exit;
    } catch(PDOException $e) { }
}

// --- 3. FILTROS DE BÚSQUEDA ---
$where = "WHERE 1=1";
$params = [];

$filtro_fecha = $_GET['fecha'] ?? '';
$filtro_cliente = $_GET['cliente'] ?? '';
$filtro_producto = $_GET['producto'] ?? '';

if (!empty($filtro_fecha)) {
    $where .= " AND DATE(v.fecha) = :fecha";
    $params[':fecha'] = $filtro_fecha;
}
if (!empty($filtro_cliente)) {
    $where .= " AND (c.nombre LIKE :cliente OR c.apellidos LIKE :cliente)";
    $params[':cliente'] = "%" . $filtro_cliente . "%";
}
if (!empty($filtro_producto)) {
    $where .= " AND EXISTS (
        SELECT 1 FROM detalle_ventas dv 
        JOIN productos p ON dv.id_producto = p.id 
        JOIN categorias cat ON p.id_categoria = cat.id 
        WHERE dv.id_venta = v.id AND cat.nombre = :cat_nombre
    )";
    $params[':cat_nombre'] = $filtro_producto;
}

// --- 4. CONSULTA PRINCIPAL DE VENTAS ---
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
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/administrador.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Estilos específicos para esta vista */
        .item-venta { display: none; } 
        
        .empty-state-ventas {
            text-align: center; padding: 50px; display: flex; flex-direction: column; align-items: center;
            background: white; border-radius: 15px; border: 2px dashed #e0e0e0; width: 100%;
        }
        .empty-state-ventas i { font-size: 60px; color: #ccc; margin-bottom: 20px; }
        .empty-state-ventas h3 { color: #293661; margin-bottom: 10px; }
        .empty-state-ventas p { color: #666; }

        /* Estilo para el badge de notificación en el botón grande */
        .boton-anadir-nuevo { position: relative; text-decoration: none; }
        
        .badge-notif {
            position: absolute;
            /* AJUSTE: Cambiado de 15px a 28px para centrarlo verticalmente en el botón de 85px */
            top: 28px; 
            right: 30px;
            background-color: #e74c3c;
            color: white;
            font-size: 14px;
            font-weight: bold;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="listadoProductos-administrador">
        
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
                    <a href="listadoventasadmin.php" style="font-weight:bold; border-bottom: 2px solid currentColor;">Ventas</a> 
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
            <h1 class="titulo-principal">Listado Ventas</h1>

            <div class="filtros-en-titulo">
                <div class="filtro-item">
                    <label for="filtro-fecha">Fecha de registro</label>
                    <div class="filtro-wrapper" id="wrapper-fecha">
                        <input type="date" id="filtro-fecha" value="<?php echo $filtro_fecha; ?>" onchange="checkInput('fecha'); aplicarFiltros()">
                        <div class="input-icon"><i class="far fa-calendar-alt"></i></div>
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
                        <div class="input-icon"><i class="fas fa-search"></i></div>
                        <button type="button" class="btn-borrar" onclick="borrarFiltro('cliente')">×</button>
                        <div id="sugerencias-cliente" class="lista-autocompletado"></div>
                    </div>
                </div>

                <div class="filtro-item">
                    <label for="producto">Categoría</label>
                    <div class="filtro-wrapper" id="wrapper-producto">
                        <select id="producto" name="producto" onchange="checkInput('producto'); aplicarFiltros()"> 
                            <option value="" selected>Todos</option>
                            <option value="ventanas">Ventanas</option>
                            <option value="puertas">Puertas</option>
                            <option value="barandillas">Barandillas</option>
                            <option value="otros">Otros</option>
                        </select>
                        <div class="input-icon"><i class="fas fa-filter"></i></div>
                        <button type="button" class="btn-borrar" onclick="borrarFiltro('producto')">×</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="botones-superiores">
            <a href="admin_propuestas.php" class="boton-anadir-nuevo">
                <p><?php echo isset($lang['admin_ventas_btn_propuestas']) ? $lang['admin_ventas_btn_propuestas'] : 'Ver Propuestas'; ?></p>
                
                <?php if($propuestas_pendientes > 0): ?>
                    <span class="badge-notif"><?php echo $propuestas_pendientes; ?></span>
                <?php endif; ?>
            </a>
        </div>

        <div class="productos-layout">
            <button class="boton-menu-lateral">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="white" viewBox="0 0 24 24">
                    <path d="M3 6h18M3 12h18M3 18h18" stroke="white" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
            
            <div class="crear-producto" id="menu-lateral">
                <a href="listadoproductosadmin.php" class="menu-item">Productos</a>
                <a href="crearproductoadmin.php" class="menu-item">Crear Producto</a>
                <a href="listadoclientesadmin.php" class="menu-item">Listado de clientes</a>
                <a href="listadoventasadmin.php" class="menu-item">Listado de ventas</a>
            </div>
            
            <div class="cuadro-fondo">
                <p class="header-tabla">
                    <?php echo isset($lang['admin_ventas_titulo_lista']) ? $lang['admin_ventas_titulo_lista'] : 'Ventas Realizadas'; ?>
                </p>

                <?php if ($total_registros == 0): ?>
                    <div class="empty-state-ventas">
                        <i class="fas fa-shopping-cart"></i>
                        <h3><?php echo isset($lang['admin_ventas_sin_ventas']) ? $lang['admin_ventas_sin_ventas'] : 'No hay ventas registradas'; ?></h3>
                        <p>No se han encontrado ventas con los filtros actuales.</p>
                        <?php if(!empty($filtro_fecha) || !empty($filtro_cliente) || !empty($filtro_producto)): ?>
                             <button onclick="borrarTodo()" style="margin-top:15px; background:#293661; color:white; padding:8px 15px; border:none; border-radius:5px; cursor:pointer;">
                                <?php echo isset($lang['admin_ventas_filtro_limpiar']) ? $lang['admin_ventas_filtro_limpiar'] : 'Limpiar Filtros'; ?>
                             </button>
                        <?php endif; ?>
                    </div>
                <?php else: ?>

                    <div id="lista-ventas">
                        <?php foreach ($ventas as $venta): ?>
                            <div class="venta item-venta">
                                <form method="POST" action="listadoventasadmin.php" style="position: absolute; top: 10px; left: 10px;">
                                    <input type="hidden" name="id_eliminar" value="<?php echo $venta['id']; ?>">
                                    <button type="submit" class="boton-eliminar" title="Eliminar Venta" onclick="return confirm('¿Eliminar venta #<?php echo $venta['id']; ?>?');">✖</button>
                                </form>

                                <div class="venta-info">
                                    <p><strong>ID Venta:</strong> #<?php echo str_pad($venta['id'], 4, '0', STR_PAD_LEFT); ?></p>
                                    <p><strong>Cliente:</strong> <?php echo $venta['nombre_cli'] . ' ' . $venta['apellidos_cli']; ?></p>
                                    <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($venta['fecha'])); ?></p>
                                    <p><strong>Total:</strong> <?php echo number_format($venta['total'], 2); ?> €</p>
                                </div>
                                
                                <a href="detallesventas.php?id=<?php echo $venta['id']; ?>" class="boton-detalles">
                                    <p>Ver detalles</p>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="contenedor-ver-mas">
                        <button id="btn-cargar-mas" class="btn-ver-mas">Ver más ventas</button>
                    </div>

                <?php endif; ?>
            </div>
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
        // --- JAVASCRIPT (Sin cambios) ---
        let datosVentas = []; 
        let btnCargarElement = null;
        let visibleCount = 5;
        const loadStep = 5;

        function checkInput(tipo) {
            let input = document.getElementById('filtro-' + tipo);
            let wrapper = document.getElementById('wrapper-' + tipo);
            if (!input) input = document.getElementById(tipo); 
            if (!wrapper && input) wrapper = document.getElementById('wrapper-' + tipo);

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
            if (!input) input = document.getElementById(tipo);

            if (input) {
                input.value = '';
                checkInput(tipo);
                
                if (tipo === 'cliente') {
                    document.getElementById('sugerencias-cliente').style.display = 'none';
                    restaurarListadoCompleto(); 
                } else {
                    aplicarFiltros(); 
                }
            }
        }

        function mostrarSugerencias() {
            const input = document.getElementById('filtro-cliente');
            const texto = input.value.toLowerCase().trim();
            const contenedor = document.getElementById('sugerencias-cliente');
            contenedor.innerHTML = '';

            if (texto.length === 0) {
                contenedor.style.display = 'none';
                restaurarListadoCompleto();
                return;
            }

            const nombresVistos = new Set();
            const coincidencias = datosVentas.filter(venta => {
                const coincide = venta.nombreCliente.toLowerCase().includes(texto);
                if (coincide && !nombresVistos.has(venta.nombreCliente)) {
                    nombresVistos.add(venta.nombreCliente);
                    return true;
                }
                return false;
            });

            if (coincidencias.length > 0) {
                coincidencias.forEach(venta => {
                    const div = document.createElement('div');
                    div.classList.add('item-sugerencia');
                    div.innerHTML = `<strong>${venta.nombreCliente}</strong>`;
                    div.addEventListener('click', () => seleccionarCliente(venta.nombreCliente));
                    contenedor.appendChild(div);
                });
                contenedor.style.display = 'block';
            } else {
                contenedor.style.display = 'none';
            }
        }

        function seleccionarCliente(nombreCliente) {
            const input = document.getElementById('filtro-cliente');
            input.value = nombreCliente;
            checkInput('cliente');
            document.getElementById('sugerencias-cliente').style.display = 'none';

            const todasLasVentas = document.querySelectorAll('.item-venta');
            todasLasVentas.forEach(div => {
                const infoCliente = div.querySelector('.venta-info p:nth-child(2)').innerText;
                if (infoCliente.includes(nombreCliente)) div.style.display = 'flex';
                else div.style.display = 'none';
            });
            if(btnCargarElement) btnCargarElement.style.display = 'none';
        }

        function restaurarListadoCompleto() {
            const todasLasVentas = document.querySelectorAll('.item-venta');
            todasLasVentas.forEach((div, index) => {
                if (index < visibleCount) div.style.display = 'flex';
                else div.style.display = 'none';
            });
            if(btnCargarElement) {
                if (visibleCount >= todasLasVentas.length) btnCargarElement.style.display = 'none';
                else btnCargarElement.style.display = 'flex';
            }
        }

        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('wrapper-cliente');
            const lista = document.getElementById('sugerencias-cliente');
            if (lista && !wrapper.contains(e.target)) lista.style.display = 'none';
        });

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

        function checkEnter(event) {
            if (event.key === "Enter") {
                const texto = document.getElementById('filtro-cliente').value.toLowerCase();
                document.getElementById('sugerencias-cliente').style.display = 'none';
                const todas = document.querySelectorAll('.item-venta');
                todas.forEach(div => {
                    if (div.innerText.toLowerCase().includes(texto)) div.style.display = 'flex';
                    else div.style.display = 'none';
                });
                if(btnCargarElement) btnCargarElement.style.display = 'none';
            }
        }

        function borrarTodo() {
            window.location.href = 'listadoventasadmin.php';
        }

        document.addEventListener("DOMContentLoaded", function () {
            checkInput('fecha'); checkInput('cliente'); checkInput('producto');

            const botonMenu = document.querySelector(".boton-menu-lateral");
            const menuLateral = document.getElementById("menu-lateral");
            if (botonMenu && menuLateral) {
                botonMenu.addEventListener("click", () => menuLateral.classList.toggle("oculto"));
            }

            const itemsDOM = document.querySelectorAll('.item-venta');
            btnCargarElement = document.getElementById('btn-cargar-mas');

            itemsDOM.forEach(div => {
                const pCliente = div.querySelector('.venta-info p:nth-child(2)');
                let nombreLimpio = "Cliente Desconocido";
                if (pCliente) nombreLimpio = pCliente.innerText.replace('Cliente:', '').trim();
                datosVentas.push({ nombreCliente: nombreLimpio, elemento: div });
            });

            restaurarListadoCompleto();

            if (btnCargarElement) {
                btnCargarElement.addEventListener('click', function(e) {
                    e.preventDefault();
                    visibleCount += loadStep;
                    restaurarListadoCompleto();
                });
            }
        });
    </script>
</body>
</html>