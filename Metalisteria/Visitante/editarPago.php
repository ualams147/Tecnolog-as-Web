<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Métodos de Pago - Metalistería Fulsan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/editarPago.css">
</head>
<body>
    <div class="visitante-pago-edit">
        
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
                    <a href="carrito.php">Carrito</a>
                    <a href="IniciarSesion.php" id="link-login">Iniciar Sesión</a>
                </nav>

                <div class="sign-in" id="box-registro">
                    <a href="registro.php" id="link-registro">Registrarse</a>
                </div>
            </div>
        </header>

        <section class="payment-hero">
            <div class="container hero-content">
                <a href="perfil.php" class="btn-back">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </a>
                <h1 class="payment-title-main">Mis Métodos de Pago</h1>
            </div>
        </section>

        <main class="payment-main container">
            <div class="payment-card-container">
                
                <h2 class="section-title">Tarjetas Guardadas</h2>
                <div class="saved-cards-grid">
                    
                    <div class="credit-card-visual visa">
                        <div class="card-top">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa" class="card-logo">
                            <svg class="chip" viewBox="0 0 24 24"><path fill="#ffd700" d="M4 4h16v16H4z" opacity="0.6"/><path fill="none" stroke="#b8860b" stroke-width="2" d="M4 10h16M10 4v16M14 4v16M4 14h16"/></svg>
                        </div>
                        <div class="card-number">**** **** **** 4242</div>
                        <div class="card-bottom">
                            <div class="card-holder">
                                <span>Titular</span>
                                <strong>JUAN PÉREZ</strong>
                            </div>
                            <div class="card-expires">
                                <span>Expira</span>
                                <strong>12/25</strong>
                            </div>
                        </div>
                        <button class="btn-delete-card" title="Eliminar tarjeta">
                            <svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                        </button>
                    </div>

                    <div class="credit-card-visual mastercard">
                        <div class="card-top">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" class="card-logo">
                            <svg class="chip" viewBox="0 0 24 24"><path fill="#ffd700" d="M4 4h16v16H4z" opacity="0.6"/><path fill="none" stroke="#b8860b" stroke-width="2" d="M4 10h16M10 4v16M14 4v16M4 14h16"/></svg>
                        </div>
                        <div class="card-number">**** **** **** 8888</div>
                        <div class="card-bottom">
                            <div class="card-holder">
                                <span>Titular</span>
                                <strong>JUAN PÉREZ</strong>
                            </div>
                            <div class="card-expires">
                                <span>Expira</span>
                                <strong>08/26</strong>
                            </div>
                        </div>
                        <button class="btn-delete-card" title="Eliminar tarjeta">
                            <svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                        </button>
                    </div>

                </div>

                <h2 class="section-title mt-50">Otras Cuentas</h2>
                
                <div class="add-other-container">
                    
                    <div class="integration-card">
                        <div class="integration-header">
                            <span class="method-icon" style="color:#003087; background:#eef4ff;">PP</span>
                            <div class="integration-info">
                                <h3>PayPal</h3>
                                <p>Conecta tu cuenta para pagos rápidos.</p>
                            </div>
                        </div>
                        <button class="btn-connect">Conectar cuenta de PayPal</button>
                    </div>

                    <div class="integration-card">
                        <div class="integration-header">
                            <span class="method-icon" style="color:#e62e5d; background:#fff0f3;">BZ</span>
                            <div class="integration-info">
                                <h3>Bizum</h3>
                                <p>Vincula tu móvil para pagar al instante.</p>
                            </div>
                        </div>
                        <div class="bizum-input-group">
                            <input type="tel" placeholder="Nº de móvil" class="input-bizum">
                            <button class="btn-connect">Vincular</button>
                        </div>
                    </div>

                </div>

                <h2 class="section-title mt-50">Añadir Nueva Tarjeta</h2>
                <div class="blue-form-container">
                    <div class="blue-form-row">
                        <label>Nº de Tarjeta:</label>
                        <input type="text" placeholder="0000 0000 0000 0000" class="blue-input">
                    </div>
                    <div class="blue-form-row split">
                        <div class="blue-group">
                            <label>CVV:</label>
                            <input type="text" placeholder="123" class="blue-input short">
                        </div>
                        <div class="blue-group">
                            <label>Caducidad:</label>
                            <input type="text" placeholder="MM/AA" class="blue-input short">
                        </div>
                    </div>
                    <div class="blue-form-row">
                        <label>Titular:</label>
                        <input type="text" placeholder="Como aparece en la tarjeta" class="blue-input">
                    </div>
                </div>

                <div class="page-actions">
                    <a href="perfil.php" class="btn-round-white">Salir sin guardar</a>
                    <button class="btn-round-white">Guardar Cambios</button>
                </div>

            </div>
        </main>

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
                                    <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" /></svg>
                                    <a href="https://www.google.com/maps/place//data=!4m2!3m1!1s0xd71fd00684554b1:0xef4e70ab821a7762?sa=X&ved=1t:8290&ictx=111" target="_blank">Extrarradio Cortijo la Purisima, 2P, 18004 Granada</a>
                                </li>
                                <li>
                                    <svg viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" /></svg>
                                    <a href="tel:652921960">652 921 960</a>
                                </li>
                                <li>
                                    <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" /></svg>
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

    <script src="../js/auth.js"></script>
</body>
</html>