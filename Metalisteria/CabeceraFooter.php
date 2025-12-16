<?php
// Evitamos iniciar sesión si ya está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Función para mostrar el encabezado (Header)
 * $active: Número de la opción del menú activa
 * 1 = Inicio, 2 = Conócenos, 3 = Productos, 4 = Carrito, 5 = Login
 */
function sectionheader($active = 0) {
    // Calculamos cantidad del carrito
    $cantidad_carrito = 0;
    if (isset($_SESSION['carrito'])) {
        foreach ($_SESSION['carrito'] as $item) {
            $cantidad_carrito += $item['cantidad'];
        }
    }
    ?>

    <style>
        /* ============================================================
        ========================== CABECERA =========================
        ============================================================ */

        .cabecera {
            position: relative;
            width: 100%;
            background: white;
            padding: 30px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

            .cabecera .container {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 40px;
            }

        /* ======================= LOGO (adaptado) ======================= */

        .logo-main {
            flex-shrink: 0;
            position: relative;
            display: flex;
            align-items: center;
        }

            .logo-main img {
                height: 60px;
                width: auto;
            }

        /* === LOGO TEXT AÑADIDO === */
        .logo-text {
            display: flex;
            flex-direction: column;
            margin-left: 12px;
            margin-top: 4px;
            line-height: 1.1;
        }

            .logo-text span {
                font-size: 14px;
                font-weight: 400;
                color: #2b2b2b;
                font-family: 'Poppins', sans-serif;
            }

            .logo-text strong {
                font-size: 18px;
                font-weight: 700;
                color: #000000;
                text-align: center;
                width: 100%;
                font-family: 'Poppins', sans-serif;
            }

            .logo-link {
                display: flex;              /* mantiene logo + texto juntos */
                align-items: center;
                text-decoration: none;      /* quita el subrayado */
                color: inherit;             /* mantiene el color del texto */
            }
        /* ========================================= */

        /* ======================= NAV BAR ======================= */

        .nav-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 140px;
            flex: 1;
            font-weight: 500;
            font-size: 18px;
            color: #2b2b2b;
            white-space: nowrap;
        }

            .nav-bar a {
                font-family: 'Poppins', sans-serif;
                font-weight: 500;
                font-size: 18px;
                color: #2b2b2b;
                text-decoration: none;
                transition: color 0.3s ease;
            }

                .nav-bar a:hover {
                    font-size: 20px;
                    color: #293661;
                    text-shadow: 0 0 4px #aab6e8; /* azul muy claro y suave */
                }

                /* Estilo para la página actual */
                .nav-bar a.activo {
                    color: #293661;           /* El azul corporativo */
                    font-weight: bold;         /* Letra un poco más gruesa */
                    border-bottom: 2px solid #293661; /* El subrayado (grosor y color) */      
                }

        /* ======================= SIGN IN ======================= */

        .sign-in {
            background: #293661;
            border-radius: 40px;
            padding: 12px 24px;
            flex-shrink: 0;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

            .sign-in:hover {
                background-color: #1f2849;
                transform: translateY(-2px);
            }

            .sign-in:active {
                transform: translateY(0);
            }

            .sign-in a {
                font-family: 'Source Sans Pro', sans-serif;
                font-weight: 700;
                font-size: 18px;
                color: white;
                text-decoration: none;
                white-space: nowrap;
            }

        /* ============ FOOTER ============ */

        .footer {
            background: #293661;
            color: white;
            padding: 60px 0 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 80px;
            margin-bottom: 40px;
        }

        .footer-logo-section {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 20px; /* separación entre logo e icono */
        }


        .logo-footer img {
            width: 200px;
            height: 200px;
            object-fit: contain;
        }

        .redes {
            display: flex;
            gap: 20px;
        }

        .instagram-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transition: background 0.3s ease;
        }

            .instagram-link:hover {
                background: rgba(255,255,255,0.2);
            }

            .instagram-link svg {
                width: 24px;
                height: 24px;
            }

        .footer-links {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 60px;
        }

        .enlaces-rapidos h3,
        .contacto-footer h3 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 20px;
            margin-bottom: 20px;
        }

        .enlaces-rapidos ul {
            list-style: none;
        }

            .enlaces-rapidos ul li {
                margin-bottom: 12px;
            }

                .enlaces-rapidos ul li a {
                    font-family: 'Poppins', sans-serif;
                    font-weight: 400;
                    font-size: 16px;
                    color: white;
                    text-decoration: none;
                    transition: opacity 0.3s ease;
                }

                    .enlaces-rapidos ul li a:hover {
                        opacity: 0.8;
                    }

        .contacto-footer ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

            .contacto-footer ul li {
                margin-bottom: 12px; /* igual que enlaces rápidos */
                display: flex;
                align-items: center;
                gap: 12px; /* separación entre icono y texto */
            }

                .contacto-footer ul li svg {
                    width: 20px;
                    height: 20px;
                    fill: white; /* fuerza el color visible */
                    flex-shrink: 0;
                }

                .contacto-footer ul li a {
                    font-family: 'Poppins', sans-serif;
                    font-weight: 400;
                    font-size: 16px;
                    color: white;
                    text-decoration: none;
                    transition: opacity 0.3s ease;
                }

                    .contacto-footer ul li a:hover {
                        opacity: 0.8;
                    }

            
        .footer-bottom {
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.2);
        }

        .politica-legal {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

            .politica-legal a {
                font-family: 'Poppins', sans-serif;
                font-weight: 400;
                font-size: 14px;
                color: white;
                text-decoration: none;
                transition: opacity 0.3s ease;
            }

                .politica-legal a:hover {
                    opacity: 0.8;
                }

            .politica-legal span {
                font-size: 14px;
                opacity: 0.5;
            }
    </style>


    <header class="cabecera">
        <div class="container">
            <div class="logo-main">
                <a href="index.php" class="logo-link">
                    <img src="imagenes/logo.png" alt="Logo Metalful">
                    <div class="logo-text">
                        <span>Metalistería</span>
                        <strong>Fulsan</strong>
                    </div>
                </a>
            </div>

            <nav class="nav-bar">
                <a href="conocenos.php" class="<?php echo ($active == 2) ? 'activo' : ''; ?>">Conócenos</a>
                <a href="productos.php" class="<?php echo ($active == 3) ? 'activo' : ''; ?>">Productos</a>
                
                <a href="carrito.php" class="<?php echo ($active == 4) ? 'activo' : ''; ?>" style="display:flex; align-items:center; gap:5px;">
                    Carrito
                    <?php if ($cantidad_carrito > 0): ?>
                        <span id="cart-count" style="background:#a0d2ac; color:#293661; padding:2px 6px; border-radius:10px; font-size:12px; font-weight:bold;">
                            <?php echo $cantidad_carrito; ?>
                        </span>
                    <?php endif; ?>
                </a>

                <?php if (isset($_SESSION['usuario'])): ?>
                    <a href="perfil.php" class="<?php echo ($active == 6) ? 'activo' : ''; ?>">Mi Perfil</a>
                <?php else: ?>
                    <a href="iniciarsesion.php" id="link-login" class="<?php echo ($active == 5) ? 'activo' : ''; ?>">Iniciar Sesión</a>
                <?php endif; ?>
            </nav>

            <div class="sign-in" id="box-registro">
                <?php if (!isset($_SESSION['usuario'])): ?>
                    <a href="registro.php" id="link-registro">Registrarse</a>
                <?php else: ?>
                    <a href="logout.php" id="link-registro">Cerrar Sesión</a>
                <?php endif; ?>
            </div>

        </div>
    </header>
    
    <?php include 'aviso-cookies.php'; ?>
    <?php
}

/**
 * Función para mostrar el pie de página (Footer)
 */
function sectionfooter() {
    ?>
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo-section">
                    <div class="logo-footer">
                        <img src="imagenes/footer.png" alt="Logo Metalful">
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
                            <?php if (isset($_SESSION['usuario'])): ?>
                                <li><a href="productos.php">Productos</a></li>
                                <li><a href="perfil.php">Mi Perfil</a></li>
                                <li><a href="carrito.php">Carrito</a></li>
                                <li><a href="HistorialPedidos.php">Historial de pedidos</a></li>
                            <?php else: ?>
                                <li><a href="conocenos.php">Conócenos</a></li>
                                <li><a href="productos.php">Productos</a></li>
                                <li><a href="iniciarsesion.php">Iniciar Sesión</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="contacto-footer">
                        <h3>Contacto</h3>
                        <ul>
                            <li>
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                <a href="https://www.google.com/maps/place//data=!4m2!3m1!1s0xd71fd00684554b1:0xef4e70ab821a7762?sa=X&ved=1t:8290&ictx=111" target="_blank">Extrarradio Cortijo la Purisima, 2P, 18004 Granada</a>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                                <a href="tel:652921960">652 921 960</a>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                                <a href="mailto:metalfulsan@gmail.com">metalfulsan@gmail.com</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="politica-legal">
                    <a href="aviso-legal.php">Aviso Legal</a><span>•</span>
                    <a href="privacidad.php">Política de Privacidad</a><span>•</span>
                    <a href="cookies.php">Política de Cookies</a>
                </div>
            </div>
        </div>
    </footer>
    <?php
}
?>