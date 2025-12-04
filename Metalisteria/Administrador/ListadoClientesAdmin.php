<?php
include 'conexion.php';

// --- 1. LÓGICA PARA ELIMINAR CLIENTE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_eliminar'])) {
    $idBorrar = $_POST['id_eliminar'];
    try {
        $stmtBorrar = $conn->prepare("DELETE FROM clientes WHERE id = ?");
        $stmtBorrar->execute([$idBorrar]);
        header("Location: ListadoClientesAdmin.php");
        exit;
    } catch(PDOException $e) {
        // Error handling
    }
}

// --- 2. LÓGICA DE FILTRADO ---
$where = "WHERE 1=1"; 
$params = [];

// Variables para mantener los filtros en los inputs (PERSISTENCIA)
$filtro_fecha = $_GET['fecha'] ?? '';
$filtro_cliente = $_GET['cliente'] ?? '';

// A) Filtro Fecha
if (!empty($filtro_fecha)) {
    $where .= " AND DATE(fecha_registro) >= :fecha";
    $params[':fecha'] = $filtro_fecha;
}

// B) Filtro Cliente
if (!empty($filtro_cliente)) {
    $busqueda = "%" . $filtro_cliente . "%";
    $where .= " AND (nombre LIKE :busqueda OR apellidos LIKE :busqueda OR dni LIKE :busqueda OR email LIKE :busqueda)";
    $params[':busqueda'] = $busqueda;
}

// Consulta Final
$sql = "SELECT * FROM clientes $where ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_clientes = count($clientes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listados Clientes Admin - Metalful</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stylesListadoClientesAdmin.css">
</head>
<body>
    <div class="ListadoClientesAdmin">
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
                    <a href="ListadoVentasAdmin.php">Ventas</a>
                    <a href="ListadoProductosAdmin.php">Productos</a>
                    <a href="ListadoClientesAdmin.php" style="font-weight:bold; border-bottom: 2px solid currentColor;">Clientes</a> 
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
            <h1 class="titulo-principal">Listado Clientes</h1>

            <!-- Filtros -->
            <div class="filtros-en-titulo">
                
                <!-- FILTRO FECHA -->
                <div class="filtro-item">
                    <label for="filtro-fecha">
                        <span class="icon">
                            <svg width="20" height="20" viewBox="0 0 40 40" fill="none"><path d="M33.3333 6.66675H6.66667C4.82572 6.66675 3.33334 8.15913 3.33334 10.0001V36.6667C3.33334 38.5077 4.82572 40.0001 6.66667 40.0001H33.3333C35.1743 40.0001 36.6667 38.5077 36.6667 36.6667V10.0001C36.6667 8.15913 35.1743 6.66675 33.3333 6.66675Z" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><path d="M26.6667 3.33325V9.99992" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><path d="M13.3333 3.33325V9.99992" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.33334 16.6667H36.6667" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        Fecha
                    </label>
                    <div class="filtro-wrapper">
                        <!-- CORREGIDO: ID coincide con JS, onchange añadido, value añadido -->
                        <input type="date" id="filtro-fecha" value="<?php echo $filtro_fecha; ?>" onchange="aplicarFiltros()">
                        <!-- CORREGIDO: Button type="button" y evento onclick -->
                        <button type="button" class="btn-borrar" onclick="borrarFiltro('fecha')">&times;</button>
                    </div>
                </div>

                <!-- FILTRO CLIENTE -->
                <div class="filtro-item">
                    <label for="filtro-cliente">
                        <span class="icon">
                            <svg width="20" height="20" viewBox="0 0 40 40" fill="none"><path d="M33.3333 35V31.6667C33.3333 29.8986 32.631 28.2029 31.3807 26.9526C30.1305 25.7024 28.4348 25 26.6667 25H13.3333C11.5652 25 9.86953 25.7024 8.61929 26.9526C7.36905 28.2029 6.66667 29.8986 6.66667 31.6667V35" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><path d="M20 18.3333C23.6819 18.3333 26.6667 15.3486 26.6667 11.6667C26.6667 7.98477 23.6819 5 20 5C16.3181 5 13.3333 7.98477 13.3333 11.6667C13.3333 15.3486 16.3181 18.3333 20 18.3333Z" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        Cliente
                    </label>
                    <div class="filtro-wrapper">
                        <!-- CORREGIDO: ID coincide con JS, onkeypress añadido, value añadido -->
                        <input type="text" id="filtro-cliente" placeholder="Buscar..." value="<?php echo htmlspecialchars($filtro_cliente); ?>" onkeypress="checkEnter(event)">
                        <button type="button" class="btn-borrar" onclick="borrarFiltro('cliente')">&times;</button>
                    </div>
                </div>

                <div class="filtro-item">
                    <label for="producto">
                        <span class="icon">
                            <svg width="20" height="20" viewBox="0 0 45 45" fill="none"><path d="M37.5 15H15M37.5 22.5H15M37.5 30H15M7.5 15H7.51875M7.5 22.5H7.51875M7.5 30H7.51875" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        Producto
                    </label>
                    <div class="filtro-wrapper">
                        <select id="producto" name="producto">
                            <option value="" selected>Todos</option>
                            <option value="ventanas">Ventanas</option>
                            <option value="puertas">Puertas</option>
                        </select>
                        <button type="button" class="btn-borrar">&times;</button>
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
                <p class="header-tabla">Información clientes</p>

                <?php if ($total_clientes == 0): ?>
                    <!-- ESTADO VACÍO -->
                    <div class="empty-state-clientes">
                        <?php if(!empty($filtro_fecha) || !empty($filtro_cliente)): ?>
                            <!-- Caso: Filtros sin resultados -->
                            <h3 style="color:#293661; font-size:24px; text-align:center;">No se encontraron resultados</h3>
                            <p style="color:#666; text-align:center;">No hay clientes que coincidan con tu búsqueda.</p>
                            <div style="text-align:center; margin-top:20px;">
                                <button onclick="borrarTodo()" style="background:#293661; color:white; padding:10px 20px; border:none; border-radius:5px; cursor:pointer;">Limpiar Filtros</button>
                            </div>
                        <?php else: ?>
                            <!-- Caso: BD vacía -->
                            <div style="text-align: center;">
                                <h3>No hay clientes registrados.</h3>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>

                    <!-- BUCLE DE CLIENTES REALES -->
                    <?php foreach ($clientes as $cli): ?>
                        <div class="cliente">
                            
                            <form method="POST" action="ListadoClientesAdmin.php" style="position: absolute; top: 10px; left: 10px;">
                                <input type="hidden" name="id_eliminar" value="<?php echo $cli['id']; ?>">
                                <button type="submit" class="boton-eliminar" title="Eliminar Cliente" onclick="return confirm('¿Estás seguro de eliminar a <?php echo $cli['nombre']; ?>?');">✖</button>
                            </form>

                            <div class="cliente-info">
                                <p><strong>Nombre:</strong> <?php echo $cli['nombre']; ?></p>
                                <p><strong>Apellidos:</strong> <?php echo $cli['apellidos']; ?></p>
                                <p><strong>Correo:</strong> <?php echo $cli['email']; ?></p>
                                <p><strong>DNI:</strong> <?php echo $cli['dni']; ?></p>
                                <p><strong>Teléfono:</strong> <?php echo $cli['telefono']; ?></p>
                                <p><strong>Domicilio:</strong> <?php echo $cli['direccion'] . ', ' . $cli['ciudad']; ?></p>
                            </div>
                            
                            <a href="ModificarDatosCliente.php?id=<?php echo $cli['id']; ?>" class="boton-editar-pequeno">
                                <p>Editar</p>
                            </a>
                        </div>
                    <?php endforeach; ?>

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
        document.addEventListener("DOMContentLoaded", function () {
            const botonMenu = document.querySelector(".boton-menu-lateral");
            const menuLateral = document.getElementById("menu-lateral");

            if (botonMenu) {
                botonMenu.addEventListener("click", function () {
                    menuLateral.classList.toggle("oculto");
                });
            }
        });

        // Función principal para recargar la página con parámetros
        function aplicarFiltros() {
            const fecha = document.getElementById('filtro-fecha').value;
            const cliente = document.getElementById('filtro-cliente').value;
            
            // Construimos la URL con los parámetros GET
            const url = new URL(window.location.href);
            
            if (fecha) url.searchParams.set('fecha', fecha);
            else url.searchParams.delete('fecha');

            if (cliente) url.searchParams.set('cliente', cliente);
            else url.searchParams.delete('cliente');

            window.location.href = url.toString();
        }

        // Detectar tecla Enter en el buscador de texto
        function checkEnter(event) {
            if (event.key === "Enter") {
                aplicarFiltros();
            }
        }

        // Borrar un filtro específico
        function borrarFiltro(tipo) {
            const input = document.getElementById('filtro-' + tipo);
            if(input) {
                input.value = '';
                aplicarFiltros(); // Recargar tras borrar
            }
        }

        // Borrar todos
        function borrarTodo() {
            window.location.href = 'ListadoClientesAdmin.php';
        }
    </script>
</body>
</html>