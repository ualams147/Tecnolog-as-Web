<?php
// 1. PRIMERO LA CABECERA (Inicia la sesión automáticamente)
include 'CabeceraFooter.php'; 

include 'conexion.php'; 

// 1. SEGURIDAD: Si no estás logueado, fuera.
// (Esto funciona bien porque en el Login guardamos 'usuario_id')
if (!isset($_SESSION['usuario_id'])) {
    header("Location: IniciarSesion.php");
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$error = '';

// 2. PROCESAR FORMULARIO (Cuando le das a "Guardar Cambios")
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recogemos los datos de los inputs
    $calle = $_POST['calle'];
    $numero = $_POST['numero'];
    $piso = $_POST['piso'];
    $codigo_postal = $_POST['codigo_postal'];
    $ciudad = $_POST['ciudad']; // "Localidad" en el formulario

    try {
        // Actualizamos la tabla clientes
        $sql = "UPDATE clientes SET 
                direccion = :direccion, 
                numero = :numero, 
                piso = :piso, 
                codigo_postal = :codigo_postal, 
                ciudad = :ciudad 
                WHERE id = :id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':direccion' => $calle,
            ':numero' => $numero,
            ':piso' => $piso,
            ':codigo_postal' => $codigo_postal,
            ':ciudad' => $ciudad,
            ':id' => $id_usuario
        ]);

        // Si sale bien, volvemos al perfil
        header("Location: perfil.php");
        exit;

    } catch (PDOException $e) {
        $error = "Error al actualizar: " . $e->getMessage();
    }
}

// 3. RECUPERAR DATOS ACTUALES PARA MOSTRARLOS
// Necesitamos que los inputs tengan valor al entrar
$stmt = $conn->prepare("SELECT * FROM clientes WHERE id = :id");
$stmt->execute([':id' => $id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: admin/CerrarSesion.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Domicilio - Metalistería Fulsan</title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/editarDomicilio.css">
    <!-- MANTENEMOS auth.js -->
    <script src="js/auth.js"></script>
</head>
<body>
    <div class="visitante-domicilio-edit">
        
        
        <?php sectionheader(6); ?>

        <section class="address-hero">
            <div class="container hero-content">
                <a href="perfil.php" class="btn-back">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </a>
                <h1 class="address-title-main">Mi Domicilio</h1>
            </div>
        </section>

        <main class="address-main container">
            <div class="address-card-container">
                
                <h2 class="section-title">Editar Dirección de Envío</h2>
                
                <?php if(!empty($error)): ?>
                    <div style="background-color:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:15px; text-align:center;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <!-- IMPORTANTE: El formulario debe envolver los inputs y el botón Guardar -->
                <form method="POST" action="">
                    <div class="blue-form-container">
                        
                        <div class="blue-form-row">
                            <label>Calle:</label>
                            <!-- Rellenamos value con PHP -->
                            <input type="text" name="calle" value="<?php echo htmlspecialchars($usuario['direccion']); ?>" class="blue-input" required>
                        </div>

                        <div class="blue-form-row split">
                            <div class="blue-group">
                                <label>Número:</label>
                                <input type="text" name="numero" value="<?php echo htmlspecialchars($usuario['numero']); ?>" class="blue-input short" required>
                            </div>
                            <div class="blue-group">
                                <label>Piso/Puerta:</label>
                                <input type="text" name="piso" value="<?php echo htmlspecialchars($usuario['piso']); ?>" class="blue-input short">
                            </div>
                        </div>

                        <div class="blue-form-row split">
                            <div class="blue-group">
                                <label>C. Postal:</label>
                                <input type="text" name="codigo_postal" value="<?php echo htmlspecialchars($usuario['codigo_postal']); ?>" class="blue-input short" required>
                            </div>
                            <div class="blue-group">
                                <label>Localidad:</label>
                                <input type="text" name="ciudad" value="<?php echo htmlspecialchars($usuario['ciudad']); ?>" class="blue-input" required>
                            </div>
                        </div>

                    </div>

                    <div class="page-actions">
                        <a href="perfil.php" class="btn-round-white">Salir sin guardar</a>
                        <!-- El botón debe ser type="submit" para enviar el formulario -->
                        <button type="submit" class="btn-round-white">Guardar Cambios</button>
                    </div>
                </form>

            </div>
        </main>

        <?php sectionfooter(); ?>
    </div>
</body>
</html>