<?php
// 1. INICIO DE SESIÓN Y BUFFER
session_start();
ob_start(); 

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

include 'conexion.php'; 
include 'CabeceraFooter.php'; 

// 2. SEGURIDAD DE SESIÓN
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['usuario_id'];
$mensaje = "";

// =======================================================================
// BLOQUE 1 Y 2: AJAX (CONTRASEÑA)
// =======================================================================
if (isset($_POST['ajax_verificar']) || isset($_POST['ajax_guardar_password'])) {
    ob_clean(); 
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
            // Mensaje traducido
            $msg_error = isset($lang['profile_js_pass_incorrect']) ? $lang['profile_js_pass_incorrect'] : 'Contraseña incorrecta';
            $response = $es_correcta ? ['status' => 'success'] : ['status' => 'error', 'message' => $msg_error];
        
        } elseif (isset($_POST['ajax_guardar_password'])) {
            $pass_nueva = $_POST['pass_nueva'] ?? '';
            $pass_confirm = $_POST['pass_confirm'] ?? '';

            // Mensajes traducidos para excepciones
            if (strlen($pass_nueva) < 8) throw new Exception($lang['profile_js_err_short']);
            if ($pass_nueva !== $pass_confirm) throw new Exception($lang['profile_js_err_match']);

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
        header("Location: editarinformacionperfil.php?actualizado=1"); 
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

function val($dato) {
    return htmlspecialchars($dato ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['idioma']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['profile_edit_title']; ?></title>
    
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
                <h1 class="address-title-main"><?php echo $lang['profile_my_data']; ?></h1>
            </div>
        </section>

        <div class="container main-container">
            
            <?php if(isset($_GET['actualizado'])): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: '<?php echo $lang['profile_swal_success_tit']; ?>',
                            text: '<?php echo $lang['profile_swal_success_txt']; ?>',
                            confirmButtonColor: '#293661'
                        });
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
                
                <h2 class="form-section-title"><?php echo $lang['profile_personal_data']; ?></h2>

                <form id="form-modificar" class="formulario-cliente" method="POST">
                    
                    <div class="form-group">
                        <label class="label-icon"><i class="far fa-user"></i> <?php echo $lang['profile_lbl_name']; ?></label>
                        <input type="text" id="nombre" class="input-display" name="nombre" 
                               value="<?php echo val($cliente['nombre']); ?>" required />
                    </div>

                    <div class="form-group">
                        <label class="label-icon"><i class="far fa-user"></i> <?php echo $lang['profile_lbl_surname']; ?></label>
                        <input type="text" id="apellidos" class="input-display" name="apellidos" 
                               value="<?php echo val($cliente['apellidos']); ?>" required />
                    </div>

                    <div class="form-group full-width">
                        <label class="label-icon"><i class="far fa-envelope"></i> <?php echo $lang['profile_lbl_email']; ?></label>
                        <input type="email" id="correo" class="input-display" name="correo" 
                               value="<?php echo val($cliente['email']); ?>" required />
                    </div>

                    <div class="form-group">
                        <label class="label-icon"><i class="far fa-id-card"></i> <?php echo $lang['profile_lbl_dni']; ?></label>
                        <input type="text" id="dni" class="input-display" name="dni" 
                               value="<?php echo val($cliente['dni']); ?>" placeholder="Ej: 12345678Z" required />
                    </div>

                    <div class="form-group">
                        <label class="label-icon"><i class="fas fa-phone-alt"></i> <?php echo $lang['profile_lbl_phone']; ?></label>
                        <input type="tel" id="telefono" class="input-display" name="telefono" 
                               value="<?php echo val($cliente['telefono']); ?>" required />
                    </div>

                    <div class="full-width" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; margin-top: 30px;">
                        <button type="button" onclick="toggleSeguridad()" style="background: none; border: 2px solid #293661; color: #293661; padding: 5px 15px; border-radius: 20px; cursor: pointer; font-weight: 600; font-family: 'Poppins', sans-serif;">
                            <?php echo $lang['profile_btn_change_pass']; ?> 
                        </button>
                    </div>

                    <div id="contenedor-seguridad" class="full-width" style="display: none; background: #f8f9fa; padding: 20px; border-radius: 10px; border: 1px solid #e9ecef;">
                        <div id="paso-verificacion" style="display: block;">
                            <p style="margin-bottom: 15px; color: #666;"><?php echo $lang['profile_pass_instruction']; ?></p>
                            <div class="form-group full-width">
                                <label><?php echo $lang['profile_lbl_current_pass']; ?></label>
                                <div style="display: flex; gap: 10px;">
                                    <div style="position: relative; flex: 1;">
                                        <input type="password" id="password_actual_check" class="input-display" style="width: 100%; padding-right: 40px;" />
                                        <i class="fas fa-eye" id="ojo_actual" onclick="mostrarOcultar('password_actual_check', 'ojo_actual')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #293661;"></i>
                                    </div>
                                    <button type="button" onclick="verificarPasswordAJAX()" style="background: #293661; color: white; border: none; padding: 0 20px; border-radius: 6px; cursor: pointer;"><?php echo $lang['profile_btn_check']; ?></button>
                                </div>
                            </div>
                        </div>

                        <div id="paso-cambio" style="display: none;">
                            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                                <i class="fas fa-check-circle"></i> <?php echo $lang['profile_identity_verified']; ?>
                            </div>
                            <div class="formulario-cliente" style="padding: 0;"> 
                                <div class="form-group">
                                    <label><?php echo $lang['profile_lbl_new_pass']; ?></label>
                                    <div style="position: relative;">
                                        <input type="password" id="pass_nueva_input" class="input-display" placeholder="<?php echo $lang['profile_ph_min_chars']; ?>" style="padding-right: 40px;" />
                                        <i class="fas fa-eye" id="ojo_nueva" onclick="mostrarOcultar('pass_nueva_input', 'ojo_nueva')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #293661;"></i>
                                    </div>
                                    
                                    <ul id="lista-requisitos-pass"></ul> 
                                </div>

                                <div class="form-group">
                                    <label><?php echo $lang['profile_lbl_confirm_pass']; ?></label>
                                    <div style="position: relative;">
                                        <input type="password" id="pass_confirm_input" class="input-display" placeholder="<?php echo $lang['profile_ph_repeat_pass']; ?>" style="padding-right: 40px;" />
                                        <i class="fas fa-eye" id="ojo_confirmar" onclick="mostrarOcultar('pass_confirm_input', 'ojo_confirmar')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #293661;"></i>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top: 20px; text-align: right;">
                                <button type="button" id="btn-guardar-pass" onclick="guardarPasswordAJAX()" 
                                        style="background-color: #293661; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold;">
                                    <?php echo $lang['profile_btn_update_pass']; ?>
                                </button>
                            </div>
                        </div>
                    </div>


                    <div class="separator-line full-width"></div>
                    <h2 class="form-section-title full-width"><?php echo $lang['profile_address_title']; ?></h2>

                    <div class="form-group full-width">
                        <label for="calle" class="label-icon"><i class="fas fa-map-marker-alt"></i> <?php echo $lang['profile_lbl_street']; ?></label>
                        <input type="text" id="calle" class="input-display" name="calle" 
                               value="<?php echo val($cliente['direccion']); ?>" required />
                    </div>
                    
                    <div class="form-group">
                        <label for="numero" class="label-icon"><i class="fas fa-hashtag"></i> <?php echo $lang['profile_lbl_num']; ?></label>
                        <input type="text" id="numero" class="input-display" name="numero" 
                               value="<?php echo val($cliente['numero']); ?>" placeholder="Ej: 12" required />
                    </div>

                    <div class="form-group">
                        <label for="piso" class="label-icon"><i class="fas fa-building"></i> <?php echo $lang['profile_lbl_floor']; ?></label>
                        <input type="text" id="piso" class="input-display" name="piso" 
                               value="<?php echo val($cliente['piso']); ?>" placeholder="Ej: 3º A" />
                    </div>

                    <div class="form-group">
                        <label for="cp" class="label-icon"><i class="fas fa-envelope-open-text"></i> <?php echo $lang['profile_lbl_cp']; ?></label>
                        <input type="text" id="cp" class="input-display" name="cp" 
                               value="<?php echo val($cliente['codigo_postal']); ?>" required />
                    </div>

                    <div class="form-group">
                        <label for="poblacion" class="label-icon"><i class="fas fa-city"></i> <?php echo $lang['profile_lbl_city']; ?></label>
                        <input type="text" id="poblacion" class="input-display" name="poblacion" 
                               value="<?php echo val($cliente['ciudad']); ?>" required />
                    </div>

                    <div class="form-group full-width">
                        <label for="provincia" class="label-icon"><i class="fas fa-map"></i> <?php echo $lang['profile_lbl_province']; ?></label>
                        <input type="text" id="provincia" class="input-display" name="provincia" 
                               value="<?php echo val($cliente['provincia']); ?>" placeholder="Granada" required />
                    </div>

                    
                    <div class="botones-finales" style="grid-column: span 2;">
                        <div class="boton-salir">
                            <a href="javascript:void(0);" onclick="confirmarSalida()"><?php echo $lang['profile_btn_exit']; ?></a>
                        </div>
                        
                        <div class="boton-modificar">
                            <button type="button" name="actualizar" onclick="confirmarModificacion()">
                                <?php echo $lang['profile_btn_save']; ?>
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <?php if(function_exists('sectionfooter')) sectionfooter(); ?>
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
            input.style.borderColor = ''; 

            if (valor === '' && input.hasAttribute('required')) return false;
            if (valor === '') return true;

            // 1. DNI
            if (/^\d{8}[A-Z]$/.test(valor)) {
                const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
                if(letras[valor.substr(0, 8) % 23] === valor.substr(8, 1)) {
                    input.style.borderColor = 'green';
                    return true;
                }
                input.style.borderColor = 'red';
                return false;
            }
            // 2. NIE
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
            // Error
            input.style.borderColor = 'red';
            return false;
        }

        // =========================================================================
        // 2. LÓGICA PRINCIPAL (FORMULARIO Y ALERTAS)
        // =========================================================================
        document.addEventListener("DOMContentLoaded", function() {
            
            // Alertas PHP
            <?php if(isset($_GET['actualizado'])): ?>
                Swal.fire({
                    icon: 'success', title: '<?php echo $lang['profile_swal_success_tit']; ?>',
                    text: '<?php echo $lang['profile_swal_success_txt']; ?>', confirmButtonColor: '#293661'
                });
            <?php endif; ?>

            // ACTIVAR VALIDACIÓN PASSWORD
            activarValidacionPassword(
                'pass_nueva_input',      
                'pass_confirm_input',    
                'lista-requisitos-pass', 
                'btn-guardar-pass'       
            );
        });
        
        function confirmarModificacion() {
            const formulario = document.getElementById('form-modificar');
            const inputDNI = document.getElementById('dni');

            if (!formulario.checkValidity()) {
                formulario.reportValidity();
                return; 
            }

            if (!validarDocumento(inputDNI)) {
                Swal.fire({
                    icon: 'error', title: '<?php echo $lang['profile_js_err_dni_tit']; ?>',
                    text: '<?php echo $lang['profile_js_err_dni_txt']; ?>', confirmButtonColor: '#293661'
                });
                inputDNI.focus(); 
                return; 
            }

            Swal.fire({
                title: '<?php echo $lang['profile_js_save_tit']; ?>', text: "<?php echo $lang['profile_js_save_txt']; ?>",
                icon: 'question', showCancelButton: true, confirmButtonColor: '#293661',
                confirmButtonText: '<?php echo $lang['profile_js_btn_yes']; ?>', cancelButtonText: '<?php echo $lang['profile_js_btn_cancel']; ?>'
            }).then((result) => {
                if (result.isConfirmed) {
                    formulario.submit();
                }
            });
        }

        function confirmarSalida() {
            Swal.fire({
                title: '<?php echo $lang['profile_js_exit_tit']; ?>', text: "<?php echo $lang['profile_js_exit_txt']; ?>",
                icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
                cancelButtonColor: '#293661', confirmButtonText: '<?php echo $lang['profile_js_btn_exit']; ?>',
                cancelButtonText: '<?php echo $lang['profile_js_btn_cancel']; ?>'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'perfil.php';
                }
            });
        }

        function toggleSeguridad() {
            const contenedor = document.getElementById('contenedor-seguridad');
            contenedor.style.display = (contenedor.style.display === 'none') ? 'block' : 'none';
        }

        function verificarPasswordAJAX() {
            const passInput = document.getElementById('password_actual_check');
            if (!passInput.value) {
                Swal.fire('<?php echo $lang['profile_js_attention']; ?>', '<?php echo $lang['profile_js_err_empty']; ?>', 'warning');
                return;
            }

            const formData = new FormData();
            formData.append('ajax_verificar', '1');
            formData.append('password_check', passInput.value);

            fetch(window.location.href, { method: 'POST', body: formData })
            .then(r => r.text()).then(text => {
                try { return JSON.parse(text); } catch(e){ throw new Error("Error Servidor"); }
            })
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('paso-verificacion').style.display = 'none';
                    document.getElementById('paso-cambio').style.display = 'block';
                    Swal.fire({ icon: 'success', title: '<?php echo $lang['profile_js_pass_correct']; ?>', timer: 1500, showConfirmButton: false });
                } else {
                    Swal.fire('Error', data.message, 'error');
                    passInput.value = ''; 
                }
            });
        }

        function guardarPasswordAJAX() {
            const passNueva = document.getElementById('pass_nueva_input').value;
            const passConfirm = document.getElementById('pass_confirm_input').value;

            if (passNueva.length < 8) {
                Swal.fire('Atención', 'Mínimo 8 caracteres', 'warning');
                return;
            }
            if (passNueva !== passConfirm) {
                Swal.fire('Atención', 'Las contraseñas no coinciden', 'warning');
                return;
            }

            const formData = new FormData();
            formData.append('ajax_guardar_password', '1'); 
            formData.append('pass_nueva', passNueva);
            formData.append('pass_confirm', passConfirm);

            fetch(window.location.href, { method: 'POST', body: formData })
            .then(r => r.text()).then(text => {
                try { return JSON.parse(text); } catch(e){ throw new Error("Error Servidor"); }
            })
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: '<?php echo $lang['profile_js_pass_updated_tit']; ?>',
                        text: '<?php echo $lang['profile_js_pass_updated_txt']; ?>',
                        icon: 'success', confirmButtonColor: '#28a745'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        }

        function mostrarOcultar(idInput, idIcono) {
            const input = document.getElementById(idInput);
            const icono = document.getElementById(idIcono);
            if (input.type === "password") {
                input.type = "text"; 
                icono.className = "fas fa-eye-slash"; 
            } else {
                input.type = "password"; 
                icono.className = "fas fa-eye"; 
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
                        btnSubmit.style.opacity = '1';
                        btnSubmit.style.cursor = 'pointer';
                        listaReq.style.display = 'none'; 
                    } else {
                        listaReq.style.display = 'block'; 
                        btnSubmit.disabled = !todoValido;
                        btnSubmit.style.opacity = todoValido ? '1' : '0.5';
                        btnSubmit.style.cursor = todoValido ? 'pointer' : 'not-allowed';
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