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
    <title>Aviso Legal - Metalistería Fulsan</title>
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
                <h1 class="legal-title">Aviso Legal</h1>
                <span class="legal-date">Última actualización: 15 de Marzo de 2024</span>

                <div class="legal-body">
                    <p>En cumplimiento del artículo 10 de la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y Comercio Electrónico (LSSICE), a continuación se exponen los datos identificativos de la entidad:</p>

                    <h2>1. Datos Identificativos</h2>
                    <ul>
                        <li><strong>Nombre comercial:</strong> Metalistería Fulsan</li>
                        <li><strong>Denominación social:</strong> Metalistería Fulsan S.L.</li>
                        <li><strong>NIF:</strong> B-12345678</li>
                        <li><strong>Domicilio social:</strong> Extrarradio Cortijo la Purísima, 2P, 18004 Granada</li>
                        <li><strong>Correo electrónico:</strong> metalfulsan@gmail.com</li>
                        <li><strong>Teléfono:</strong> 652 921 960</li>
                    </ul>

                    <h2>2. Propiedad Intelectual</h2>
                    <p>El código fuente, los diseños gráficos, las imágenes, las fotografías, los sonidos, las animaciones, el software, los textos, así como la información y los contenidos que se recogen en el presente sitio web están protegidos por la legislación española sobre los derechos de propiedad intelectual e industrial a favor de Metalistería Fulsan S.L.</p>

                    <h2>3. Protección de Datos Personales</h2>
                    <p>En el marco del cumplimiento de la legislación vigente, recogida en la Ley Orgánica 15/1999, de 13 de diciembre, sobre protección de Datos de Carácter Personal (LOPD), cuyo objeto es garantizar y proteger, en lo que concierne al tratamiento de los datos personales, las libertades y derechos fundamentales de las personas físicas, Metalistería Fulsan informa a los usuarios de que los datos introducidos en los formularios de contacto se almacenarán en un fichero automatizado propiedad de la empresa.</p>

                    <h2>4. Legislación Aplicable</h2>
                    <p>La ley aplicable en caso de disputa o conflicto de interpretación de los términos que conforman este aviso legal, así como cualquier cuestión relacionada con los servicios del presente portal, será la ley española.</p>
                </div>
            </div>
        </main>

        <?php sectionfooter(); ?>
    </div>
    <script src="js/auth.js"></script>
</body>
</html>