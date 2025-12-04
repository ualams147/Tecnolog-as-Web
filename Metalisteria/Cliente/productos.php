<?php
session_start();
include '../conexion.php'; 

// 1. CONSULTA: Traemos productos y también su categoría
try {
    $sql = "SELECT * FROM productos WHERE id IN (SELECT MIN(id) FROM productos GROUP BY referencia)";
    $stmt = $conn->query($sql);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al cargar productos: " . $e->getMessage();
    die();
}

// Lógica del contador del menú
$total_items = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $total_items += $item['cantidad'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Metalistería Fulsan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/productos.css">
    
    <style>
        /* Ocultamos productos por defecto */
        .item-producto { display: none; }
        
        /* Estilo para indicar qué filtros están activos */
        .cat-card.active .cat-frame {
            border: 3px solid #a0d2ac;
            box-shadow: 0 0 15px rgba(160, 210, 172, 0.6);
            transform: translateY(-5px);
        }
        .cat-card.active .cat-name {
            color: #a0d2ac;
        }
        
        .hero-title { cursor: pointer; transition: opacity 0.2s; }
        .hero-title:hover { opacity: 0.8; }
    </style>
</head>
<body>
    <div class="visitante-productos">
        
        <header class="cabecera">
            <div class="container">
                <div class="logo-main">
                    <a href="index.php" class="logo-link">
                        <img src="../imagenes/logo.png" alt="Logo Metalful">
                        <div class="logo-text">
                            <span>Metalistería</span>
                            <strong>Fulsan</strong>
                        </div>
                    </a>
                </div>

                <nav class="nav-bar">
                    <a href="conocenos.php">Conócenos</a>
                    <a href="productos.php" class="activo">Productos</a>
                    <a href="carrito.php">
                        Carrito 
                        <?php if($total_items > 0): ?>
                            <span style="background: #e74c3c; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; position: relative; top: -2px;">
                                <?php echo $total_items; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <a href="IniciarSesion.php" id="link-login">Iniciar Sesión</a>
                </nav>

                <div class="sign-in" id="box-registro">
                    <a href="registro.php" id="link-registro">Registrarse</a>
                </div>
            </div>
        </header>

        <section class="hero-productos">
            <h1 class="hero-title" onclick="filtrar('todos')" title="Clic para ver todos">Nuestros productos</h1>
            
            <div class="categorias-container">
                <div class="cat-card" onclick="filtrar('2')" id="cat-2">
                    <div class="cat-frame"><img src="../imagenes/p1.png" alt="Puertas"></div>
                    <span class="cat-name">PUERTAS</span>
                </div>

                <div class="cat-card" onclick="filtrar('1')" id="cat-1">
                    <div class="cat-frame"><img src="../imagenes/v1.png" alt="Ventanas"></div>
                    <span class="cat-name">VENTANAS</span>
                </div>

                <div class="cat-card" onclick="filtrar('5')" id="cat-5">
                    <div class="cat-frame"><img src="../imagenes/b1.png" alt="Barandillas"></div>
                    <span class="cat-name">BARANDILLAS</span>
                </div>

                <div class="cat-card" onclick="filtrar('otros')" id="cat-otros">
                    <div class="cat-frame"><img src="../imagenes/otro.png" alt="Otras"></div>
                    <span class="cat-name">OTRAS<br>ESTRUCTURAS</span>
                </div>
            </div>
        </section>

        <main class="catalogo-main container">
            
            <div class="cta-medida-info">
                <div class="cta-content">
                    <h2>Crea tu producto a Medida</h2>
                    <p>Diseñamos y fabricamos exactamente lo que necesitas</p>
                </div>
            </div>

            <div class="productos-grid" id="lista-productos">
                
                <?php if (count($productos) > 0): ?>
                    <?php foreach ($productos as $producto): ?>
                        
                        <div class="prod-card-outer item-producto" data-categoria="<?php echo $producto['id_categoria']; ?>">
                            <div class="prod-card-inner">
                                <div class="prod-img-box">
                                    <img src="../<?php echo htmlspecialchars($producto['imagen_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                         onerror="this.src='https://via.placeholder.com/300x300?text=Sin+Imagen'">
                                </div>
                                <div class="prod-info">
                                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                    <p style="font-size: 0.85em; color: #666; margin-bottom: 5px;">
                                        Ref: <?php echo htmlspecialchars($producto['referencia']); ?>
                                    </p>

                                    <p class="precio-label">Precio desde</p>
                                    
                                    <div class="prod-actions">
                                        <span class="precio-box"><?php echo number_format($producto['precio'], 2); ?>€</span>
                                        <a href="infoProducto.php?id=<?php echo $producto['id']; ?>" class="btn-detalles">Ver Opciones</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align:center; width:100%;">No hay productos disponibles.</p>
                <?php endif; ?>

            </div>
            
            <p id="msg-no-results" style="display:none; text-align:center; width:100%; font-size:18px; color:#666;">No hay productos con los filtros seleccionados.</p>

            <div class="ver-mas-container">
                <button id="btn-cargar-mas" class="btn-ver-mas">Ver más</button>
            </div>

        </main>

        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-logo-section">
                        <div class="logo-footer"><img src="../imagenes/footer.png" alt="Logo Metalful"></div>
                        <div class="redes">
                            <a href="https://www.instagram.com/metalfulsansl/" target="_blank" class="instagram-link">
                                <svg viewBox="0 0 24 24" fill="white"><path d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z"/></svg>
                            </a>
                        </div>
                    </div>
                    <div class="footer-links">
                        <div class="enlaces-rapidos">
                            <h3>Enlaces rápidos</h3>
                            <ul>
                                <li><a href="conocenos.php">Conócenos</a></li>
                                <li><a href="productos.php">Productos</a></li>
                                <li><a href="IniciarSesion.php">Iniciar Sesión</a></li>
                            </ul>
                        </div>
                        <div class="contacto-footer">
                            <h3>Contacto</h3>
                            <ul>
                                <li><svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg><a href="#">Granada, España</a></li>
                                <li><svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg><a href="tel:652921960">652 921 960</a></li>
                                <li><svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg><a href="mailto:metalfulsan@gmail.com">metalfulsan@gmail.com</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom">
                    <div class="politica-legal">
                        <a href="#">Aviso Legal</a><span>•</span><a href="#">Política de Privacidad</a><span>•</span><a href="#">Política de Cookies</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <script src="../js/auth.js"></script>

    <script>
        let filtrosActivos = [];
        let iniciales = 9; 
        let porCarga = 3;
        let visiblesActuales = iniciales;

        document.addEventListener("DOMContentLoaded", function () {
            const btnCargar = document.getElementById('btn-cargar-mas');
            actualizarVista(); 

            if (btnCargar) {
                btnCargar.addEventListener('click', function(e) {
                    e.preventDefault();
                    visiblesActuales += porCarga;
                    actualizarVista();
                });
            }
        });

        function filtrar(categoria) {
            if (categoria === 'todos') {
                filtrosActivos = [];
                document.querySelectorAll('.cat-card').forEach(card => card.classList.remove('active'));
            } else {
                if (filtrosActivos.includes(categoria)) {
                    filtrosActivos = filtrosActivos.filter(c => c !== categoria);
                    document.getElementById('cat-' + categoria).classList.remove('active');
                } else {
                    filtrosActivos.push(categoria);
                    document.getElementById('cat-' + categoria).classList.add('active');
                }
            }
            visiblesActuales = iniciales;
            actualizarVista();
        }

        function actualizarVista() {
            const productos = document.querySelectorAll('.item-producto');
            const btnCargar = document.getElementById('btn-cargar-mas');
            const msgNoResults = document.getElementById('msg-no-results');

            let totalCoincidencias = 0; 
            let mostradosAhora = 0; 

            productos.forEach(prod => {
                const catProd = prod.getAttribute('data-categoria');
                let cumpleFiltro = false;

                if (filtrosActivos.length === 0) {
                    cumpleFiltro = true;
                } else {
                    for (let filtro of filtrosActivos) {
                        if (filtro === 'otros') {
                            if (['3', '4', '6'].includes(catProd)) {
                                cumpleFiltro = true;
                                break;
                            }
                        } else {
                            if (catProd === filtro) {
                                cumpleFiltro = true;
                                break;
                            }
                        }
                    }
                }

                if (cumpleFiltro) {
                    totalCoincidencias++;
                    if (mostradosAhora < visiblesActuales) {
                        prod.style.display = 'flex'; 
                        mostradosAhora++;
                    } else {
                        prod.style.display = 'none'; 
                    }
                } else {
                    prod.style.display = 'none';
                }
            });

            if (btnCargar) {
                if (mostradosAhora < totalCoincidencias) {
                    btnCargar.style.display = 'inline-block';
                } else {
                    btnCargar.style.display = 'none';
                }
            }

            if (totalCoincidencias === 0) {
                msgNoResults.style.display = 'block';
            } else {
                msgNoResults.style.display = 'none';
            }
        }
    </script>
</body>
</html>