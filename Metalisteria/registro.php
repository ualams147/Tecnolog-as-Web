<?php
// 1. INICIAR SESIÓN
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 0. CARGAR IDIOMA
if (!isset($_SESSION['idioma'])) {
    $_SESSION['idioma'] = 'es';
}
$archivo_lang = "idiomas/" . $_SESSION['idioma'] . ".php";
if (file_exists($archivo_lang)) {
    include $archivo_lang;
} else {
    include "idiomas/es.php";
}

include 'CabeceraFooter.php'; 
include 'conexion.php'; 

$error = '';
$error_email = '';
$error_dni = '';
$success = '';

// --- INICIALIZAR VARIABLES ---
$nombre = '';
$apellidos = '';
$email = '';
$email_confirm = '';
$dni = '';
$telefono = '';
$calle = '';
$numero = '';
$piso = '';
$cp = '';
$localidad = '';

// --- CAPTURAR ORIGEN ---
$origen = '';
if (isset($_GET['origen'])) $origen = $_GET['origen'];
elseif (isset($_POST['origen'])) $origen = $_POST['origen'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $email = $_POST['email'] ?? '';
    $email_confirm = $_POST['email_confirm'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $dni = $_POST['dni'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $calle = $_POST['calle'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $piso = $_POST['piso'] ?? '';
    $cp = $_POST['cp'] ?? '';
    $localidad = $_POST['localidad'] ?? '';

    // Validaciones básicas PHP
    if ($email !== $email_confirm) {
        $error = $lang['registro_err_emails'];
    } elseif ($password !== $password_confirm) {
        $error = $lang['registro_err_pass'];
    } else {
        // Comprobar Email
        $stmt = $conn->prepare("SELECT id FROM clientes WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) $error_email = $lang['registro_err_email_existe'];

        // Comprobar DNI
        if (!empty($dni)) {
            $stmt = $conn->prepare("SELECT id FROM clientes WHERE dni = :dni LIMIT 1");
            $stmt->execute([':dni' => $dni]);
            if ($stmt->fetch()) $error_dni = $lang['registro_err_dni_existe'];
        }

        if (empty($error) && empty($error_email) && empty($error_dni)) {
            try {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO clientes (nombre, apellidos, email, password, dni, telefono, direccion, numero, piso, ciudad, codigo_postal, rol) 
                        VALUES (:nombre, :apellidos, :email, :password, :dni, :telefono, :direccion, :numero, :piso, :ciudad, :codigo_postal, 'cliente')";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':nombre' => $nombre, ':apellidos' => $apellidos, ':email' => $email,
                    ':password' => $password_hash,
                    ':dni' => $dni, ':telefono' => $telefono,
                    ':direccion' => $calle, ':numero' => $numero, ':piso' => $piso,
                    ':ciudad' => $localidad, ':codigo_postal' => $cp
                ]);

                // Login automático
                $nuevo_id = $conn->lastInsertId();
                $stmtUser = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
                $stmtUser->execute([$nuevo_id]);
                $nuevo_usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

                $_SESSION['usuario'] = $nuevo_usuario; 
                $_SESSION['usuario_id'] = $nuevo_usuario['id'];
                $_SESSION['usuario_nombre'] = $nuevo_usuario['nombre'];
                $_SESSION['usuario_rol'] = 'cliente';

                if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
                    foreach ($_SESSION['carrito'] as $item_sess) {
                        $stmtIns = $conn->prepare("INSERT INTO carrito (cliente_id, producto_id, cantidad) VALUES (?, ?, ?)");
                        $stmtIns->execute([$nuevo_id, $item_sess['id'], $item_sess['cantidad']]);
                    }
                }

                $destino = ($origen === 'compra') ? 'datosenvio.php' : 'index.php';
                echo "<script>localStorage.setItem('usuarioLogueado', 'true'); window.location.href = '$destino';</script>";
                exit;

            } catch (PDOException $e) {
                $error = $lang['registro_err_tecnico'] . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['idioma']) ? $_SESSION['idioma'] : 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['registro_titulo_pag']; ?></title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/registro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/auth.js"></script>
</head>
<body>
    <div class="visitante-registro">
        <?php sectionheader(); ?>

        <main class="registro-section">
            <div class="registro-card">
                <h1 class="registro-title"><?php echo $lang['registro_h1']; ?></h1>

                <form class="registro-form" id="form-registro" method="POST" action="">
                    <?php if(!empty($origen)): ?>
                        <input type="hidden" name="origen" value="<?php echo htmlspecialchars($origen); ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <label for="nombre" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg> <?php echo $lang['registro_lbl_nombre']; ?>
                        </label>
                        <input type="text" id="nombre" name="nombre" class="form-input" value="<?php echo htmlspecialchars($nombre); ?>" placeholder="<?php echo $lang['registro_ph_nombre']; ?>" required>
                    </div>

                    <div class="form-row">
                        <label for="apellidos" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg> <?php echo $lang['registro_lbl_apellidos']; ?>
                        </label>
                        <input type="text" id="apellidos" name="apellidos" class="form-input" value="<?php echo htmlspecialchars($apellidos); ?>" placeholder="<?php echo $lang['registro_ph_apellidos']; ?>" required>
                    </div>

                    <div class="form-row">
                        <label for="email" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg> <?php echo $lang['registro_lbl_email']; ?>
                        </label>
                        <input type="email" id="email" name="email" class="form-input <?php echo !empty($error_email) ? 'error' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>" placeholder="<?php echo $lang['registro_ph_email']; ?>" required>
                    </div>

                    <div class="form-row">
                        <label for="email_confirm" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg> 
                            <?php echo $lang['registro_lbl_email_conf']; ?>
                        </label>
                        <input type="email" id="email_confirm" name="email_confirm" class="form-input" value="<?php echo htmlspecialchars($email_confirm); ?>" placeholder="<?php echo $lang['registro_ph_email_conf']; ?>" required>
                    </div>

                    <div class="form-row alinear-arriba">
                        <label for="password" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg> 
                            <?php echo $lang['registro_lbl_pass']; ?>
                        </label>
                        
                        <div class="grupo-input-columna">
                            <div style="position: relative;">
                                <input type="password" id="password" name="password" class="form-input" required style="padding-right: 40px;">
                                <i class="fas fa-eye" id="ojo_nueva" onclick="mostrarOcultar('password', 'ojo_nueva')" 
                                   style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #293661;"></i>
                            </div>
                            
                            <ul id="lista-requisitos-pass"></ul> 
                        </div>
                    </div>

                    <div class="form-row">
                        <label for="password_confirm" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg> 
                            <?php echo $lang['registro_lbl_pass_conf']; ?>
                        </label>
                        
                        <div style="flex: 1; position: relative;">
                            <input type="password" id="password_confirm" name="password_confirm" class="form-input" required style="padding-right: 40px; width: 100%;">
                            <i class="fas fa-eye" id="ojo_confirm" onclick="mostrarOcultar('password_confirm', 'ojo_confirm')" 
                               style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #293661;"></i>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="dni" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg> <?php echo $lang['registro_lbl_dni']; ?>
                        </label>
                        <input type="text" id="dni" name="dni" class="form-input <?php echo !empty($error_dni) ? 'error' : ''; ?>" value="<?php echo htmlspecialchars($dni); ?>" placeholder="<?php echo $lang['registro_ph_dni']; ?>" required>
                    </div>

                    <div class="form-row">
                        <label for="telefono" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg> <?php echo $lang['registro_lbl_telefono']; ?>
                        </label>
                        <input type="tel" id="telefono" name="telefono" class="form-input" value="<?php echo htmlspecialchars($telefono); ?>" placeholder="<?php echo $lang['registro_ph_telefono']; ?>" required>
                    </div>

                    <div class="form-row">
                        <label for="calle" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg> <?php echo $lang['registro_lbl_calle']; ?>
                        </label>
                        <input type="text" id="calle" name="calle" class="form-input" value="<?php echo htmlspecialchars($calle); ?>" placeholder="<?php echo $lang['registro_ph_calle']; ?>" required>
                    </div>

                    <div class="form-row">
                        <label for="numero" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg> <?php echo $lang['registro_lbl_numero']; ?>
                        </label>
                        <div class="input-group">
                            <input type="text" id="numero" name="numero" class="form-input" value="<?php echo htmlspecialchars($numero); ?>" placeholder="<?php echo $lang['registro_ph_numero']; ?>" required>
                            <input type="text" id="piso" name="piso" class="form-input" value="<?php echo htmlspecialchars($piso); ?>" placeholder="<?php echo $lang['registro_ph_piso']; ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <label for="cp" class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg> <?php echo $lang['registro_lbl_cp']; ?>
                        </label>
                        <div class="input-group">
                            <input type="text" id="cp" name="cp" class="form-input" value="<?php echo htmlspecialchars($cp); ?>" placeholder="<?php echo $lang['registro_ph_cp']; ?>" required>
                            <input type="text" id="localidad" name="localidad" class="form-input" value="<?php echo !empty($localidad) ? htmlspecialchars($localidad) : 'Granada'; ?>" placeholder="<?php echo $lang['registro_ph_localidad']; ?>">
                        </div>
                    </div>

                    <button type="submit" id="btn-registrarse" class="btn-register-submit"><?php echo $lang['registro_btn_submit']; ?></button>

                    <p class="register-text">
                        <?php echo $lang['registro_txt_login']; ?> 
                        <a href="iniciarsesion.php<?php echo (!empty($origen)) ? '?origen='.$origen : ''; ?>">
                            <em><?php echo $lang['registro_link_login']; ?></em>
                        </a>
                    </p>

                </form> 
            </div>
        </main>

        <?php sectionfooter(); ?>
    </div>

    <script>
        // =========================================================================
        // 1. VALIDACIÓN DNI (INCRUSTADA)
        // =========================================================================
        document.addEventListener("DOMContentLoaded", function() {
            const inputDNI = document.getElementById('dni');
            if (inputDNI) {
                inputDNI.addEventListener('blur', function() {
                    validarDocumento(this);
                });
                inputDNI.addEventListener('input', function() {
                    this.style.borderColor = ''; // Limpiar al escribir
                });
            }
        });

        function validarDocumento(input) {
            if (!input) return false;
            const valor = input.value.toUpperCase().trim();
            input.style.borderColor = ''; // Limpiar

            if (valor === '' && input.hasAttribute('required')) return false;
            if (valor === '') return true;

            // 1. DNI (8 números + Letra)
            if (/^\d{8}[A-Z]$/.test(valor)) {
                const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
                if(letras[valor.substr(0, 8) % 23] === valor.substr(8, 1)) {
                    input.style.borderColor = 'green';
                    return true;
                }
                input.style.borderColor = 'red';
                return false;
            }
            // 2. NIE (X/Y/Z + 7 números + Letra)
            else if (/^[XYZ]\d{7}[A-Z]$/.test(valor)) {
                let num = valor.substr(0, 8).replace('X','0').replace('Y','1').replace('Z','2');
                const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
                if(letras[num % 23] === valor.substr(8, 1)){
                    input.style.borderColor = 'green';
                    return true;
                }
                input.style.borderColor = 'red';
                return false;
            }
            // 3. CIF
            else if (/^[ABCDEFGHJKLMNPQRSUVW]\d{7}[0-9A-J]$/.test(valor)) {
                input.style.borderColor = 'green';
                return true;
            }
            // Formato incorrecto
            input.style.borderColor = 'red';
            return false;
        }

        // =========================================================================
        // 2. LÓGICA PRINCIPAL (FORMULARIO Y ALERTAS)
        // =========================================================================
        document.addEventListener("DOMContentLoaded", function() {
            
            // ALERTAS PHP
            <?php if (!empty($error_email)): ?>
                Swal.fire({ icon: 'error', title: 'Email no disponible', text: '<?php echo $error_email; ?>', confirmButtonColor: '#293661' });
                document.getElementById('email').style.borderColor = 'red';
            <?php elseif (!empty($error_dni)): ?>
                Swal.fire({ icon: 'error', title: 'DNI ya registrado', text: '<?php echo $error_dni; ?>', confirmButtonColor: '#293661' });
                document.getElementById('dni').style.borderColor = 'red';
            <?php elseif (!empty($error)): ?>
                Swal.fire({ icon: 'error', title: 'Error', text: '<?php echo $error; ?>', confirmButtonColor: '#293661' });
            <?php endif; ?>

            // ACTIVAR VALIDACIÓN PASSWORD
            activarValidacionPassword(
                'password',              
                'password_confirm',      
                'lista-requisitos-pass', 
                'btn-registrarse'        
            );

            // LOGICA SUBMIT
            const form = document.getElementById('form-registro');
            const inputDNI = document.getElementById('dni');
            const inputEmail = document.getElementById('email');
            const inputEmailConf = document.getElementById('email_confirm');

            inputEmailConf.addEventListener('input', function() {
                this.style.borderColor = (this.value === inputEmail.value) ? 'green' : '';
            });

            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) return;

                if (inputEmail.value.trim() !== inputEmailConf.value.trim()) {
                    e.preventDefault(); 
                    Swal.fire({
                        icon: 'error', title: 'Los correos no coinciden', 
                        text: 'Asegúrate de que el correo y su confirmación sean iguales.', confirmButtonColor: '#293661'
                    });
                    inputEmailConf.style.borderColor = 'red';
                    inputEmailConf.focus();
                    return; 
                }

                if (!validarDocumento(inputDNI)) {
                    e.preventDefault(); 
                    Swal.fire({
                        icon: 'error', title: 'DNI Inválido', 
                        text: 'El formato del documento no es correcto.', confirmButtonColor: '#293661'
                    });
                    inputDNI.focus(); 
                }
            });
        });
        
        function mostrarOcultar(idInput, idIcono) {
            const input = document.getElementById(idInput);
            const icono = document.getElementById(idIcono);
            if (input.type === "password") {
                input.type = "text"; 
                icono.classList.remove("fa-eye"); icono.classList.add("fa-eye-slash"); 
            } else {
                input.type = "password"; 
                icono.classList.remove("fa-eye-slash"); icono.classList.add("fa-eye"); 
            }
        }

        // =========================================================================
        // 3. VALIDACIÓN PASSWORD (INCRUSTADA)
        // =========================================================================
        function activarValidacionPassword(idInputPass, idInputConfirm, idListaRequisitos, idBotonSubmit) {
            const inputPass = document.getElementById(idInputPass);
            const inputConfirm = document.getElementById(idInputConfirm);
            const listaReq = document.getElementById(idListaRequisitos);
            const btnSubmit = document.getElementById(idBotonSubmit);

            if (!inputPass || !listaReq) return;

            const reglas = [
                { id: 'req-longitud', regex: /.{8,}/, texto: 'Mínimo 8 caracteres' },
                { id: 'req-mayus', regex: /[A-Z]/, texto: 'Al menos una mayúscula' },
                { id: 'req-minus', regex: /[a-z]/, texto: 'Al menos una minúscula' },
                { id: 'req-num', regex: /[0-9]/, texto: 'Al menos un número' }
            ];

            listaReq.innerHTML = reglas.map(regla => 
                `<li id="${regla.id}" class="requisito-pendiente"><i class="fas fa-circle"></i> ${regla.texto}</li>`
            ).join('') + `<li id="req-coinciden" class="requisito-pendiente"><i class="fas fa-circle"></i> Las contraseñas coinciden</li>`;

            function validar() {
                const valor = inputPass.value;
                const valorConfirm = inputConfirm ? inputConfirm.value : '';
                let todoValido = true;

                reglas.forEach(regla => {
                    const item = document.getElementById(regla.id);
                    const cumple = regla.regex.test(valor);
                    actualizarEstilo(item, cumple);
                    if (!cumple) todoValido = false;
                });

                const itemCoinciden = document.getElementById('req-coinciden');
                if (inputConfirm) {
                    const coinciden = (valor === valorConfirm) && valor.length > 0;
                    actualizarEstilo(itemCoinciden, coinciden);
                    if (!coinciden) todoValido = false;
                }

                if (btnSubmit) {
                    if (valor.length === 0 && (!inputConfirm || inputConfirm.value.length === 0)) {
                        btnSubmit.disabled = false; 
                        listaReq.style.display = 'none'; 
                    } else {
                        listaReq.style.display = 'block'; 
                        btnSubmit.disabled = !todoValido;
                    }
                }
            }

            function actualizarEstilo(elemento, cumple) {
                if (cumple) {
                    elemento.className = 'requisito-bien';
                    elemento.querySelector('i').className = 'fas fa-check-circle';
                } else {
                    elemento.className = 'requisito-pendiente';
                    elemento.querySelector('i').className = 'far fa-circle';
                }
            }

            inputPass.addEventListener('input', validar);
            if (inputConfirm) inputConfirm.addEventListener('input', validar);
            listaReq.style.display = 'none';
        }
    </script>
</body>
</html>