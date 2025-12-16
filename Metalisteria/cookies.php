<?php
    // 1. CARGA DE RECURSOS Y SESIÓN
    include 'CabeceraFooter.php'; 
    include 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Cookies - Metalistería Fulsan</title>
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
                <h1 class="legal-title">Política de Cookies</h1>
                <span class="legal-date">Última actualización: 15 de Marzo de 2024</span>

                <div class="legal-body">
                    <p>Este sitio web utiliza cookies propias y de terceros para mejorar la experiencia del usuario y analizar nuestros servicios.</p>

                    <h2>1. ¿Qué son las cookies?</h2>
                    <p>Las cookies son pequeños archivos de texto que se descargan en su navegador cuando accede a determinados sitios web. Permiten almacenar y recuperar información sobre los hábitos de navegación de un usuario o de su equipo.</p>

                    <h2>2. Tipos de Cookies que utilizamos</h2>
                    <ul>
                        <li><strong>Cookies técnicas:</strong> Son necesarias para el correcto funcionamiento de la web (ej. mantener la sesión o el carrito de compra).</li>
                    </ul>

                    <h2>3. Gestión de Cookies</h2>
                    <p>Puede usted permitir, bloquear o eliminar las cookies instaladas en su equipo mediante la configuración de las opciones del navegador instalado en su ordenador:</p>
                    <ul>
                        <li>Chrome</li>
                        <li>Firefox</li>
                        <li>Safari</li>
                        <li>Microsoft Edge</li>
                    </ul>
                    <p>Si decide desactivar las cookies, es posible que algunas funciones del sitio web no funcionen correctamente.</p>
                </div>
            </div>
        </main>

        <?php sectionfooter(); ?>
    </div>
    <script src="js/auth.js"></script>
</body>
</html>