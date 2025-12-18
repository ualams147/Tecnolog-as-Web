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
    <title><?php echo $lang['privacidad_titulo_pag']; ?></title>
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
                <h1 class="legal-title"><?php echo $lang['privacidad_h1']; ?></h1>
                <span class="legal-date"><?php echo $lang['privacidad_fecha']; ?></span>

                <div class="legal-body">
                    <p><?php echo $lang['privacidad_intro']; ?></p>

                    <h2><?php echo $lang['privacidad_h2_1']; ?></h2>
                    <p><?php echo $lang['privacidad_p_1']; ?></p>

                    <h2><?php echo $lang['privacidad_h2_2']; ?></h2>
                    <p><?php echo $lang['privacidad_p_2_intro']; ?></p>
                    <ul>
                        <li><?php echo $lang['privacidad_li_1']; ?></li>
                        <li><?php echo $lang['privacidad_li_2']; ?></li>
                        <li><?php echo $lang['privacidad_li_3']; ?></li>
                        <li><?php echo $lang['privacidad_li_4']; ?></li>
                    </ul>

                    <h2><?php echo $lang['privacidad_h2_3']; ?></h2>
                    <p><?php echo $lang['privacidad_p_3']; ?></p>

                    <h2><?php echo $lang['privacidad_h2_4']; ?></h2>
                    <p><?php echo $lang['privacidad_p_4']; ?></p>
                </div>
            </div>
        </main>

        <?php sectionfooter(); ?>
    </div>
</body>
</html>