<?php
include 'conexion.php';

// 1. VERIFICAR ID
if (!isset($_GET['id'])) {
    header("Location: ListadoClientesAdmin.php");
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
    $password = $_POST['contrasena'];
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
                    password=?, 
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
            $password, 
            $dni, 
            $telefono, 
            $calle,   
            $numero,  
            $piso,    
            $cp, 
            $ciudad, 
            $id
        ]);
        
        $mensaje = "¡Cliente actualizado correctamente!";
        
    } catch(PDOException $e) {
        $mensaje = "Error al guardar: " . $e->getMessage();
    }
}

// 3. OBTENER DATOS DEL CLIENTE
$stmt = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    echo "Cliente no encontrado";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Cliente - Metalistería Fulsán</title>
    <link rel="stylesheet" href="stylesModificarDatosCliente.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Estilos necesarios para que el script JS muestre los errores visualmente -->
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

        <div class="titulo-section">
            <div class="degradado"></div>
            <div class="recuadro-fondo-titulo"></div> 
            <a href="ListadoClientesAdmin.php" class="flecha-circular">&#8592;</a>
            
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

                <form class="formulario-cliente" method="POST">
                    
                    <div class="form-group">
                        <label class="label-icon"><i class="far fa-user"></i> Nombre:</label>
                        <input type="text" id="nombre" class="input-display" name="nombre" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required />
                    </div>

                    <div class="form-group">
                        <label class="label-icon"><i class="far fa-user"></i> Apellidos:</label>
                        <input type="text" id="apellidos" class="input-display" name="apellidos" value="<?php echo htmlspecialchars($cliente['apellidos']); ?>" required />
                    </div>

                    <div class="form-group">
                        <label class="label-icon"><i class="far fa-envelope"></i> Correo electrónico:</label>
                        <input type="email" id="correo" class="input-display" name="correo" value="<?php echo htmlspecialchars($cliente['email']); ?>" required />
                    </div>

                    <div class="form-group">
                        <label class="label-icon"><i class="fas fa-user-lock"></i> Contraseña:</label>
                        <input type="text" id="contrasena" class="input-display" name="contrasena" value="<?php echo htmlspecialchars($cliente['password']); ?>" />
                    </div>

                    <div class="form-group">
                        <label class="label-icon"><i class="far fa-id-card"></i> DNI/NIF/NIE:</label>
                        <!-- El script busca input[name="dni"] -->
                        <input type="text" id="dni" class="input-display" name="dni" value="<?php echo isset($cliente['dni']) ? htmlspecialchars($cliente['dni']) : ''; ?>" placeholder="Ej: 12345678Z" />
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

                    <!-- Botones -->
                    <div class="botones-finales full-width" style="grid-column: span 2;">
                        <div class="boton-salir">
                            <a href="ListadoClientesAdmin.php">Salir</a>
                        </div>
                        
                        <div class="boton-modificar">
                            <button type="submit" name="actualizar">Modificar cliente</button>
                        </div>
                    </div>

                </form>
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
                        <a href="#">Aviso Legal</a> • <a href="#">Privacidad</a> • <a href="#">Cookies</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Cargamos tu script de validación -->
    <script src="AlgoritmoDNIs.js"></script>

</body>
</html>