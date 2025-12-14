<?php
include '../conexion.php';

// --- LÓGICA PARA ELIMINAR PRODUCTO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_eliminar'])) {
    $idBorrar = $_POST['id_eliminar'];
    try {
        $stmtBorrar = $conn->prepare("DELETE FROM productos WHERE id = ?");
        $stmtBorrar->execute([$idBorrar]);
        header("Location: ../Administrador/ListadoProductosAdmin.php");
        exit;
    } catch(PDOException $e) {
        // Error handling
    }
}

// --- CONSULTA DE PRODUCTOS ---
$sql = "SELECT p.*, c.nombre as nombre_categoria 
        FROM productos p 
        LEFT JOIN categorias c ON p.id_categoria = c.id
        ORDER BY p.id DESC"; 

$stmt = $conn->query($sql);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_productos = count($productos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listados de Productos Admin - Metalful</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/stylesListadoProductosAdmin.css">
    <link rel="stylesheet" href="../css/CabeceraFooterAdmin.css">
</head>
<body>
    <div class="listadoProductos-administrador">
        
        <header class="cabecera">
            <div class="container">
                <div class="logo-main">
                    <a href="../Administrador/indexAdmin.php" class="logo-main">
                        <img src="../imagenes/logo.png" alt="Logo Metalful">
                        <div class="logo-text">
                            <span> Metalisteria</span>
                            <strong>Fulsan</strong>
                        </div>
                    </a>
                </div>
                
                <nav class="nav-bar">
                    <a href="../Administrador/ListadoVentasAdmin.php">Ventas</a>
                    <a href="../Administrador/ListadoProductosAdmin.php" class="activo" style="font-weight:bold; border-bottom: 2px solid currentColor;">Productos</a> 
                    <a href="../Administrador/ListadoClientesAdmin.php">Clientes</a>
                </nav>

                <div class="log-out">
                    <a href="../Visitante/index.php">Cerrar Sesión</a>
                </div>
            </div>
        </header>

        <div class="titulo-section">
            <div class="degradado"></div>
            <div class="recuadro-fondo"></div>
    
            <!-- Título con reset -->
            <h1 class="titulo-principal" onclick="toggleFiltro('todos')" title="Ver todos">Listado Productos</h1>

            <!-- FILTROS ACTIVADOS (IDs de BD: 1=Ventanas, 2=Puertas, 5=Barandillas) -->
            <div class="filtro-productos">
                <button id="btn-filtro-2" class="filtro-text" onclick="toggleFiltro('2')">Puertas</button>
                <button id="btn-filtro-1" class="filtro-text" onclick="toggleFiltro('1')">Ventanas</button>
                <button id="btn-filtro-5" class="filtro-text" onclick="toggleFiltro('5')">Barandillas</button>
                <button id="btn-filtro-otros" class="filtro-text" onclick="toggleFiltro('otros')">Otras</button>
            </div>
        </div>
        
        <div class="botones-superiores">
            <a href="../Administrador/CrearProductoAdmin.php" class="boton-anadir-nuevo">
                <p>+ Añadir producto</p>
            </a>
        </div>

        <div class="productos-layout">
            
            <button class="boton-menu-lateral">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="white" viewBox="0 0 24 24">
                    <path d="M3 6h18M3 12h18M3 18h18" stroke="white" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
            
            <div class="crear-producto" id="menu-lateral">
                <a href="../Administrador/ListadoProductosAdmin.php" class="menu-item">Productos</a>
                <a href="../Administrador/crear_producto.php" class="menu-item">Crear Producto</a>
                <a href="../Administrador/ListadoClientesAdmin.php" class="menu-item">Listado de clientes</a>
                <a href="../Administrador/ListadoVentasAdmin.php" class="menu-item">Listado de ventas</a>
            </div>
            
            <div class="cuadro-fondo">
                <p class="header-tabla">Catálogo de Productos</p>

                <!-- 1. BOTÓN AÑADIR (SIEMPRE VISIBLE) -->
                <!-- <div class="add-btn-container"> ... (Ya tienes uno arriba grande, este sobra) ... </div> -->

                <?php if ($total_productos == 0): ?>
                    <div class="empty-state" style="text-align: center; padding: 50px;">
                        <i class="fas fa-box-open" style="font-size: 50px; color: #ccc;"></i>
                        <h2>No hay productos disponibles</h2>
                        <p>Actualmente la base de datos está vacía.</p>
                    </div>
                <?php else: ?>

                    <!-- GRID PRODUCTOS -->
                    <div class="grid-productos" id="lista-productos">
                        <?php foreach ($productos as $prod): ?>
                            
                            <!-- Clase 'item-producto' y 'data-categoria' VITALES para JS -->
                            <div class="producto item-producto" data-categoria="<?php echo $prod['id_categoria']; ?>">
                                
                                <form method="POST" action="../Administrador/ListadoProductosAdmin.php" style="display:inline;" onsubmit="return confirm('¿Estás seguro de querer eliminar este producto?');">
                                    <input type="hidden" name="id_eliminar" value="<?php echo $prod['id']; ?>">
                                    <button type="submit" class="boton-eliminar" title="Eliminar">✖</button>
                                </form>
                                
                                <div class="producto-info">
                                    <p><strong>Nombre:</strong> <?php echo $prod['nombre']; ?></p>
                                    <p><strong>Categoría:</strong> <?php echo $prod['nombre_categoria']; ?></p>
                                    <p><strong>Descripción:</strong> <?php echo $prod['descripcion']; ?></p>
                                    <p><strong>Precio:</strong> <?php echo $prod['precio']; ?>€</p>
                                </div>

                                <a href="../Administrador/ModificarProductoAdmin.php?id=<?php echo $prod['id']; ?>" class="boton-editar-pequeno">
                                    <p>Editar</p>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Mensaje No Resultados -->
                    <div id="mensaje-no-resultados">
                        <h3>No hay productos con los filtros seleccionados.</h3>
                    </div>

                    <!-- Botón Ver Más -->
                    <div class="contenedor-ver-mas">
                        <button id="btn-cargar-mas" class="btn-ver-mas">Ver más productos</button>
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
    
    <!-- SCRIPT DE FILTRADO Y PAGINACIÓN COMBINADOS -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // --- VARIABLES ---
            const productos = document.querySelectorAll('.item-producto');
            const btnCargar = document.getElementById('btn-cargar-mas');
            const mensajeNoResultados = document.getElementById('mensaje-no-resultados');
            const botonMenu = document.querySelector(".boton-menu-lateral");
            const menuLateral = document.getElementById("menu-lateral");

            // Configuración
            const iniciales = 5; 
            const porCarga = 5;
            let visiblesActuales = iniciales;
            let filtrosActivos = [];

            // --- INICIALIZACIÓN ---
            // IMPORTANTE: Al cargar la página, ejecutar la vista para ocultar los sobrantes
            actualizarVista(); 

            // Evento Menú Lateral
            if(botonMenu && menuLateral) {
                botonMenu.addEventListener("click", () => menuLateral.classList.toggle("oculto"));
            }

            // Evento Botón Ver Más
            if (btnCargar) {
                btnCargar.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Aumentamos el límite de visibles
                    visiblesActuales += porCarga;
                    // Recalculamos qué se ve
                    actualizarVista();
                });
            }

            // --- FUNCIONES DE FILTRADO (Globales para onclick) ---
            window.toggleFiltro = function(categoria) {
                if (categoria === 'todos') {
                    filtrosActivos = []; // Limpiar filtros
                } else {
                    // Lógica Toggle (poner/quitar)
                    if (filtrosActivos.includes(categoria)) {
                        filtrosActivos = filtrosActivos.filter(c => c !== categoria);
                    } else {
                        filtrosActivos.push(categoria);
                    }
                }
                
                // IMPORTANTE: Al cambiar filtro, reseteamos la paginación a la página 1 (5 productos)
                visiblesActuales = iniciales;
                
                actualizarEstilosBotones();
                actualizarVista();
            };

            function actualizarEstilosBotones() {
                // Quitamos activo a todos
                document.querySelectorAll('.filtro-text').forEach(btn => btn.classList.remove('activo'));
                // Ponemos activo a los seleccionados
                filtrosActivos.forEach(cat => {
                    const btn = document.getElementById('btn-filtro-' + cat);
                    if(btn) btn.classList.add('activo');
                });
            }

            // --- FUNCIÓN MAESTRA QUE DECIDE QUÉ SE VE ---
            function actualizarVista() {
                let productosFiltrados = 0; // Total que coinciden con el filtro activo
                let productosMostradosAhora = 0; // Total que hemos pintado en pantalla en esta vuelta

                productos.forEach(prod => {
                    const catProducto = prod.getAttribute('data-categoria');
                    let cumpleFiltro = false;

                    // 1. ¿Cumple el filtro?
                    if (filtrosActivos.length === 0) {
                        cumpleFiltro = true; // Si no hay filtros, valen todos
                    } else {
                        // Lógica OR (si coincide con alguno de los activos)
                        filtrosActivos.forEach(filtro => {
                            if (filtro === 'otros') {
                                if (['3', '4', '6'].includes(catProducto)) cumpleFiltro = true;
                            } else {
                                if (catProducto === filtro) cumpleFiltro = true;
                            }
                        });
                    }

                    // 2. Lógica de visibilidad (Filtro + Paginación)
                    if (cumpleFiltro) {
                        productosFiltrados++; // Este producto es válido para el filtro actual
                        
                        // Solo lo mostramos si está dentro del cupo de paginación actual
                        if (productosMostradosAhora < visiblesActuales) {
                            prod.style.display = 'flex'; // MOSTRAR
                            productosMostradosAhora++;
                        } else {
                            prod.style.display = 'none'; // OCULTAR (Paginación: está en la página siguiente)
                        }
                    } else {
                        prod.style.display = 'none'; // OCULTAR (No cumple el filtro)
                    }
                });

                // 3. Gestionar botón "Ver Más"
                if (btnCargar) {
                    // Si hemos mostrado menos de los que existen filtrados, es que quedan más -> enseñamos botón
                    if (productosMostradosAhora < productosFiltrados) {
                        btnCargar.style.display = 'block';
                    } else {
                        btnCargar.style.display = 'none';
                    }
                }

                // 4. Mensaje "No hay resultados"
                if (productosFiltrados === 0 && productos.length > 0) {
                    if(mensajeNoResultados) mensajeNoResultados.style.display = 'block';
                } else {
                    if(mensajeNoResultados) mensajeNoResultados.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>