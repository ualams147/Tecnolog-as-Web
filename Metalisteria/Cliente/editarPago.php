<?php
include '../CabeceraFooter.php'; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Métodos de Pago - Metalistería Fulsan</title>
    <link rel="icon" type="image/png" href="../imagenes/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/editarPago.css">
</head>
<body>
    <div class="visitante-pago-edit">
        
        <?php sectionheader(6); ?>
        
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

        <?php sectionfooter(); ?>
    </div>

    <script src="../js/auth.js"></script>
</body>
</html>