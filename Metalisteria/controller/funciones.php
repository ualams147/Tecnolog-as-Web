<?php
// Evitamos iniciar sesión si ya está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Función para mostrar el encabezado (Header)
 * $active: Número de la opción del menú activa
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
    <div class="nav-container">
        <nav class="navbar">
            <div class="logo">
                <a href="../index.php">
                    <img src="../imagenes/logo.png" alt="Metalistería Fulsan" style="height: 50px;">
                </a>
            </div>
            <ul class="nav-links">
                <li><a href="../index.php" class="<?php echo ($active == 1) ? 'active' : ''; ?>">Inicio</a></li>
                <li><a href="conocenos.php" class="<?php echo ($active == 2) ? 'active' : ''; ?>">Conócenos</a></li>
                <li><a href="productos.php" class="<?php echo ($active == 3) ? 'active' : ''; ?>">Productos</a></li>
                <li><a href="contacto.php" class="<?php echo ($active == 4) ? 'active' : ''; ?>">Contacto</a></li>
            </ul>
            <div class="nav-icons">
                <a href="carrito.php" class="cart-icon">
                    🛒 <span id="cart-count"><?php echo $cantidad_carrito; ?></span>
                </a>
                <?php if (isset($_SESSION['usuario'])): ?>
                    <a href="logout.php">Salir</a>
                <?php else: ?>
                    <a href="IniciarSesion.php">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
    
    <style>
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .nav-links { list-style: none; display: flex; gap: 20px; }
        .nav-links a { text-decoration: none; color: #333; font-weight: 500; }
        .nav-links a.active { color: #a0d2ac; font-weight: 700; }
    </style>
    <?php
}

/**
 * Función para mostrar el pie de página (Footer)
 */
function sectionfooter() {
    ?>
    <footer class="footer" style="background: #333; color: white; padding: 20px; margin-top: 50px;">
        <div class="container" style="text-align: center;">
            <p>&copy; <?php echo date('Y'); ?> Metalistería Fulsan. Todos los derechos reservados.</p>
        </div>
    </footer>
    <?php
}
?>