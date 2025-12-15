<?php
include '../CabeceraFooter.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¿Eres Cliente? - Metalistería Fulsan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/eresCliente.css">
</head>
<body>
    <div class="visitante-cliente">
        
        <?php sectionheader(); ?>

        <main class="cliente-section">
            
            <div class="cliente-card">
                
                <h1 class="cliente-title">Inicia sesión o crea una cuenta para continuar</h1>

                <div class="buttons-row">
                    <a href="datosEnvio.php" class="btn-big-action">Iniciar Sesión</a>
                    <a href="datosEnvio.php" class="btn-big-action">Registro</a>
                </div>

                <a href="carrito.php" class="btn-back-pill">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                    Volver atrás
                </a>

            </div>

        </main>

        <?php sectionfooter(); ?>
    </div>
    <script src="../js/auth.js"></script>
</body>
</html>