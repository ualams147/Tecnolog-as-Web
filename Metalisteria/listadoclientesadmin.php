<?php
include 'conexion.php';

// --- 1. LÓGICA PARA ELIMINAR CLIENTE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_eliminar'])) {
    $idBorrar = $_POST['id_eliminar'];
    try {
        $stmtBorrar = $conn->prepare("DELETE FROM clientes WHERE id = ?");
        $stmtBorrar->execute([$idBorrar]);
        // Redirección corregida
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
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/administrador.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="ListadoClientesAdmin">
        <header class="cabecera">
            <div class="container">
                <div class="logo-main">
                    <a href="indexAdmin.php" class="logo-main">
                        <img src="imagenes/logo.png" alt="Logo Metalful">
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
                    <a href="index.php">Cerrar Sesión</a>
                </div>

            </div>
        </header>
    
        <div class="titulo-section">
            <div class="degradado"></div>
            <div class="recuadro-fondo"></div>
            <h1 class="titulo-principal">Listado Clientes</h1>

            <div class="filtros-en-titulo">
                
                <div class="filtro-item">
                    <label for="filtro-fecha">Fecha de registro</label>
                    <div class="filtro-wrapper" id="wrapper-fecha">
                        <input type="date" id="filtro-fecha" value="<?php echo $filtro_fecha; ?>" onchange="checkInput('fecha'); aplicarFiltros()">
                        
                        <div class="input-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>

                        <button type="button" class="btn-borrar" onclick="borrarFiltro('fecha')">×</button>
                    </div>
                </div>

                <div class="filtro-item">
                    <label for="filtro-cliente">Nombre cliente</label>
                    <div class="filtro-wrapper" id="wrapper-cliente">
                        <input type="text" id="filtro-cliente" placeholder="Buscar..." value="<?php echo htmlspecialchars($filtro_cliente); ?>" autocomplete="off" oninput="checkInput('cliente'); mostrarSugerencias()" onkeypress="checkEnter(event)">
                        
                        <div class="input-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>

                        <button type="button" class="btn-borrar" onclick="borrarFiltro('cliente')">×</button>
                        
                        <div id="sugerencias-cliente" class="lista-autocompletado"></div>
                    </div>
                </div>

                <div class="filtro-item">
                    <label for="producto">Categoría de productos</label>
                    <div class="filtro-wrapper" id="wrapper-producto">
                        <select id="producto" name="producto" onchange="checkInput('producto')"> <option value="" selected>Todos</option>
                            <option value="ventanas">Ventanas</option>
                            <option value="puertas">Puertas</option>
                        </select>
                        
                        <div class="input-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        </div>

                        <button type="button" class="btn-borrar" onclick="borrarFiltro('producto')">×</button>
                    </div>
                </div>

            </div>
        </div>
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
                    <div class="empty-state-clientes">
                        <?php if(!empty($filtro_fecha) || !empty($filtro_cliente)): ?>
                            <h3 style="color:#293661; font-size:24px; text-align:center;">No se encontraron resultados</h3>
                            <p style="color:#666; text-align:center;">No hay clientes que coincidan con tu búsqueda.</p>
                            <div style="text-align:center; margin-top:20px;">
                                <button onclick="borrarTodo()" style="background:#293661; color:white; padding:10px 20px; border:none; border-radius:5px; cursor:pointer;">Limpiar Filtros</button>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center;">
                                <h3>No hay clientes registrados.</h3>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>

                    <div id="lista-clientes-container"> <?php foreach ($clientes as $cli): ?>
                            
                            <div class="cliente item-cliente">
                                
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
                    
                    </div> <div class="contenedor-ver-mas">
                        <button id="btn-cargar-mas" class="btn-ver-mas" style="display: none;">Ver más clientes</button>
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
        // ==========================================
        // VARIABLES GLOBALES
        // ==========================================
        let datosClientes = []; // Array limpio con info de clientes para buscar rápido
        let btnCargarElement = null;
        let visibleCount = 5;
        const loadStep = 5;

        // ==========================================
        // 1. FUNCIONES VISUALES (ICONO/CRUZ)
        // ==========================================
        function checkInput(tipo) {
            let input = document.getElementById('filtro-' + tipo);
            let wrapper = document.getElementById('wrapper-' + tipo);
            if (!input) input = document.getElementById(tipo); // Fallback select
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
                    // Si borramos cliente, ocultamos sugerencias y restauramos la lista original
                    document.getElementById('sugerencias-cliente').style.display = 'none';
                    restaurarListadoCompleto(); 
                } else if (tipo !== 'producto') {
                    aplicarFiltros(); // Recargar si es fecha
                }
            }
        }

        // ==========================================
        // 2. LÓGICA DE AUTOCOMPLETADO (LO NUEVO)
        // ==========================================
        
        function mostrarSugerencias() {
            const input = document.getElementById('filtro-cliente');
            const texto = input.value.toLowerCase().trim();
            const contenedor = document.getElementById('sugerencias-cliente');
            
            // Limpiar sugerencias previas
            contenedor.innerHTML = '';

            // Si no hay texto, ocultar lista y no hacer nada más (no filtramos abajo aún)
            if (texto.length === 0) {
                contenedor.style.display = 'none';
                restaurarListadoCompleto(); // Opcional: si borras todo, vuelve al estado inicial
                return;
            }

            // Buscar coincidencias en nuestro array de memoria
            const coincidencias = datosClientes.filter(cliente => 
                cliente.busqueda.includes(texto)
            );

            if (coincidencias.length > 0) {
                // Crear elementos de la lista
                coincidencias.forEach(cliente => {
                    const div = document.createElement('div');
                    div.classList.add('item-sugerencia');
                    // Mostramos Nombre y DNI en la sugerencia
                    div.innerHTML = `<strong>${cliente.nombre}</strong> <span style="font-size:12px; color:#666">(${cliente.dni})</span>`;
                    
                    // Al hacer clic en una sugerencia...
                    div.addEventListener('click', () => {
                        seleccionarCliente(cliente);
                    });
                    
                    contenedor.appendChild(div);
                });
                contenedor.style.display = 'block';
            } else {
                contenedor.style.display = 'none';
            }
        }

        function seleccionarCliente(clienteData) {
            // 1. Poner el nombre en el input
            const input = document.getElementById('filtro-cliente');
            input.value = clienteData.nombre; // O el nombre completo
            checkInput('cliente'); // Asegurar que sale la X

            // 2. Ocultar sugerencias
            document.getElementById('sugerencias-cliente').style.display = 'none';

            // 3. FILTRAR EL CUERPO (Ahora sí actualizamos abajo)
            const todosLosDivs = document.querySelectorAll('.item-cliente');
            
            todosLosDivs.forEach(div => {
                // Comparamos el ID único para no fallar con nombres duplicados
                // Asumimos que el input hidden del form de eliminar tiene el ID
                const idInput = div.querySelector('input[name="id_eliminar"]');
                
                if (idInput && idInput.value == clienteData.id) {
                    div.style.display = 'flex';
                } else {
                    div.style.display = 'none';
                }
            });

            // 4. Ocultar el botón "Ver más" porque ya estamos viendo uno específico
            if(btnCargarElement) btnCargarElement.style.display = 'none';
        }

        // Función para volver a ver los 5 primeros si borramos la búsqueda
        function restaurarListadoCompleto() {
            const todosLosDivs = document.querySelectorAll('.item-cliente');
            
            // Volvemos a la lógica de paginación
            todosLosDivs.forEach((div, index) => {
                if (index < visibleCount) div.style.display = 'flex';
                else div.style.display = 'none';
            });

            // Restaurar botón si hace falta
            if(btnCargarElement) {
                if (visibleCount >= todosLosDivs.length) btnCargarElement.style.display = 'none';
                else btnCargarElement.style.display = 'flex';
            }
        }

        // Cerrar autocompletado si clicamos fuera
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('wrapper-cliente');
            const lista = document.getElementById('sugerencias-cliente');
            if (lista && !wrapper.contains(e.target)) {
                lista.style.display = 'none';
            }
        });


        // ==========================================
        // 3. INICIALIZACIÓN Y OTRAS FUNCIONES
        // ==========================================
        
        function aplicarFiltros() {
            // Filtro clásico de servidor (Fecha)
            const fecha = document.getElementById('filtro-fecha').value;
            const url = new URL(window.location.href);
            if (fecha) url.searchParams.set('fecha', fecha);
            else url.searchParams.delete('fecha');
            // Nota: El cliente lo filtramos por JS, no recargamos
            window.location.href = url.toString();
        }

        function checkEnter(event) {
            if (event.key === "Enter") {
                // Si pulsa enter, aplicamos filtro de texto puro en el listado
                const texto = document.getElementById('filtro-cliente').value.toLowerCase();
                document.getElementById('sugerencias-cliente').style.display = 'none';
                
                // Filtrar abajo por texto genérico (no selección exacta)
                const todos = document.querySelectorAll('.item-cliente');
                todos.forEach(div => {
                    if (div.innerText.toLowerCase().includes(texto)) div.style.display = 'flex';
                    else div.style.display = 'none';
                });
                if(btnCargarElement) btnCargarElement.style.display = 'none';
            }
        }

        function borrarTodo() {
            window.location.href = 'ListadoClientesAdmin.php';
        }

        document.addEventListener("DOMContentLoaded", function () {
            // 1. Inicializar Visuales
            checkInput('fecha');
            checkInput('cliente');
            checkInput('producto');

            // 2. Menú Lateral
            const botonMenu = document.querySelector(".boton-menu-lateral");
            const menuLateral = document.getElementById("menu-lateral");
            if (botonMenu && menuLateral) {
                botonMenu.addEventListener("click", () => menuLateral.classList.toggle("oculto"));
            }

            // 3. PREPARAR DATOS PARA AUTOCOMPLETADO
            // Leemos el DOM para crear nuestro índice de búsqueda
            const itemsDOM = document.querySelectorAll('.item-cliente');
            btnCargarElement = document.getElementById('btn-cargar-mas');

            itemsDOM.forEach(div => {
                // Extraemos datos de cada tarjeta para buscar
                const textoCompleto = div.innerText.toLowerCase(); // Todo el texto visible
                // Sacamos el nombre "limpio" buscando el primer <p> o strong
                // Esto es un aproximado, ajustamos buscando el strong del nombre
                const nombreRaw = div.querySelector('.cliente-info p:nth-child(1)').innerText.replace('Nombre:', '').trim();
                const dniRaw = div.querySelector('.cliente-info p:nth-child(4)').innerText.replace('DNI:', '').trim();
                const idRaw = div.querySelector('input[name="id_eliminar"]').value;

                datosClientes.push({
                    id: idRaw,
                    nombre: nombreRaw, // Para poner en el input al seleccionar
                    dni: dniRaw,
                    busqueda: textoCompleto // Para filtrar sucio
                });
            });

            // 4. Inicializar Paginación "Ver Más"
            restaurarListadoCompleto();

            // Evento Botón
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