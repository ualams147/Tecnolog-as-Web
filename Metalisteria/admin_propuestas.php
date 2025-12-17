<?php
include 'conexion.php';
require 'seguridad_admin.php'; 

// --- CONTROL DE SESIÓN ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar Idioma
$lang = [];
if(isset($_SESSION['idioma']) && file_exists("idiomas/" . $_SESSION['idioma'] . ".php")) {
    include "idiomas/" . $_SESSION['idioma'] . ".php";
} else {
    include "idiomas/es.php";
}

// --- LÓGICA: PROCESAR ACCIONES ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. APROBAR (Poner Precio)
    if (isset($_POST['accion']) && $_POST['accion'] === 'aprobar') {
        $id_propuesta = $_POST['id_propuesta'];
        $precio = $_POST['precio'];
        
        try {
            $stmt = $conn->prepare("UPDATE carrito_personalizados SET estado = 'aprobado', precio_final = ? WHERE id = ?");
            $stmt->execute([$precio, $id_propuesta]);
            header("Location: admin_propuestas.php?status=success");
            exit;
        } catch (PDOException $e) { }
    }

    // 2. RECHAZAR
    if (isset($_POST['accion']) && $_POST['accion'] === 'rechazar') {
        $id_propuesta = $_POST['id_propuesta'];
        try {
            $stmt = $conn->prepare("UPDATE carrito_personalizados SET estado = 'rechazado' WHERE id = ?");
            $stmt->execute([$id_propuesta]);
            header("Location: admin_propuestas.php?status=rejected");
            exit;
        } catch (PDOException $e) { }
    }
}

// --- CONSULTA: OBTENER PROPUESTAS PENDIENTES ---
$sql = "SELECT cp.id as id_propuesta, cp.medidas, cp.color as color_pers, cp.detalles, 
               p.nombre as nombre_prod, p.imagen_url, p.referencia,
               c.nombre as nombre_cli, c.apellidos as apellidos_cli, c.email
        FROM carrito_personalizados cp
        JOIN carrito car ON cp.carrito_id = car.id
        JOIN productos p ON car.producto_id = p.id
        JOIN clientes c ON car.cliente_id = c.id
        WHERE cp.estado = 'pendiente'
        ORDER BY car.fecha_agregado ASC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$propuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_propuestas = count($propuestas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($lang['propuestas_titulo']) ? $lang['propuestas_titulo'] : 'Gestión de Propuestas'; ?> - Metalful</title>
    
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/administrador.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* AJUSTE: Superposición sobre el azul */
        .titulo-section {
            padding-bottom: 150px; 
            margin-bottom: 0;      
        }

        .main-container-propuestas {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;        
            margin-top: -120px;    /* Sube sobre el fondo azul */
            position: relative;    
            z-index: 10;           
            padding-bottom: 60px;  
        }

        .cuadro-fondo {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            border: 1px solid #eee;
            padding: 40px;
            min-height: 400px;
        }

        /* --- ESTILOS TARJETA --- */
        .header-tabla {
            font-size: 1.5rem;
            font-weight: 700;
            color: #293661;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }

        .card-propuesta {
            background: white;
            border: 1px solid #e0e0e0;
            border-left: 5px solid #eeca00;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            display: flex;
            gap: 30px;
            align-items: flex-start;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        
        .card-propuesta:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        }

        .img-producto-base {
            width: 120px;
            height: 120px;
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid #f0f0f0;
            padding: 5px;
            background: #fff;
        }

        .info-columna {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px 30px;
        }

        .info-item { font-size: 15px; color: #555; }
        .info-item strong { color: #293661; font-weight: 600; display: block; margin-bottom: 3px; }
        
        .detalles-box {
            grid-column: 1 / -1;
            background-color: #f9f9f9;
            padding: 12px 15px;
            border-radius: 6px;
            border-left: 3px solid #ccc;
            font-size: 14px;
            color: #444;
            margin-top: 10px;
        }

        .acciones-columna {
            display: flex;
            flex-direction: column;
            gap: 12px;
            justify-content: center;
            min-width: 180px;
            border-left: 1px solid #eee;
            padding-left: 25px;
        }

        .btn-accion {
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            text-align: center;
            transition: 0.3s;
            border: none;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-aprobar {
            background-color: #293661;
            color: white;
            box-shadow: 0 4px 10px rgba(41, 54, 97, 0.2);
        }
        .btn-aprobar:hover { background-color: #1a2442; transform: scale(1.05); }

        .btn-rechazar {
            background-color: white;
            border: 2px solid #e74c3c;
            color: #e74c3c;
        }
        .btn-rechazar:hover { background-color: #e74c3c; color: white; }

        .empty-propuestas {
            text-align: center;
            padding: 80px 20px;
            color: #888;
        }
        .empty-propuestas i { font-size: 60px; color: #ddd; margin-bottom: 20px; }

        /* Responsive */
        @media (max-width: 900px) {
            .card-propuesta { flex-direction: column; align-items: center; text-align: center; }
            .acciones-columna { border-left: none; padding-left: 0; width: 100%; border-top: 1px solid #eee; padding-top: 20px; flex-direction: row; }
            .info-columna { grid-template-columns: 1fr; text-align: left; width: 100%; }
            .main-container-propuestas { width: 95%; margin-top: -80px; }
        }
    </style>
</head>
<body>
    <div class="ListadoVentasAdmin">
        
        <header class="cabecera">
            <div class="container">
                <div class="logo-main">
                    <a href="indexadmin.php" class="logo-main">
                        <img src="imagenes/logo.png" alt="Logo Metalful">
                        <div class="logo-text"><span>Metalisteria</span><strong>Fulsan</strong></div>
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
            <h1 class="titulo-principal"><?php echo isset($lang['propuestas_titulo']) ? $lang['propuestas_titulo'] : 'Gestión de Propuestas'; ?></h1>
        </div>

        <div class="main-container-propuestas">
            <div class="cuadro-fondo">
                <p class="header-tabla">
                    <?php echo isset($lang['propuestas_subtitulo']) ? $lang['propuestas_subtitulo'] : 'Solicitudes Pendientes'; ?>
                </p>

                <?php if ($total_propuestas == 0): ?>
                    <div class="empty-propuestas">
                        <i class="fas fa-clipboard-check"></i>
                        <h3>¡Todo al día!</h3>
                        <p><?php echo isset($lang['propuestas_vacio']) ? $lang['propuestas_vacio'] : 'No hay propuestas pendientes de valoración.'; ?></p>
                    </div>
                <?php else: ?>

                    <?php foreach ($propuestas as $p): ?>
                        <?php 
                            $img = !empty($p['imagen_url']) ? str_replace('../', '', $p['imagen_url']) : 'imagenes/producto-sin-imagen.png';
                        ?>
                        
                        <div class="card-propuesta">
                            <img src="<?php echo htmlspecialchars($img); ?>" class="img-producto-base" alt="Producto">

                            <div class="info-columna">
                                <div class="info-item">
                                    <strong><?php echo isset($lang['propuestas_cliente']) ? $lang['propuestas_cliente'] : 'Cliente'; ?></strong>
                                    <?php echo htmlspecialchars($p['nombre_cli'] . ' ' . $p['apellidos_cli']); ?>
                                    <br><small style="color:#888"><?php echo htmlspecialchars($p['email']); ?></small>
                                </div>
                                
                                <div class="info-item">
                                    <strong><?php echo isset($lang['propuestas_producto']) ? $lang['propuestas_producto'] : 'Producto Base'; ?></strong>
                                    <?php echo htmlspecialchars($p['nombre_prod']); ?> 
                                    <small>(Ref: <?php echo htmlspecialchars($p['referencia']); ?>)</small>
                                </div>

                                <div class="info-item">
                                    <strong><?php echo isset($lang['propuestas_medidas']) ? $lang['propuestas_medidas'] : 'Medidas'; ?></strong>
                                    <span style="font-size:1.1em; color:#293661; font-weight:bold;"><?php echo htmlspecialchars($p['medidas']); ?></span>
                                </div>

                                <div class="info-item">
                                    <strong><?php echo isset($lang['propuestas_color']) ? $lang['propuestas_color'] : 'Color'; ?></strong>
                                    <?php echo htmlspecialchars($p['color_pers']); ?>
                                </div>

                                <?php if (!empty($p['detalles'])): ?>
                                    <div class="detalles-box">
                                        <strong>Nota del cliente:</strong> 
                                        "<?php echo htmlspecialchars($p['detalles']); ?>"
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="acciones-columna">
                                <button onclick="valorarPropuesta(<?php echo $p['id_propuesta']; ?>)" class="btn-accion btn-aprobar">
                                    <i class="fas fa-euro-sign"></i> 
                                    <?php echo isset($lang['propuestas_btn_valorar']) ? $lang['propuestas_btn_valorar'] : 'Valorar'; ?>
                                </button>
                                
                                <button onclick="rechazarPropuesta(<?php echo $p['id_propuesta']; ?>)" class="btn-accion btn-rechazar">
                                    <i class="fas fa-times"></i> 
                                    <?php echo isset($lang['propuestas_btn_rechazar']) ? $lang['propuestas_btn_rechazar'] : 'Rechazar'; ?>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>

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
        function valorarPropuesta(id) {
            Swal.fire({
                title: '<?php echo isset($lang['propuestas_swal_titulo']) ? $lang['propuestas_swal_titulo'] : 'Valorar Propuesta'; ?>',
                text: '<?php echo isset($lang['propuestas_swal_texto']) ? $lang['propuestas_swal_texto'] : 'Introduce el precio final (IVA incluido):'; ?>',
                input: 'number',
                inputAttributes: { min: '0.01', step: '0.01', placeholder: 'Ej: 150.00' },
                showCancelButton: true,
                confirmButtonText: 'Guardar y Aprobar',
                confirmButtonColor: '#293661',
                cancelButtonColor: '#d33',
                preConfirm: (precio) => {
                    if (!precio || precio <= 0) {
                        Swal.showValidationMessage('<?php echo isset($lang['propuestas_swal_error']) ? $lang['propuestas_swal_error'] : 'Precio inválido'; ?>');
                    }
                    return precio;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    enviarFormulario('aprobar', id, result.value);
                }
            });
        }

        function rechazarPropuesta(id) {
            Swal.fire({
                title: '¿Rechazar propuesta?',
                text: "El cliente verá el rechazo en su carrito.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#293661',
                confirmButtonText: 'Sí, rechazar'
            }).then((result) => {
                if (result.isConfirmed) {
                    enviarFormulario('rechazar', id, null);
                }
            });
        }

        function enviarFormulario(accion, id, precio) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'admin_propuestas.php';

            const inputAccion = document.createElement('input');
            inputAccion.type = 'hidden';
            inputAccion.name = 'accion';
            inputAccion.value = accion;
            form.appendChild(inputAccion);

            const inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'id_propuesta';
            inputId.value = id;
            form.appendChild(inputId);

            if (precio !== null) {
                const inputPrecio = document.createElement('input');
                inputPrecio.type = 'hidden';
                inputPrecio.name = 'precio';
                inputPrecio.value = precio;
                form.appendChild(inputPrecio);
            }

            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>