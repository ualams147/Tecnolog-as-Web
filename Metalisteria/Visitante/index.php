<?php
session_start();
include '../conexion.php'; // Asegúrate de que la ruta a conexion.php sea correcta

// LÓGICA DEL CONTADOR (Badge rojo)
$total_items = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $total_items += $item['cantidad'];
    }
}
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

        <!-- Cabecera -->
        <header class="cabecera">
            <div class="container">
                <div class="logo-main">
                    <a href="index.php" class="logo-link">
                        <img src="../imagenes/logo.png" alt="Logo Metalful">
                        <div class="logo-text">
                            <span>Metalistería</span>
                            <strong>Fulsan</strong>
                        </div>
                    </a>
                </div>

                <nav class="nav-bar">
                    <a href="conocenos.php">Conócenos</a>
                    <a href="productos.php">Productos</a>
                    <a href="carrito.php">
                        Carrito 
                        <?php if($total_items > 0): ?>
                            <span style="background: #e74c3c; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; position: relative; top: -2px;">
                                <?php echo $total_items; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <a href="IniciarSesion.php" id="link-login">Iniciar Sesión</a>
                </nav>

                <div class="sign-in" id="box-registro">
                    <a href="registro.php" id="link-registro">Registrarse</a>
                </div>
            </div>
        </header>

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

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-logo-section">
                        <div class="logo-footer">
                            <img src="../imagenes/footer.png" alt="Logo Metalful">
                        </div>
                        <div class="redes">
                            <a href="https://www.instagram.com/metalfulsansl/" target="_blank" class="instagram-link">
                                <svg viewBox="0 0 24 24" fill="white">
                                    <path d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="footer-links">
                        <div class="enlaces-rapidos">
                            <h3>Enlaces rápidos</h3>
                            <ul>
                                <li><a href="conocenos.php">Conócenos</a></li>
                                <li><a href="productos.php">Productos</a></li>
                                <li><a href="IniciarSesion.php">Iniciar Sesión</a></li>
                            </ul>
                        </div>

                        <div class="contacto-footer">
                            <h3>Contacto</h3>
                            <ul>
                                <li>
                                    <svg viewBox="0 0 24 24">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                    </svg>
                                    <a href="https://www.google.com/maps/place//data=!4m2!3m1!1s0xd71fd00684554b1:0xef4e70ab821a7762?sa=X&ved=1t:8290&ictx=111" target="_blank">
                                        Extrarradio Cortijo la Purisima, 2P, 18004 Granada
                                    </a>
                                </li>
                                <li>
                                    <svg viewBox="0 0 24 24">
                                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                                    </svg>
                                    <a href="tel:652921960">652 921 960</a>
                                </li>
                                <li>
                                    <svg viewBox="0 0 24 24">
                                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                                    </svg>
                                    <a href="mailto:metalfulsan@gmail.com">metalfulsan@gmail.com</a>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>

                <div class="footer-bottom">
                    <div class="politica-legal">
                        <a href="aviso-legal.php">Aviso Legal</a>
                        <span>•</span>
                        <a href="privacidad.php">Política de Privacidad</a>
                        <span>•</span>
                        <a href="cookies.php">Política de Cookies</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="../js/faq.js"></script>
    <script src="../js/auth.js"></script>
</body>
</html>
