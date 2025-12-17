<?php
// 1. INICIO DE SESIÓN Y LÓGICA DE IDIOMAS
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. IDIOMA POR DEFECTO
$lang = array(
    "conocenos" => "Conócenos",
    "productos" => "Productos",
    "carrito" => "Carrito",
    "perfil" => "Mi Perfil",
    "login" => "Iniciar Sesión",
    "logout" => "Cerrar Sesión",
    "registro" => "Registrarse",
    "enlaces" => "Enlaces rápidos",
    "contacto" => "Contacto",
    "aviso" => "Aviso Legal",
    "privacidad" => "Política de Privacidad",
    "cookies" => "Política de Cookies"
);

// 3. CAMBIO DE IDIOMA
if (isset($_GET['lang'])) {
    $_SESSION['idioma'] = $_GET['lang'];
}
$idioma_actual = isset($_SESSION['idioma']) ? $_SESSION['idioma'] : 'es';

// 4. CARGAR ARCHIVO EXTERNO
$archivo_lang = "idiomas/" . $idioma_actual . ".php";
if (file_exists($archivo_lang)) {
    include $archivo_lang;
}

// 5. MAPA DE BANDERAS
$flag_urls = [
    'es' => 'https://flagcdn.com/w80/es.png',
    'en' => 'https://flagcdn.com/w80/gb.png',
    'fr' => 'https://flagcdn.com/w80/fr.png'
];

/**
 * Función para mostrar el encabezado (Header)
 */
function sectionheader($active = 0) {
    global $lang; 
    global $idioma_actual; 
    global $flag_urls;
    
    // Calculamos cantidad del carrito
    $cantidad_carrito = 0;
    if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
        foreach ($_SESSION['carrito'] as $item) {
            $cantidad_carrito += $item['cantidad'];
        }
    }
    
    $usuario_logueado = isset($_SESSION['usuario']);
    
    // --- AQUÍ FALTABA CERRAR PHP ---
    ?>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* ================= ESTILOS BASE (ESCRITORIO) ================= */
        .cabecera { position: relative; width: 100%; background: white; padding: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000; }
        .cabecera .container { display: flex; align-items: center; justify-content: space-between; gap: 40px; flex-wrap: wrap; }
        
        .logo-main { flex-shrink: 0; position: relative; display: flex; align-items: center; z-index: 1001; }
        .logo-main img { height: 60px; width: auto; }
        .logo-text { display: flex; flex-direction: column; margin-left: 12px; margin-top: 4px; line-height: 1.1; }
        .logo-text span { font-size: 14px; font-weight: 400; color: #2b2b2b; font-family: 'Poppins', sans-serif; }
        .logo-text strong { font-size: 18px; font-weight: 700; color: #000000; text-align: center; width: 100%; font-family: 'Poppins', sans-serif; }
        .logo-link { display: flex; align-items: center; text-decoration: none; color: inherit; }

        .nav-bar { display: flex; align-items: center; justify-content: center; gap: 140px; flex: 1; font-weight: 500; font-size: 18px; color: #2b2b2b; white-space: nowrap; }
        .nav-bar a { font-family: 'Poppins', sans-serif; font-weight: 500; font-size: 18px; color: #2b2b2b; text-decoration: none; transition: color 0.3s ease; }
        .nav-bar a:hover { font-size: 20px; color: #293661; text-shadow: 0 0 4px #aab6e8; }
        .nav-bar a.activo { color: #293661; font-weight: bold; border-bottom: 2px solid #293661; }

        .sign-in { background: #293661; border-radius: 40px; padding: 12px 24px; flex-shrink: 0; cursor: pointer; transition: background-color 0.3s ease, transform 0.2s ease; }
        .sign-in:hover { background-color: #1f2849; transform: translateY(-2px); }
        .sign-in:active { transform: translateY(0); }
        .sign-in a { font-family: 'Source Sans Pro', sans-serif; font-weight: 700; font-size: 18px; color: white; text-decoration: none; white-space: nowrap; }

        /* Botón Hamburguesa */
        .menu-toggle { display: none; background: none; border: none; cursor: pointer; padding: 5px; z-index: 1002; margin-left: 10px; }
        .menu-toggle span { display: block; width: 28px; height: 3px; background-color: #293661; margin: 6px 0; transition: 0.3s; border-radius: 3px; }

        /* Widget de Idiomas */
        .lang-widget { position: fixed; bottom: 30px; left: 30px; z-index: 9999; font-family: 'Poppins', sans-serif; }
        .lang-toggle-btn { width: 50px; height: 50px; border-radius: 50%; border: 3px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.3); cursor: pointer; overflow: hidden; background: white; transition: transform 0.3s ease; padding: 0; display: flex; }
        .lang-toggle-btn img { width: 100%; height: 100%; object-fit: cover; }
        .lang-toggle-btn:hover { transform: scale(1.1); }
        .lang-options { position: absolute; bottom: 65px; left: 0; background: white; border-radius: 12px; padding: 8px 0; box-shadow: 0 5px 20px rgba(0,0,0,0.2); width: 160px; flex-direction: column; border: 1px solid #eee; opacity: 0; visibility: hidden; transform: translateY(10px); transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
        .lang-options.active { opacity: 1; visibility: visible; transform: translateY(0); }
        .lang-options a { display: flex; align-items: center; gap: 12px; padding: 10px 15px; color: #333; text-decoration: none; font-size: 14px; transition: background 0.2s; }
        .lang-options a img { width: 24px; height: 18px; border-radius: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
        .lang-options a:hover { background: #f5f7fa; color: #293661; font-weight: 600; }
        .lang-options a.selected { background: #eef2ff; color: #293661; font-weight: bold; }

        /* FOOTER BASE */
        .footer { background: #293661; color: white; padding: 60px 0 30px; }
        .footer-content { display: grid; grid-template-columns: 1fr 2fr; gap: 80px; margin-bottom: 40px; }
        .footer-logo-section { display: flex; align-items: center; justify-content: flex-start; gap: 20px; }
        .logo-footer img { width: 200px; height: 200px; object-fit: contain; }
        .redes { display: flex; gap: 20px; }
        .instagram-link { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 50%; transition: background 0.3s ease; }
        .instagram-link:hover { background: rgba(255,255,255,0.2); }
        .instagram-link svg { width: 24px; height: 24px; }
        .footer-links { display: grid; grid-template-columns: 1fr 2fr; gap: 60px; }
        .enlaces-rapidos h3, .contacto-footer h3 { font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 20px; margin-bottom: 20px; }
        .enlaces-rapidos ul { list-style: none; }
        .enlaces-rapidos ul li { margin-bottom: 12px; }
        .enlaces-rapidos ul li a { font-family: 'Poppins', sans-serif; font-weight: 400; font-size: 16px; color: white; text-decoration: none; transition: opacity 0.3s ease; }
        .enlaces-rapidos ul li a:hover { opacity: 0.8; }
        .contacto-footer ul { list-style: none; padding: 0; margin: 0; }
        .contacto-footer ul li { margin-bottom: 12px; display: flex; align-items: center; gap: 12px; }
        .contacto-footer ul li svg { width: 20px; height: 20px; fill: white; flex-shrink: 0; }
        .contacto-footer ul li a { font-family: 'Poppins', sans-serif; font-weight: 400; font-size: 16px; color: white; text-decoration: none; transition: opacity 0.3s ease; }
        .contacto-footer ul li a:hover { opacity: 0.8; }
        .footer-bottom { padding-top: 30px; border-top: 1px solid rgba(255,255,255,0.2); }
        .politica-legal { display: flex; align-items: center; justify-content: center; gap: 15px; flex-wrap: wrap; }
        .politica-legal a { font-family: 'Poppins', sans-serif; font-weight: 400; font-size: 14px; color: white; text-decoration: none; transition: opacity 0.3s ease; }
        .politica-legal a:hover { opacity: 0.8; }
        .politica-legal span { font-size: 14px; opacity: 0.5; }

        /* ================= MEDIA QUERIES (RESPONSIVE) ================= */
        
        @media (max-width: 1024px) {
            .nav-bar { gap: 40px; font-size: 16px; } 
            .cabecera .container { gap: 20px; }
        }

        @media (max-width: 768px) {
            /* --- CABECERA MÓVIL --- */
            .cabecera { padding: 15px 0; }
            .cabecera .container { gap: 0; }

            /* Orden elementos cabecera */
            .logo-main { order: 1; margin-right: auto; }
            .sign-in { order: 2; padding: 8px 16px; margin: 0; }
            .sign-in a { font-size: 14px; }
            .menu-toggle { display: block; order: 3; }
            
            .nav-bar { display: none; width: 100%; flex-direction: column; order: 4; padding-top: 20px; gap: 20px; text-align: center; border-top: 1px solid #eee; margin-top: 15px; }
            .nav-bar.active { display: flex; }
            .lang-widget { bottom: 20px; left: 20px; }

            /* --- FOOTER MÓVIL (Corrección Forzada) --- */
            
            /* 1. Quitamos la estructura Grid y ponemos Flex Vertical */
            .footer-content { 
                display: flex !important; 
                flex-direction: column !important; 
                gap: 40px !important; 
            }

            .footer-logo-section { 
                flex-direction: column !important; 
                align-items: center !important; 
            }
            .logo-footer img { width: 150px; height: 150px; }

            /* 2. Contenedor de enlaces también vertical */
            .footer-links { 
                display: flex !important; 
                flex-direction: column !important; 
                gap: 50px !important; 
                text-align: center !important;
            }

            /* 3. SUBIR SECCIÓN CONTACTO (Order -1 la sube arriba) */
            .contacto-footer {
                order: -1 !important; 
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
            }

            /* 4. Centrar los iconos y el texto del teléfono */
            .contacto-footer ul li {
                justify-content: center !important;
            }
        }
    </style>

    <div class="lang-widget" id="langWidget">
        <div class="lang-options" id="langOptions">
            <a href="?lang=es" class="<?php echo ($idioma_actual == 'es') ? 'selected' : ''; ?>">
                <img src="<?php echo $flag_urls['es']; ?>" alt="ES"> Español
            </a>
            <a href="?lang=en" class="<?php echo ($idioma_actual == 'en') ? 'selected' : ''; ?>">
                <img src="<?php echo $flag_urls['en']; ?>" alt="EN"> English
            </a>
            <a href="?lang=fr" class="<?php echo ($idioma_actual == 'fr') ? 'selected' : ''; ?>">
                <img src="<?php echo $flag_urls['fr']; ?>" alt="FR"> Français
            </a>
        </div>

        <button class="lang-toggle-btn" onclick="toggleLanguageMenu(event)" title="Cambiar idioma">
            <img src="<?php echo $flag_urls[$idioma_actual]; ?>" alt="Idioma Actual">
        </button>
    </div>

    <script>
        function toggleLanguageMenu(e) {
            e.stopPropagation(); 
            var menu = document.getElementById('langOptions');
            menu.classList.toggle('active');
        }

        function toggleMobileMenu() {
            var navbar = document.querySelector('.nav-bar');
            navbar.classList.toggle('active');
        }

        document.addEventListener('click', function(event) {
            var menuLang = document.getElementById('langOptions');
            var widgetLang = document.getElementById('langWidget');
            if (menuLang.classList.contains('active') && !widgetLang.contains(event.target)) {
                menuLang.classList.remove('active');
            }
        });
    </script>

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

            <button class="menu-toggle" onclick="toggleMobileMenu()">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <nav class="nav-bar">
                <a href="conocenos.php" class="<?php echo ($active == 2) ? 'activo' : ''; ?>"><?php echo $lang['conocenos']; ?></a>
                <a href="productos.php" class="<?php echo ($active == 3) ? 'activo' : ''; ?>"><?php echo $lang['productos']; ?></a>
                
                <a href="carrito.php" class="<?php echo ($active == 4) ? 'activo' : ''; ?>" style="display:flex; align-items:center; gap:5px; justify-content:center;">
                    <?php echo $lang['carrito']; ?>
                    <?php if ($cantidad_carrito > 0): ?>
                        <span id="cart-count" style="background:#a0d2ac; color:#293661; padding:2px 6px; border-radius:10px; font-size:12px; font-weight:bold;">
                            <?php echo $cantidad_carrito; ?>
                        </span>
                    <?php endif; ?>
                </a>

                <?php if ($usuario_logueado): ?>
                    <a href="perfil.php" class="<?php echo ($active == 6) ? 'activo' : ''; ?>"><?php echo $lang['perfil']; ?></a>
                <?php else: ?>
                    <a href="iniciarsesion.php" id="link-login" class="<?php echo ($active == 5) ? 'activo' : ''; ?>"><?php echo $lang['login']; ?></a>
                <?php endif; ?>
            </nav>

            <div class="sign-in" id="box-registro">
                <?php if ($usuario_logueado): ?>
                    <a href="logout.php" id="link-registro"><?php echo $lang['logout']; ?></a>
                <?php else: ?>
                    <a href="registro.php" id="link-registro"><?php echo $lang['registro']; ?></a>
                <?php endif; ?>
            </div>

        </div>
    </header>
    <?php
}

function sectionfooter() {
    global $lang;
    $usuario_logueado = isset($_SESSION['usuario']);
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
                        <h3><?php echo $lang['enlaces']; ?></h3>
                        <ul>
                            <?php if ($usuario_logueado): ?>
                                <li><a href="productos.php"><?php echo $lang['productos']; ?></a></li>
                                <li><a href="perfil.php"><?php echo $lang['perfil']; ?></a></li>
                                <li><a href="carrito.php"><?php echo $lang['carrito']; ?></a></li>
                            <?php else: ?>
                                <li><a href="conocenos.php"><?php echo $lang['conocenos']; ?></a></li>
                                <li><a href="productos.php"><?php echo $lang['productos']; ?></a></li>
                                <li><a href="iniciarsesion.php"><?php echo $lang['login']; ?></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="contacto-footer">
                        <h3><?php echo $lang['contacto']; ?></h3>
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
                    <a href="aviso-legal.php"><?php echo $lang['aviso']; ?></a><span>•</span>
                    <a href="privacidad.php"><?php echo $lang['privacidad']; ?></a><span>•</span>
                    <a href="cookies.php"><?php echo $lang['cookies']; ?></a>
                </div>
            </div>
        </div>
    </footer>
    <?php
}
?>