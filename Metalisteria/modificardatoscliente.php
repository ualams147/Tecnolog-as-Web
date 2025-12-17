<?php
include 'conexion.php';

// 1. VERIFICAR ID
if (!isset($_GET['id'])) {
    header("Location: listadoclientesadmin.php");
    exit;
}

$id = $_GET['id'];
$mensaje = "";

// 2. PROCESAR FORMULARIO (GUARDAR CAMBIOS)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recogemos datos personales
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $email = $_POST['correo'];
    $dni = $_POST['dni'];
    $telefono = $_POST['telefono'];
    
    // Recogemos dirección DESGLOSADA
    $calle = $_POST['calle'];     
    $numero = $_POST['numero'];   
    $piso = $_POST['piso'];        
    
    $cp = $_POST['cp'];
    $ciudad = $_POST['poblacion'];

    try {
        // SQL: Guardamos numero y piso en sus columnas
        $sql = "UPDATE clientes SET 
                    nombre=?, 
                    apellidos=?, 
                    email=?, 
                    dni=?, 
                    telefono=?, 
                    direccion=?, 
                    numero=?, 
                    piso=?, 
                    codigo_postal=?, 
                    ciudad=? 
                WHERE id=?";
                
        $stmt = $conn->prepare($sql);
        
        $stmt->execute([
            $nombre, 
            $apellidos, 
            $email, 
            $dni, 
            $telefono, 
            $calle,    
            $numero,  
            $piso,    
            $cp, 
            $ciudad, 
            $id
        ]);
        
        // --- CAMBIO CLAVE AQUÍ: REDIRECCIÓN DIRECTA COMO EN PRODUCTOS ---
        header("Location: listadoclientesadmin.php"); 
        exit;
        
    } catch(PDOException $e) {
        $mensaje = "Error al guardar: " . $e->getMessage();
    }
}

// 3. OBTENER LOS DATOS ACTUALES DEL CLIENTE (¡Esto faltaba en tu código pegado!)
// Es vital para rellenar el formulario al cargar la página
$sql = "SELECT * FROM clientes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC); // Guardamos en $cliente para usarlo abajo

if (!$cliente) {
    echo "Cliente no encontrado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Cliente - Metalistería Fulsán</title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="stylesheet" href="css/administrador.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .boton-modificar button {
            background: none; border: none; color: white; font-family: inherit; font-size: inherit; font-weight: inherit; cursor: pointer; width: 100%; height: 100%;
        }
        .details-card { min-height: auto; } 
        
        /* Clases que usa tu script AlgoritmoDNIs.js */
        .input-error { border: 2px solid #e74c3c !important; background-color: #fceceb; }
        .input-success { border: 2px solid #2ecc71 !important; background-color: #eafaf1; }
        .msg-error { color: #e74c3c; font-size: 13px; margin-top: 5px; display: block; font-weight: 600; }
    </style>
</head>
<body>
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
                    <a href="listadoventasadmin.php">Ventas</a>
                    <a href="listadoproductosadmin.php">Productos</a>
                    <a href="listadoclientesadmin.php" style="font-weight:bold; border-bottom: 2px solid currentColor;">Clientes</a> 
                </nav>

                <div class="log-out">
                    <a href="index.php">Cerrar Sesión</a>
                </div>

            </div>
    </header>

        <div class="titulo-section">
            <div class="degradado"></div>
            <div class="recuadro-fondo"></div> 
            <a href="listadoclientesadmin.php" class="flecha-circular">&#8592;</a>
            
            <h1 class="titulo-principal"><?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']); ?></h1>
        </div>

        <div class="container main-container">
            
            <?php if($mensaje): ?>
                <div class="details-card" style="padding: 20px; margin-bottom: 20px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            
            <div class="details-card">
                
                <h2 class="form-section-title">Datos Personales</h2>

                <form id="form-modificar" class="formulario-cliente" method="POST">
                    
                    <div class="form-group">
                        <label class="label-icon"><i class="far fa-user"></i> Nombre:</label>
                        <input type="text" id="nombre" class="input-display" name="nombre" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required />
                    </div>

                    <div class="form-group">
                        <label class="label-icon"><i class="far fa-user"></i> Apellidos:</label>
                        <input type="text" id="apellidos" class="input-display" name="apellidos" value="<?php echo htmlspecialchars($cliente['apellidos']); ?>" required />
                    </div>

                    <div class="form-group full-width">
                        <label class="label-icon"><i class="far fa-envelope"></i> Correo electrónico:</label>
                        <input type="email" id="correo" class="input-display" name="correo" value="<?php echo htmlspecialchars($cliente['email']); ?>" required />
                    </div>

                    <div class="form-group">
                        <label class="label-icon"><i class="far fa-id-card"></i> DNI/NIF/NIE:</label>
                        <input type="text" id="dni" class="input-display" name="dni" value="<?php echo isset($cliente['dni']) ? htmlspecialchars($cliente['dni']) : ''; ?>" placeholder="Ej: 12345678Z" required />
                    </div>

                    <div class="form-group">
                        <label class="label-icon"><i class="fas fa-phone-alt"></i> Teléfono:</label>
                        <input type="tel" id="telefono" class="input-display" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono']); ?>" />
                    </div>

                    <div class="separator-line full-width"></div>
                    <h2 class="form-section-title full-width">Dirección del domicilio</h2>

                    <div class="form-group full-width">
                        <label for="calle">Calle:</label>
                        <input type="text" id="calle" class="input-display" name="calle" value="<?php echo htmlspecialchars($cliente['direccion']); ?>" />
                    </div>
                    
                    <div class="form-group">
                        <label for="numero">Número:</label>
                        <input type="text" id="numero" class="input-display" name="numero" value="<?php echo isset($cliente['numero']) ? htmlspecialchars($cliente['numero']) : ''; ?>" placeholder="Ej: 12" />
                    </div>

                    <div class="form-group">
                        <label for="piso">Piso / Puerta:</label>
                        <input type="text" id="piso" class="input-display" name="piso" value="<?php echo isset($cliente['piso']) ? htmlspecialchars($cliente['piso']) : ''; ?>" placeholder="Ej: 3º A" />
                    </div>

                    <div class="form-group">
                        <label for="cp">Código Postal:</label>
                        <input type="text" id="cp" class="input-display" name="cp" value="<?php echo htmlspecialchars($cliente['codigo_postal']); ?>" />
                    </div>

                    <div class="form-group">
                        <label for="poblacion">Población / Ciudad:</label>
                        <input type="text" id="poblacion" class="input-display" name="poblacion" value="<?php echo htmlspecialchars($cliente['ciudad']); ?>" />
                    </div>

                    <div class="form-group">
                        <label for="provincia">Provincia:</label>
                        <input type="text" id="provincia" class="input-display" name="provincia" placeholder="Granada" />
                    </div>

                    <div class="botones-finales full-width" style="grid-column: span 2;">
                        <div class="boton-salir">
                            <a href="javascript:void(0);" onclick="confirmarSalida()">Salir</a>
                        </div>
                        
                        <div class="boton-modificar">
                            <button type="button" name="actualizar" onclick="confirmarModificacion()">
                                Guardar cambios
                            </button>
                        </div>
                    </div>

                </form>
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
                            <a href="#" class="instagram-link">
                                <svg viewBox="0 0 24 24" fill="white"><path d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z"/></svg>
                            </a>
                        </div>
                    </div>

                    <div class="footer-links">
                        <div class="contacto-footer">
                            <h3>Contacto</h3>
                            <div class="contacto-item">
                                <span>Extrarradio Cortijo la Purisima, 2P, 18004 Granada</span>
                            </div>
                            <div class="contacto-item">
                                <span>652 921 960</span>
                            </div>
                            <div class="contacto-item">
                                <span>metalfulsan@gmail.com</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="footer-bottom">
                    <div class="politica-legal">
                        <a href="#aviso-legal">Aviso Legal</a> • <a href="#privacidad">Privacidad</a> • <a href="#cookies">Cookies</a>
                    </div>
                </div>
            </div>
        </footer>
    
    <script src="js/AlgoritmoDNIs.js"></script>

    <script>
        function confirmarModificacion() {
            const formulario = document.getElementById('form-modificar');
            const inputDNI = document.getElementById('dni');

            // 1. Validar campos requeridos básicos (HTML5)
            if (!formulario.checkValidity()) {
                formulario.reportValidity();
                return; 
            }

            // 2. VALIDACIÓN ESTRICTA DEL DNI
            // Usamos la función global que tienes en 'js/AlgoritmoDNIs.js'
            if (typeof validarDocumento === 'function') {
                const esDniValido = validarDocumento(inputDNI);
                
                if (!esDniValido) {
                    // Si el DNI está mal, mostramos alerta y PARAMOS todo
                    Swal.fire({
                        icon: 'error',
                        title: 'Documento no válido',
                        text: 'El DNI, NIE o CIF introducido no es correcto. Por favor revísalo.',
                        confirmButtonColor: '#293661'
                    });
                    
                    // Ponemos el foco en el input para que lo corrija
                    inputDNI.focus();
                    return; // Importante: Esto evita que siga hacia abajo y guarde
                }
            }

            // 3. Si todo está bien, pedimos confirmación
            Swal.fire({
                title: '¿Guardar cambios?',
                text: "¿Estás seguro de que quieres actualizar los datos de este cliente?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#293661',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, guardar cambios',
                cancelButtonText: 'No, seguir editando',
                customClass: {
                    popup: 'swal2-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    formulario.submit();
                }
            });
        }

        function confirmarSalida() {
            Swal.fire({
                title: '¿Salir sin guardar?',
                text: "Se perderán los cambios que no hayas guardado.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#293661',
                confirmButtonText: 'Sí, salir',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'swal2-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'listadoclientesadmin.php';
                }
            });
        }

        function confirmarSalida() {
            Swal.fire({
                title: '¿Salir sin guardar?',
                text: "Se perderán los cambios que no hayas guardado.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#293661',
                confirmButtonText: 'Sí, salir',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'swal2-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'listadoclientesadmin.php';
                }
            });
        }
    </script>
</body>
</html>