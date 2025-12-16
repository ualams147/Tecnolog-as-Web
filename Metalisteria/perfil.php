<?php
// 1. CABECERA Y SESIÓN (Siempre lo primero)
include 'CabeceraFooter.php'; // Carga el idioma
include 'conexion.php'; 

// 2. SEGURIDAD: Si no hay sesión iniciada, lo mandamos al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: iniciarsesion.php"); // Corregido: redirigir al login, no a perfil (bucle)
    exit;
}

// 3. RECUPERAR DATOS: Buscamos al usuario en la BD
$id_usuario = $_SESSION['usuario_id'];
$sql = "SELECT * FROM clientes WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['idioma']) ? $_SESSION['idioma'] : 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['perfil_titulo_pag']; ?></title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/perfil.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="visitante-perfil">
        
        <?php sectionheader(6); ?>

        <section class="profile-hero">
            <div class="container hero-content">
                <a href="pedidosactivos.php" class="btn-pedidos">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                    <?php echo $lang['perfil_btn_pedidos']; ?>
                </a>
            </div>
        </section>

        <main class="profile-main container">
            <div class="profile-card">
                
                <h1 class="profile-title"><?php echo $lang['perfil_h1']; ?></h1>
                
                <form class="profile-form">
                    
                    <div class="form-row">
                        <label class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            <?php echo $lang['perfil_lbl_nombre']; ?>
                        </label>
                        <input type="text" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" class="form-input" readonly>
                    </div>

                    <div class="form-row">
                        <label class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            <?php echo $lang['perfil_lbl_apellidos']; ?>
                        </label>
                        <input type="text" value="<?php echo htmlspecialchars($usuario['apellidos']); ?>" class="form-input" readonly>
                    </div>

                    <div class="form-row">
                        <label class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                            <?php echo $lang['perfil_lbl_email']; ?>
                        </label>
                        <input type="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" class="form-input" readonly>
                    </div>

                    <div class="form-row">
                        <label class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M2 4v16h20V4H2zm18 14H4V6h16v12zM6 10h4v4H6v-4zm6 0h6v2h-6v-2zm0 4h6v2h-6v-2z"/></svg>
                            <?php echo $lang['perfil_lbl_dni']; ?>
                        </label>
                        <input type="text" value="<?php echo htmlspecialchars($usuario['dni']); ?>" class="form-input" readonly>
                    </div>

                    <div class="form-row">
                        <label class="label-icon">
                            <svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                            <?php echo $lang['perfil_lbl_telefono']; ?>
                        </label>
                        <input type="tel" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" class="form-input" readonly>
                    </div>

                    <div class="form-row">
                         <label class="label-icon">🏠 <?php echo $lang['perfil_lbl_direccion']; ?></label>
                         <input type="text" value="<?php echo htmlspecialchars($usuario['direccion'] . ', ' . $usuario['numero'] . ', ' . $usuario['piso']); ?>" class="form-input" readonly>
                    </div>

                    <div class="edit-buttons-container">
                        <a href="modificardatoscliente.php" class="btn-edit" style="text-decoration: none; text-align: center; display: block;">
                            <?php echo $lang['perfil_btn_editar']; ?>
                        </a>
                    </div>

                </form>

            </div>

        </main>

        <?php sectionfooter(); ?>
    </div>

    <script src="js/auth.js"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const params = new URLSearchParams(window.location.search);
            if (params.get('actualizado') === '1') {
                Swal.fire({
                    title: '<?php echo $lang['perfil_swal_success_tit']; ?>',
                    text: '<?php echo $lang['perfil_swal_success_txt']; ?>',
                    icon: 'success',
                    confirmButtonColor: '#293661',
                    confirmButtonText: '<?php echo $lang['perfil_swal_btn']; ?>'
                }).then(() => {
                    window.history.replaceState({}, document.title, window.location.pathname);
                });
            }
        });
    </script>
    
</body>
</html>