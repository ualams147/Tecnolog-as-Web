<?php
    // 1. CARGA DE RECURSOS Y SESIÓN
    include 'CabeceraFooter.php'; 
    include 'conexion.php';
?>

<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['idioma']) ? $_SESSION['idioma'] : 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['legal_titulo_pag']; ?></title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/legal.css">
</head>
<body>
    <div class="visitante-legal">
        
        <?php sectionheader(); ?>

        <main class="legal-main">
            <div class="legal-document">
                <h1 class="legal-title"><?php echo $lang['legal_h1']; ?></h1>
                <span class="legal-date"><?php echo $lang['legal_fecha']; ?></span>

                <div class="legal-body">
                    <p><?php echo $lang['legal_intro']; ?></p>

                    <h2><?php echo $lang['legal_datos_t']; ?></h2>
                    <ul>
                        <li><strong><?php echo $lang['legal_nombre_com']; ?></strong> Metalistería Fulsan</li>
                        <li><strong><?php echo $lang['legal_denom_soc']; ?></strong> Metalistería Fulsan S.L.</li>
                        <li><strong>NIF:</strong> B-12345678</li>
                        <li><strong><?php echo $lang['legal_domicilio']; ?></strong> Extrarradio Cortijo la Purísima, 2P, 18004 Granada</li>
                        <li><strong><?php echo $lang['legal_correo']; ?></strong> metalfulsan@gmail.com</li>
                        <li><strong><?php echo $lang['legal_tel']; ?></strong> 652 921 960</li>
                    </ul>

                    <h2><?php echo $lang['legal_prop_t']; ?></h2>
                    <p><?php echo $lang['legal_prop_desc']; ?></p>

                    <h2><?php echo $lang['legal_prot_t']; ?></h2>
                    <p><?php echo $lang['legal_prot_desc']; ?></p>

                    <h2><?php echo $lang['legal_ley_t']; ?></h2>
                    <p><?php echo $lang['legal_ley_desc']; ?></p>
                </div>
            </div>
        </main>

        <?php sectionfooter(); ?>
    </div>
    <script src="js/auth.js"></script>
</body>
</html>