<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Administrador - Metalful</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stylesAdmin.css">
</head>
<body>
    <div class="inicio-administrador">
        <!-- Cabecera -->
        <header class="cabecera">
            <div class="container">
                <div class="logo-main">
                    <img src="../imagenes/logo.png" alt="Logo Metalful">
                    <div class="logo-text">
                        <span> Metalisteria</span>
                        <strong>Fulsan</strong>
                    </div>
                </div>
                
                <nav class="nav-bar">
                    <a href="ListadoVentasAdmin.php">Ventas</a>
                    <a href="ListadoProductosAdmin.php">Productos</a>
                    <a href="ListadoClientesAdmin.php">Clientes</a>
                </nav>

                <div class="log-out">
                    <a href="../Visitante/index.php">Cerrar Sesión</a>
                </div>

            </div>
        </header>

        <!-- Título -->
        <div class="titulo-section">
            <div class="degradado"></div>
            <div class="recuadro-fondo"></div>
            
            <h1 class="titulo-principal">Resumen de ventas</h1>
            
            <div class="filtros-en-titulo">
                <!-- Filtro Fecha -->
                <div class="filtro-item">
                <label for="fecha">
                    <span class="icon">
                    <svg width="20" height="20" viewBox="0 0 40 40" fill="none">
                        <path d="M33.3333 6.66675H6.66667C4.82572 6.66675 3.33334 8.15913 3.33334 10.0001V36.6667C3.33334 38.5077 4.82572 40.0001 6.66667 40.0001H33.3333C35.1743 40.0001 36.6667 38.5077 36.6667 36.6667V10.0001C36.6667 8.15913 35.1743 6.66675 33.3333 6.66675Z" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M26.6667 3.33325V9.99992" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M13.3333 3.33325V9.99992" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3.33334 16.6667H36.6667" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    </span>
                    Fecha
                </label>
                <input type="date" id="fecha" name="fecha" />
                </div>

                <!-- Filtro Cliente -->
                <div class="filtro-item">
                <label for="cliente">
                    <span class="icon">
                    <svg width="20" height="20" viewBox="0 0 40 40" fill="none">
                        <path d="M33.3333 35V31.6667C33.3333 29.8986 32.631 28.2029 31.3807 26.9526C30.1305 25.7024 28.4348 25 26.6667 25H13.3333C11.5652 25 9.86953 25.7024 8.61929 26.9526C7.36905 28.2029 6.66667 29.8986 6.66667 31.6667V35" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M20 18.3333C23.6819 18.3333 26.6667 15.3486 26.6667 11.6667C26.6667 7.98477 23.6819 5 20 5C16.3181 5 13.3333 7.98477 13.3333 11.6667C13.3333 15.3486 16.3181 18.3333 20 18.3333Z" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    </span>
                    Cliente
                </label>
                <select id="cliente" name="cliente">
                    <option value="">Todos los clientes</option>
                    <option value="cliente1">Cliente 1</option>
                    <option value="cliente2">Cliente 2</option>
                </select>
                </div>

                <!-- Filtro Producto -->
                <div class="filtro-item">
                <label for="producto">
                    <span class="icon">
                    <svg width="20" height="20" viewBox="0 0 45 45" fill="none">
                        <path d="M37.5 15H15M37.5 22.5H15M37.5 30H15M7.5 15H7.51875M7.5 22.5H7.51875M7.5 30H7.51875" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    </span>
                    Producto
                </label>
                <select id="producto" name="producto">
                    <option value="">Todos los productos</option>
                    <option value="ventanas">Ventanas</option>
                    <option value="puertas">Puertas</option>
                </select>
                </div>
            </div>
        </div>

        <!-- Diagrama -->
        <div class="diagrama-section">
            <div class="grafica-container">
                <img src="https://figma-alpha-api.s3.us-west-2.amazonaws.com/images/43b4ec6b-81ae-2575-06d8-e6f73047a1b4" alt="Gráfica de ventas" class="grafica-image">
                <p class="grafica-title">Número de ventas en el último Mes</p>
            </div>
        </div>

        <!-- Opciones -->
        <div class="cards-grid">
            <!-- Productos -->
            <a href="ListadoProductosAdmin.php" class="card">
                <img src="../imagenes/4.png" alt="Productos" class="card-icon">
                <h3>Productos</h3>
            </a>

            <!-- Crear Producto -->
            <a href="CrearProductoAdmin.php" class="card">
                <img src="../imagenes/2.png" alt="Crear Producto" class="card-icon">
                <h3>Crear Producto</h3>
            </a>

            <!-- Clientes -->
            <a href="ListadoClientesAdmin.php" class="card">
                <img src="../imagenes/3.png" alt="Clientes" class="card-icon">
                <h3>Clientes</h3>
            </a>

            <!-- Ventas -->
            <a href="ListadoVentasAdmin.php" class="card">
                <img src="../imagenes/5.png" alt="Ventas" class="card-icon">
                <h3>Ventas</h3>
            </a>
        </div>

        
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
                        <div class="contacto-footer">
                            <h3>Contacto</h3>
                            <div class="contacto-item">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                </svg>
                                <a href="https://www.google.com/maps/place//data=!4m2!3m1!1s0xd71fd00684554b1:0xef4e70ab821a7762?sa=X&ved=1t:8290&ictx=111" target="_blank">Extrarradio Cortijo la Purisima, 2P, 18004 Granada</a>
                            </div>
                            <div class="contacto-item">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                                </svg>
                                <a href="tel:652921960">652 921 960</a>
                            </div>
                            <div class="contacto-item">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                                </svg>
                                <a href="mailto:metalfulsan@gmail.com">metalfulsan@gmail.com</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="footer-bottom">
                    <div class="politica-legal">
                        <a href="#aviso-legal">Aviso Legal</a>
                        <span>•</span>
                        <a href="#privacidad">Política de Privacidad</a>
                        <span>•</span>
                        <a href="#cookies">Política de Cookies</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

</body>
</html>
