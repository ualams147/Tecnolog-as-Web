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
    <title>Política de Privacidad - Metalistería Fulsan</title>
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
                <h1 class="legal-title">Política de Privacidad</h1>
                <span class="legal-date">Última actualización: 15 de Marzo de 2024</span>

                <div class="legal-body">
                    <p>En Metalistería Fulsan nos tomamos muy en serio la privacidad de tus datos. Esta Política de Privacidad describe cómo recopilamos, utilizamos y protegemos tu información personal.</p>

                    <h2>1. Responsable del Tratamiento</h2>
                    <p>El responsable del tratamiento de sus datos es <strong>Metalistería Fulsan S.L.</strong>, con domicilio en Granada. Puede contactarnos a través del email metalfulsan@gmail.com para cualquier consulta relacionada con sus datos.</p>

                    <h2>2. Finalidad del Tratamiento</h2>
                    <p>Sus datos personales serán utilizados con las siguientes finalidades:</p>
                    <ul>
                        <li>Gestión de pedidos y facturación.</li>
                        <li>Envío de presupuestos personalizados a medida.</li>
                        <li>Atención al cliente y resolución de consultas.</li>
                        <li>Envío de comunicaciones comerciales relacionadas con nuestros productos (solo si ha dado su consentimiento).</li>
                    </ul>

                    <h2>3. Legitimación</h2>
                    <p>La base legal para el tratamiento de sus datos es la ejecución del contrato de compraventa o prestación de servicios, así como el consentimiento expreso del usuario para el envío de formularios de contacto.</p>

                    <h2>4. Derechos del Usuario</h2>
                    <p>Usted tiene derecho a acceder, rectificar y suprimir sus datos, así como a oponerse al tratamiento de los mismos. Para ejercer estos derechos, envíe una solicitud por escrito a nuestra dirección de correo electrónico.</p>
                </div>
            </div>
        </main>

        <?php sectionfooter(); ?>
    </div>
    <script src="js/auth.js"></script>
</body>
</html>