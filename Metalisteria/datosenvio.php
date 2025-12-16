<?php
include 'CabeceraFooter.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Envío - Metalistería Fulsan</title>
    <link rel="icon" type="image/png" href="imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/datosEnvio.css">
</head>
<body>
    <div class="visitante-envio">
        
        <?php sectionheader(); ?>

        <section class="steps-section">
            <div class="container">
                <div class="steps-container">
                    <div class="step active">
                        <span class="step-number">Paso 1</span>
                        <span class="step-label">Datos de envio</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step">
                        <span class="step-number">Paso 2</span>
                        <span class="step-label">Método de Pago</span>
                    </div>
                    <div class="step-line"></div>
                    <div class="step">
                        <span class="step-number">Paso 3</span>
                        <span class="step-label">Factura de Compra</span>
                    </div>
                </div>
            </div>
        </section>

        <main class="envio-main container">
            
            <div class="envio-card">
                <h1 class="page-title">Datos de envio</h1>

                <form class="envio-form">
                    
                    <div class="form-row">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>

                    <div class="form-row">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-row">
                        <label for="telefono">Teléfono:</label>
                        <input type="tel" id="telefono" name="telefono" required>
                    </div>

                    <div class="form-row">
                        <label for="calle">Calle:</label>
                        <input type="text" id="calle" name="calle" placeholder="Ej: Calle Recogidas" required>
                    </div>

                    <div class="form-row">
                        <label for="numero">Nº / Piso:</label>
                        <div class="input-group">
                            <input type="text" id="numero" name="numero" placeholder="Ej: 12" class="input-small" required>
                            <input type="text" id="piso" name="piso" placeholder="Ej: 3º A, Esc. Dcha" class="input-large">
                        </div>
                    </div>

                    <div class="form-row">
                        <label for="cp">Código Postal / Localidad:</label>
                        <div class="input-group">
                            <input type="text" id="cp" name="cp" placeholder="Ej: 18001" class="input-small" required>
                            <input type="text" id="localidad" name="localidad" value="Granada" class="input-large">
                        </div>
                    </div>

                    <div class="form-row notes-row">
                        <label for="notas">Notas para el repartidor:</label>
                        <textarea id="notas" name="notas"></textarea>
                    </div>

                    <div class="form-actions">
                        <a href="carrito.php" class="btn-action btn-back">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                            </svg>
                            Volver atrás
                        </a>

                        <a href="metodoPago.php" class="btn-action btn-next">
                            Continuar con el pago
                        </a>
                    </div>

                </form>
            </div>
        </main>

        <?php sectionfooter(); ?>
    </div>
    <script src="js/auth.js"></script>
</body>
</html>