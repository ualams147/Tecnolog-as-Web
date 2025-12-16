<?php
// 1. INICIO DE SESIÓN Y BUFFER
session_start();
ob_start(); 

include 'conexion.php'; 
include 'CabeceraFooter.php'; // Incluimos funciones de cabecera

// 2. SEGURIDAD DE SESIÓN
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['usuario_id'];
$mensaje = "";

// =======================================================================
// BLOQUE 1 Y 2: AJAX (CONTRASEÑA) - SE MANTIENE IGUAL
// =======================================================================
if (isset($_POST['ajax_verificar']) || isset($_POST['ajax_guardar_password'])) {
    ob_clean(); // Limpiar cualquier HTML previo (incluida la cabecera si se coló)
    header('Content-Type: application/json');
    $response = ['status' => 'error', 'message' => 'Error desconocido'];

    try {
        if (isset($_POST['ajax_verificar'])) {
            $pass_check = $_POST['password_check'] ?? '';
            $stmt = $conn->prepare("SELECT password FROM clientes WHERE id = ?");
            $stmt->execute([$id_user]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $es_correcta = false;
            if ($user) {
                if (password_verify($pass_check, $user['password']) || $pass_check === $user['password']) {
                    $es_correcta = true;
                }
            }
            $response = $es_correcta ? ['status' => 'success'] : ['status' => 'error', 'message' => 'Contraseña incorrecta'];
        
        } elseif (isset($_POST['ajax_guardar_password'])) {
            $pass_nueva = $_POST['pass_nueva'] ?? '';
            $pass_confirm = $_POST['pass_confirm'] ?? '';

            if (strlen($pass_nueva) < 4) throw new Exception('La contraseña es muy corta.');
            if ($pass_nueva !== $pass_confirm) throw new Exception('Las contraseñas no coinciden.');

            $hash_nueva = password_hash($pass_nueva, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE clientes SET password = ? WHERE id = ?");
            if ($stmt->execute([$hash_nueva, $id_user])) {
                $response = ['status' => 'success'];
            } else {
                throw new Exception('Error al guardar en BD.');
            }
        }
    } catch (Exception $e) {
        $response = ['status' => 'error', 'message' => $e->getMessage()];
    }
    echo json_encode($response);
    exit;
}

// =======================================================================
// BLOQUE 3: ACTUALIZAR DATOS PERSONALES (POST NORMAL)
// =======================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['ajax_verificar']) && !isset($_POST['ajax_guardar_password'])) {
    
    // Recogemos datos usando el operador ?? '' para evitar errores si algo falta
    $nombre    = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $email     = $_POST['correo'] ?? '';
    $dni       = $_POST['dni'] ?? '';
    $telefono  = $_POST['telefono'] ?? '';
    $calle     = $_POST['calle'] ?? '';
    $numero    = $_POST['numero'] ?? '';
    $piso      = $_POST['piso'] ?? '';
    $cp        = $_POST['cp'] ?? '';
    $ciudad    = $_POST['poblacion'] ?? '';
    $provincia = $_POST['provincia'] ?? '';

    try {
        $sql = "UPDATE clientes SET 
                    nombre=?, apellidos=?, email=?, dni=?, telefono=?, 
                    direccion=?, numero=?, piso=?, codigo_postal=?, ciudad=?, provincia=?
                WHERE id=?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $nombre, $apellidos, $email, $dni, $telefono, 
            $calle, $numero, $piso, $cp, $ciudad, $provincia, 
            $id_user
        ]);
        
        // Recargar la página para ver cambios
        header("Location: perfil.php?actualizado=1"); 
        exit;
        
    } catch(Exception $e) {
        $mensaje = "Error al actualizar: " . $e->getMessage();
    }
}

// =======================================================================
// BLOQUE 4: OBTENER DATOS PARA EL FORMULARIO
// =======================================================================
$stmt = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$id_user]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    echo "Error crítico: Usuario no encontrado.";
    exit;
}

// Función auxiliar para rellenar los values sin errores
function val($dato) {
    return htmlspecialchars($dato ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Datos - Metalistería Fulsan</title>
    
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/editarinformacionperfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/auth.js"></script>
</head>
<body>
    <div class="visitante-domicilio-edit">
        
        <?php if(function_exists('sectionheader')) sectionheader(6); ?>

        <section class="address-hero">
            <div class="container hero-content">
                <a href="perfil.php" class="flecha-circular">&#8592;</a>
                <h1 class="address-title-main">Mis datos</h1>
            </div>
        </section>

        <div class="container main-container">
            
            <?php if(isset($_GET['actualizado'])): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Datos actualizados',
                            text: 'Tu perfil se ha guardado correctamente.',
                            confirmButtonColor: '#293661'
                        });
                        // Limpiar la URL
                        window.history.replaceState({}, document.title, window.location.pathname);
                    });
                </script>
            <?php endif; ?>

            <?php if($mensaje): ?>
                <div class="details-card" style="padding: 15px; margin-bottom: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            
            <div class="details-card">
                
                <h2 class="form-section-title">Datos Personales</h2>

                <form id="form-modificar" class="formulario-cliente" method="POST">
                    
                    <div class="form-group">
                        <label class="label-icon"><i class="far fa-user"></i> Nombre:</label>
                        <input type="text" id="nombre" class="input-display" name="nombre" 
                               value="<?php echo val($cliente['nombre']); ?>" required />
                    </div>

                    <div class="form-group">
                        <label class="label-icon"><i class="far fa-user"></i> Apellidos:</label>
                        <input type="text" id="apellidos" class="input-display" name="apellidos" 
                               value="<?php echo val($cliente['apellidos']); ?>" required />
                    </div>

                    <div class="form-group full-width">
                        <label class="label-icon"><i class="far fa-envelope"></i> Correo electrónico:</label>
                        <input type="email" id="correo" class="input-display" name="correo" 
                               value="<?php echo val($cliente['email']); ?>" required />
                    </div>

                    <div class="form-group">
                        <label class="label-icon"><i class="far fa-id-card"></i> DNI/NIF/NIE:</label>
                        <input type="text" id="dni" class="input-display" name="dni" 
                               value="<?php echo val($cliente['dni']); ?>" placeholder="Ej: 12345678Z" required />
                    </div>

                    <div class="form-group">
                        <label class="label-icon"><i class="fas fa-phone-alt"></i> Teléfono:</label>
                        <input type="tel" id="telefono" class="input-display" name="telefono" 
                               value="<?php echo val($cliente['telefono']); ?>" required />
                    </div>

                    <div class="full-width" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; margin-top: 30px;">
                        <button type="button" onclick="toggleSeguridad()" style="background: none; border: 2px solid #293661; color: #293661; padding: 5px 15px; border-radius: 20px; cursor: pointer; font-weight: 600; font-family: 'Poppins', sans-serif;">
                            Cambiar Contraseña 
                        </button>
                    </div>

                    <div id="contenedor-seguridad" class="full-width" style="display: none; background: #f8f9fa; padding: 20px; border-radius: 10px; border: 1px solid #e9ecef;">
                        <div id="paso-verificacion" style="display: block;">
                            <p style="margin-bottom: 15px; color: #666;">Para establecer una nueva contraseña, verifica tu identidad.</p>
                            <div class="form-group full-width">
                                <label>Contraseña actual:</label>
                                <div style="display: flex; gap: 10px;">
                                    <div style="position: relative; flex: 1;">
                                        <input type="password" id="password_actual_check" class="input-display" placeholder="Contraseña actual" style="width: 100%; padding-right: 40px;" />
                                        <i class="fas fa-eye" id="ojo_actual" onclick="mostrarOcultar('password_actual_check', 'ojo_actual')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #293661;"></i>
                                    </div>
                                    <button type="button" onclick="verificarPasswordAJAX()" style="background: #293661; color: white; border: none; padding: 0 20px; border-radius: 6px; cursor: pointer;">Comprobar</button>
                                </div>
                            </div>
                        </div>

                        <div id="paso-cambio" style="display: none;">
                            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                                <i class="fas fa-check-circle"></i> Identidad verificada.
                            </div>
                            <div class="formulario-cliente" style="padding: 0;"> 
                                <div class="form-group">
                                    <label>Nueva Contraseña:</label>
                                    <div style="position: relative;">
                                        <input type="password" id="pass_nueva_input" class="input-display" placeholder="Mínimo 8 caracteres" style="padding-right: 40px;" />
                                        <i class="fas fa-eye" id="ojo_nueva" onclick="mostrarOcultar('pass_nueva_input', 'ojo_nueva')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #293661;"></i>
                                    </div>
                                    
                                    <ul id="lista-requisitos-pass"></ul> 
                                </div>

                                <div class="form-group">
                                    <label>Confirmar Nueva:</label>
                                    <div style="position: relative;">
                                        <input type="password" id="pass_confirm_input" class="input-display" placeholder="Repite la contraseña" style="padding-right: 40px;" />
                                        <i class="fas fa-eye" id="ojo_confirmar" onclick="mostrarOcultar('pass_confirm_input', 'ojo_confirmar')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #293661;"></i>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top: 20px; text-align: right;">
                                <button type="button" id="btn-guardar-pass" onclick="guardarPasswordAJAX()" 
                                        style="background-color: #293661; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold;">
                                    Actualizar Contraseña
                                </button>
                            </div>
                        </div>
                    </div>


                    <div class="separator-line full-width"></div>
                    <h2 class="form-section-title full-width">Dirección del domicilio</h2>

                    <div class="form-group full-width">
                        <label for="calle" class="label-icon"><i class="fas fa-map-marker-alt"></i> Calle:</label>
                        <input type="text" id="calle" class="input-display" name="calle" 
                               value="<?php echo val($cliente['direccion']); ?>" required />
                    </div>
                    
                    <div class="form-group">
                        <label for="numero" class="label-icon"><i class="fas fa-hashtag"></i> Número:</label>
                        <input type="text" id="numero" class="input-display" name="numero" 
                               value="<?php echo val($cliente['numero']); ?>" placeholder="Ej: 12" required />
                    </div>

                    <div class="form-group">
                        <label for="piso" class="label-icon"><i class="fas fa-building"></i> Piso / Puerta:</label>
                        <input type="text" id="piso" class="input-display" name="piso" 
                               value="<?php echo val($cliente['piso']); ?>" placeholder="Ej: 3º A" />
                    </div>

                    <div class="form-group">
                        <label for="cp" class="label-icon"><i class="fas fa-envelope-open-text"></i> Código Postal:</label>
                        <input type="text" id="cp" class="input-display" name="cp" 
                               value="<?php echo val($cliente['codigo_postal']); ?>" required />
                    </div>

                    <div class="form-group">
                        <label for="poblacion" class="label-icon"><i class="fas fa-city"></i> Población / Ciudad:</label>
                        <input type="text" id="poblacion" class="input-display" name="poblacion" 
                               value="<?php echo val($cliente['ciudad']); ?>" required />
                    </div>

                    <div class="form-group full-width">
                        <label for="provincia" class="label-icon"><i class="fas fa-map"></i> Provincia:</label>
                        <input type="text" id="provincia" class="input-display" name="provincia" 
                               value="<?php echo val($cliente['provincia']); ?>" placeholder="Granada" required />
                    </div>

                    
                    <div class="botones-finales" style="grid-column: span 2;">
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

        <?php if(function_exists('sectionfooter')) sectionfooter(); ?>
    </div>

    <script src="js/AlgoritmoDNIs.js"></script>
    <script src="js/validarpasswd.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
        
            activarValidacionPassword(
                'pass_nueva_input',      // ID del input contraseña
                'pass_confirm_input',    // ID del input confirmar
                'lista-requisitos-pass', // ID de la lista UL
                'btn-guardar-pass'       // ID del botón a bloquear
            );

        });
        
        
        function confirmarModificacion() {
            const formulario = document.getElementById('form-modificar');
            const inputDNI = document.getElementById('dni');

            // 1. Validar campos vacíos HTML
            if (!formulario.checkValidity()) {
                formulario.reportValidity();
                return; 
            }

            // 2. VALIDAR DNI (CON SEGURIDAD)
            // Primero comprobamos si la función existe
            if (typeof validarDocumento === 'function') {
                
                // Ejecutamos validación
                const esValido = validarDocumento(inputDNI);

                if (esValido === false) {
                    // SI ESTÁ MAL EL DNI:
                    Swal.fire({
                        icon: 'error',
                        title: 'Documento Incorrecto',
                        text: 'El DNI/NIE tiene un formato o letra inválida. Corrígelo para guardar.',
                        confirmButtonColor: '#293661'
                    });
                    inputDNI.focus(); // Llevamos al usuario al campo DNI
                    return; // <--- ¡STOP! AQUÍ PARAMOS EL PROCESO
                }

            } else {
                // SI NO ENCUENTRA LA FUNCIÓN (ERROR TÉCNICO)
                // Antes esto dejaba pasar, AHORA NO.
                Swal.fire({
                    icon: 'warning',
                    title: 'Error de carga',
                    text: 'No se ha cargado el validador de DNI. Prueba a recargar la página (Ctrl + F5).',
                });
                console.error("CRÍTICO: La función 'validarDocumento' no existe. Revisa js/AlgoritmoDNIs.js");
                return; // <--- ¡STOP! NO DEJAMOS GUARDAR SI NO PODEMOS VALIDAR
            }

            // 3. SI LLEGAMOS AQUÍ, TODO ESTÁ BIEN -> GUARDAMOS
            Swal.fire({
                title: '¿Guardar cambios?',
                text: "¿Estás seguro de que quieres actualizar tus datos?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#293661',
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    formulario.submit();
                }
            });
        }

        function confirmarSalida() {
            Swal.fire({
                title: '¿Salir sin guardar?',
                text: "Se perderán los cambios que no haya guardado.",
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
                    window.location.href = 'perfil.php';
                }
            });
        }

        function toggleSeguridad() {
        const contenedor = document.getElementById('contenedor-seguridad');
        // Alternar visibilidad
        if (contenedor.style.display === 'none') {
            contenedor.style.display = 'block';
        } else {
            contenedor.style.display = 'none';
        }
    }

    function verificarPasswordAJAX() {
        console.log("Iniciando comprobación..."); // CHIVATO 1

        // Verificamos si SweetAlert está cargado
        if (typeof Swal === 'undefined') {
            alert("Error: La librería SweetAlert no está cargada. Revisa el CabeceraFooter.");
            return;
        }

        const passInput = document.getElementById('password_actual_check');
        const pass = passInput.value;

        if (!pass) {
            Swal.fire('Atención', 'Por favor, escribe tu contraseña actual.', 'warning');
            return;
        }

        const formData = new FormData();
        formData.append('ajax_verificar', '1');
        formData.append('password_check', pass);

        // Fetch a la misma página
        fetch(window.location.href, { // Usamos window.location.href para mayor seguridad
            method: 'POST',
            body: formData
        })
        .then(response => response.text()) // <--- IMPORTANTE: Leemos como TEXTO primero para ver errores
        .then(text => {
            console.log("Respuesta cruda del servidor:", text); // CHIVATO 2: Aquí verás si hay errores PHP

            try {
                return JSON.parse(text); // Intentamos convertir a JSON
            } catch (e) {
                console.error("No es un JSON válido");
                throw new Error("El servidor devolvió algo que no es JSON. Mira la consola.");
            }
        })
        .then(data => {
            console.log("Datos JSON:", data); // CHIVATO 3

            if (data.status === 'success') {
                // 1. Ocultar paso verificación
                document.getElementById('paso-verificacion').style.display = 'none';
                
                // 2. Mostrar formulario de cambio
                document.getElementById('paso-cambio').style.display = 'block';
                
                // 3. Copiar la contraseña válida
                document.getElementById('password_actual_final').value = pass;
                
                const Toast = Swal.mixin({
                    toast: true, position: 'top-end', showConfirmButton: false, timer: 3000
                });
                Toast.fire({ icon: 'success', title: 'Contraseña correcta' });

            } else {
                Swal.fire('Error', 'La contraseña no es correcta.', 'error');
                passInput.value = ''; 
            }
        });
    }

    function guardarPasswordAJAX() {
        console.log("Botón pulsado: Intentando guardar contraseña...");

        const passNueva = document.getElementById('password_nueva').value;
        const passConfirm = document.getElementById('password_confirmar').value;

        if (passNueva.length < 4) {
            Swal.fire('Atención', 'La contraseña debe tener al menos 4 caracteres.', 'warning');
            return;
        }
        if (passNueva !== passConfirm) {
            Swal.fire('Atención', 'Las contraseñas no coinciden.', 'warning');
            return;
        }

        const formData = new FormData();
        formData.append('ajax_guardar_password', '1'); // Clave para el PHP
        formData.append('pass_nueva', passNueva);
        formData.append('pass_confirm', passConfirm);

        // Fetch
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text()) 
        .then(text => {
            console.log("Respuesta del servidor:", text); 
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error("El servidor no devolvió JSON válido.");
            }
        })
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: '¡Actualizado!',
                    text: 'Nueva contraseña guardada correctamente.',
                    icon: 'success',
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    // Limpiar y cerrar
                    document.getElementById('password_nueva').value = '';
                    document.getElementById('password_confirmar').value = '';
                    document.getElementById('password_actual_check').value = '';
                    document.getElementById('contenedor-seguridad').style.display = 'none';
                    // Reiniciar el flujo por si quiere cambiarla de nuevo
                    document.getElementById('paso-verificacion').style.display = 'block';
                    document.getElementById('paso-cambio').style.display = 'none';
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => {
            console.error("Error JS:", error);
            Swal.fire('Error Técnico', 'Revisa la consola (F12) para más detalles.', 'error');
        });
    }

    function mostrarOcultar(idInput, idIcono) {
        const input = document.getElementById(idInput);
        const icono = document.getElementById(idIcono);

        if (input.type === "password") {
            input.type = "text"; // Muestra el texto
            icono.classList.remove("fa-eye");
            icono.classList.add("fa-eye-slash"); // Cambia icono a ojo tachado
        } else {
            input.type = "password"; // Oculta el texto
            icono.classList.remove("fa-eye-slash");
            icono.classList.add("fa-eye"); // Vuelve al icono normal
        }
    }


    </script>

</body>
</html>