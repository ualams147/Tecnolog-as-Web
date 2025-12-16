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
    <title><?php echo $lang['cookies_titulo_pag']; ?></title>
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
                <h1 class="legal-title"><?php echo $lang['cookies_h1']; ?></h1>
                <span class="legal-date"><?php echo $lang['cookies_fecha']; ?></span>

                <div class="legal-body">
                    <p><?php echo $lang['cookies_intro']; ?></p>

                    <h2><?php echo $lang['cookies_h2_1']; ?></h2>
                    <p><?php echo $lang['cookies_p_1']; ?></p>

                    <h2><?php echo $lang['cookies_h2_2']; ?></h2>
                    <ul>
                        <li><?php echo $lang['cookies_li_1']; ?></li>
                    </ul>

                    <h2><?php echo $lang['cookies_h2_3']; ?></h2>
                    <p><?php echo $lang['cookies_p_2']; ?></p>
                    <ul>
                        <li>Chrome</li>
                        <li>Firefox</li>
                        <li>Safari</li>
                        <li>Microsoft Edge</li>
                    </ul>
                    <p><?php echo $lang['cookies_p_3']; ?></p>
                </div>
            </div>
        </main>

        <?php sectionfooter(); ?>
    </div>
    <script src="js/auth.js"></script>
</body>
</html>