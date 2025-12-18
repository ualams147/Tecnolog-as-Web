<?php
include 'CabeceraFooter.php'; 
?>

<!DOCTYPE html>
<html lang="<?php echo isset($_SESSION['idioma']) ? $_SESSION['idioma'] : 'es'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['conocenos_titulo_pag']; ?></title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="stylesheet" href="css/conocenos.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="visitante-conocenos">
        
        <?php sectionheader(2); ?>

        <section class="nuestra-empresa">
            <div class="content-wrapper">
                <div class="text-content">
                    <h2 class="title"><?php echo $lang['empresa_titulo']; ?></h2>
                    <p><?php echo $lang['empresa_desc1']; ?></p>
                    <p><?php echo $lang['empresa_desc2']; ?></p>
                </div>
                <div class="foto-taller">
                    <img src="imagenes/fotos taller0001.jpg" alt="Foto del taller">
                </div>
            </div>
        </section>

        <section class="nosotros">
            <div class="container">
                <div class="content-grid">
                    <div class="image-container">
                        <img src="imagenes/memento_mori.png" alt="Nosotros">
                    </div>
                    <div class="text-container">
                        <h2 class="heading"><?php echo $lang['nosotros_titulo']; ?></h2>
                        <p class="intro"><?php echo $lang['nosotros_intro']; ?></p>
                        <p class="description"><?php echo $lang['nosotros_historia']; ?></p>
                        <p class="description"><?php echo $lang['nosotros_servicios']; ?></p>
                    </div>
                </div>
            </div>
        </section>

        <?php sectionfooter(); ?>
        
    </div>
    
</body>
</html>