<?php
// 1. IMPORTANTE: Primero incluimos el archivo de funciones (que inicia la sesión)
// Asegúrate de que el nombre del archivo es correcto (funciones.php o CabeceraFooter.php)
include '../CabeceraFooter.php'; 

// 2. Luego la conexión a la base de datos
include '../conexion.php'; 

// 3. LÓGICA DE PRODUCTOS
try {
    // Tu consulta para sacar un producto único por referencia
    $sql = "SELECT * FROM productos WHERE id IN (SELECT MIN(id) FROM productos GROUP BY referencia)";
    $stmt = $conn->query($sql);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al cargar productos: " . $e->getMessage();
    die();
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
        .item-producto { display: none; }
        .cat-card.active .cat-frame {
            border: 3px solid #a0d2ac;
            box-shadow: 0 0 15px rgba(160, 210, 172, 0.6);
            transform: translateY(-5px);
        }
        .cat-card.active .cat-name { color: #a0d2ac; }
        
        /* Efecto visual suave cuando volvemos del carrito */
        @keyframes highlight {
            0% { transform: scale(1); box-shadow: 0 0 0 rgba(0,0,0,0); }
            50% { transform: scale(1.02); box-shadow: 0 0 20px rgba(160, 210, 172, 0.8); }
            100% { transform: scale(1); box-shadow: 0 0 0 rgba(0,0,0,0); }
        }
        .producto-destacado {
            animation: highlight 1.5s ease-in-out;
            border: 2px solid #a0d2ac;
        }
    </style>
</head>
<body>
    <div class="visitante-productos">
        
        <?php sectionheader(3); ?>

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
                <h2>CREA TU PRODUCTO A MEDIDA</h2>
                <p>Selecciona un producto para personalizarlo a tu gusto</p>
            </div>

            <div class="productos-grid" id="lista-productos">
                
                <?php if (count($productos) > 0): ?>
                    <?php foreach ($productos as $producto): ?>
                        
                        <div class="prod-card-outer item-producto" 
                             id="producto-<?php echo $producto['id']; ?>" 
                             data-categoria="<?php echo $producto['id_categoria']; ?>">
                             
                            <div class="prod-card-inner">
                                <div class="prod-img-box">
                                    <a href="infoProducto.php?id=<?php echo $producto['id']; ?>">
                                        <img src="../<?php echo htmlspecialchars($producto['imagen_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                             onerror="this.src='https://via.placeholder.com/300x300?text=Sin+Imagen'">
                                    </a>
                                </div>
                                <div class="prod-info">
                                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                    
                                    <div class="prod-actions">
                                        <span class="precio-box"><?php echo number_format($producto['precio'], 2); ?>€</span>
                                        <a href="infoProducto.php?id=<?php echo $producto['id']; ?>" class="btn-detalles">Ver Detalles</a>
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

        <?php sectionfooter(); ?>
    </div>
    
    <script src="../js/auth.js"></script>

    <script>
        let filtrosActivos = [];
        let iniciales = 9; 
        let porCarga = 6;
        let visiblesActuales = iniciales;

        document.addEventListener("DOMContentLoaded", function () {
            const btnCargar = document.getElementById('btn-cargar-mas');
            
            // --- NUEVO: DETECTAR RETORNO DE CARRITO ---
            // Si la URL tiene #producto-123, calculamos dónde está para mostrarlo
            const hash = window.location.hash;
            if (hash) {
                // Quitamos el # para buscar por ID
                const targetElement = document.querySelector(hash);
                
                if (targetElement) {
                    // Calculamos en qué posición está el producto
                    const todosLosProductos = Array.from(document.querySelectorAll('.item-producto'));
                    const index = todosLosProductos.indexOf(targetElement);
                    
                    // Si el producto está oculto (índice mayor que visibles), ampliamos la lista
                    if (index >= visiblesActuales) {
                        visiblesActuales = index + 1; // Mostramos hasta ese producto
                        // Redondeamos para que siga viéndose ordenado
                        visiblesActuales = Math.ceil(visiblesActuales / 3) * 3; 
                    }
                    
                    // Le añadimos un efecto visual bonito para saber cuál hemos comprado
                    targetElement.querySelector('.prod-card-inner').classList.add('producto-destacado');
                }
            }
            // ----------------------------------------------

            actualizarVista(); 

            // Hacemos el scroll manual si hay hash, después de actualizar la vista
            if (hash) {
                const targetElement = document.querySelector(hash);
                if(targetElement && targetElement.style.display !== 'none') {
                    setTimeout(() => {
                        targetElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 100);
                }
            }

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
            // Al filtrar reseteamos la vista
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
                                cumpleFiltro = true; break;
                            }
                        } else {
                            if (catProd === filtro) {
                                cumpleFiltro = true; break;
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
                btnCargar.style.display = (mostradosAhora < totalCoincidencias) ? 'inline-block' : 'none';
            }

            msgNoResults.style.display = (totalCoincidencias === 0) ? 'block' : 'none';
        }
    </script>
</body>
</html>