<?php
include '../CabeceraFooter.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Visitante - Metalful</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="inicio-visitante">

        <?php sectionheader(1); ?>

        <!-- Portada -->
        <section class="portada">
            <div class="portada-image">
                <img src="../imagenes/principal.png" alt="Taller de metalurgia">
            </div>
            <div class="portada-overlay"></div>
            <h1 class="portada-title">Metalistería con más de 30 años de experiencia en el sector</h1>
        </section>

        <!-- Main Content -->
        <main class="main-content">

            <!-- Ubicación -->
            <section class="ubicacion">
                <div class="container">
                    <div class="ubicacion-content">
                        <div class="texto-ubicacion">
                            <h2>Donde nos encontramos</h2>
                            <div class="direccion-box">
                                <a href="https://www.google.com/maps/place//data=!4m2!3m1!1s0xd71fd00684554b1:0xef4e70ab821a7762?sa=X&ved=1t:8290&ictx=111" target="_blank" class="direccion-link icono-link">
                                    <div class="location-icon">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                        </svg>
                                    </div>
                                    Extrarradio Cortijo la Purisima, 2P, 18004 Granada
                                </a>
                            </div>

                            <div class="contacto-info">
                                <p>Si tiene problemas para encontrarnos llame a este número:</p>
                                <a href="tel:652921960" class="telefono-btn">652 921 960</a>
                            </div>
                        </div>

                        <div class="mapa-container">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3179.6663429428845!2d-3.619986189773082!3d37.16063247203042!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd71fd00684554b1%3A0xef4e70ab821a7762!2sMetalister%C3%ADa%20Fulsan%20SL!5e0!3m2!1ses!2ses!4v1763111584348!5m2!1ses!2ses" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Nuestros Productos -->
            <section class="nuestros-productos">
                <div class="productos-container">
                    <h2>Nuestros Productos</h2>

                    <div class="productos-grid">
                        <div class="producto-card">
                            <img src="../imagenes/puertaAzul.png" alt="Producto 1">
                        </div>
                        <div class="producto-card">
                            <img src="../imagenes/puertaMetalica.png" alt="Puerta metálica">
                        </div>
                        <div class="producto-card">
                            <img src="../imagenes/escalera.png" alt="Barandilla">
                        </div>
                    </div>

                    <button class="ver-mas-btn">Ver más</button>
                </div>
            </section>

            <!-- Preguntas Frecuentes -->
            <section class="preguntas-frecuentes">
                <div class="container">

                    

                    <div class="faq-content">
                        <h2>Preguntas Frecuentes</h2>

                        <!-- IMÁGENES -->
                        <div class="faq-images">
                            <img src="../imagenes/rojo.png" alt="Trabajo metálico" class="faq-img-1">
                            <img src="../imagenes/blanco.png" alt="Detalle metálico" class="faq-img-2">
                        </div>

                        <!-- PREGUNTAS -->
                        <div class="faq-questions">

                            <!-- ITEM 1 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Cuánto tarda el producto en llegar?</span>
                                    <svg class="arrow-icon" viewBox="0 0 47 40">
                                        <path d="M11.75 15L23.5 26.75L35.25 15L11.75 15Z" fill="currentColor" />
                                    </svg>
                                </div>
                                <div class="faq-answer">
                                    <p>El tiempo de entrega suele ser entre 3 y 7 días según el tipo de producto.</p>
                                </div>
                            </div>

                            <!-- ITEM 2 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Cuál es el rango de distribución de los productos?</span>
                                    <svg class="arrow-icon" viewBox="0 0 47 40">
                                        <path d="M11.75 15L23.5 26.75L35.25 15L11.75 15Z" fill="currentColor" />
                                    </svg>
                                </div>
                                <div class="faq-answer">
                                    <p>Hacemos entregas en toda la provincia de Granada y zonas cercanas.</p>
                                </div>
                            </div>

                            <!-- ITEM 3 -->
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>¿Cómo puede contactarnos?</span>
                                    <svg class="arrow-icon" viewBox="0 0 47 40">
                                        <path d="M11.75 15L23.5 26.75L35.25 15L11.75 15Z" fill="currentColor" />
                                    </svg>
                                </div>
                                <div class="faq-answer">
                                    <p>Puede llamarnos al 652 921 960 o escribirnos a metalfulsan@gmail.com.</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>

        </main>

    <?php sectionfooter(); ?>

    <script src="../js/faq.js"></script>
    <script src="../js/auth.js"></script>
</body>
</html>
