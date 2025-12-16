<?php
// 0. CARGAR IDIOMA
session_start();

if (!isset($_SESSION['idioma'])) {
    $_SESSION['idioma'] = 'es';
}

$archivo_lang = "idiomas/" . $_SESSION['idioma'] . ".php";
if (file_exists($archivo_lang)) {
    include $archivo_lang;
} else {
    include "idiomas/es.php";
}

// Recogemos el teléfono de la URL. Si no viene, ponemos "6XX XXX XXX" de ejemplo.
$telefono = isset($_GET['movil']) ? $_GET['movil'] : '6XX XXX XXX';
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['idioma']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['bizum_titulo_pag']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f2f4f7; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .bizum-card { 
            background: white; 
            width: 100%; 
            max-width: 420px; 
            padding: 50px 30px; 
            border-radius: 20px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.08); 
            text-align: center; 
            position: relative;
            overflow: hidden; /* Evita desbordamientos */
        }
        
        /* Barra superior decorativa */
        .bizum-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: linear-gradient(90deg, #00bfd3, #008a99);
        }

        /* --- LOGO BIZUM CORREGIDO (SVG TEXTO) --- */
        .logo-container {
            margin-bottom: 30px;
        }
        .bizum-logo-svg {
            width: 140px;
            height: auto;
        }

        h2 { 
            color: #1a1a1a; 
            font-size: 22px; 
            margin-bottom: 10px; 
            font-weight: 700; 
        }
        
        p { 
            color: #666; 
            font-size: 15px; 
            margin-bottom: 25px; 
            line-height: 1.5; 
        }
        
        /* Caja del teléfono destacado */
        .phone-box {
            background-color: #eefbfc;
            color: #007c89;
            font-family: 'Nunito', sans-serif;
            font-size: 20px;
            font-weight: 800;
            padding: 15px 25px;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 35px;
            letter-spacing: 1px;
            border: 1px dashed #00bfd3;
        }

        /* Spinner grande y limpio */
        .spinner { 
            margin: 0 auto 25px auto; 
            width: 55px; 
            height: 55px; 
            border: 5px solid #ecf0f1; 
            border-top: 5px solid #00bfd3; 
            border-radius: 50%; 
            animation: spin 1.2s cubic-bezier(0.68, -0.55, 0.27, 1.55) infinite; 
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        .status-msg { 
            font-size: 17px; 
            color: #4a4a4a; 
            margin-bottom: 30px; 
            font-weight: 600;
            min-height: 27px;
            transition: all 0.3s ease;
        }
        
        .secure-footer { 
            border-top: 1px solid #eee;
            padding-top: 20px;
            color: #999; 
            font-size: 13px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 8px; 
        }
    </style>
</head>
<body>

    <div class="bizum-card">
        
        <div class="logo-container">
            <svg class="bizum-logo-svg" viewBox="0 0 120 40" xmlns="http://www.w3.org/2000/svg">
                <text x="50%" y="30" font-family="'Nunito', sans-serif" font-weight="900" font-size="38" fill="#00bfd3" text-anchor="middle">bizum</text>
            </svg>
        </div>

        <h2><?php echo $lang['bizum_h2']; ?></h2>
        <p><?php echo $lang['bizum_desc']; ?></p>
        
        <div class="phone-box"><?php echo htmlspecialchars($telefono); ?></div>

        <div class="spinner" id="loader"></div>
        
        <div class="status-msg" id="mensaje"><?php echo $lang['bizum_estado_inicial']; ?></div>
        
        <div class="secure-footer">
            <span>🔒</span> <?php echo $lang['bizum_footer_seguridad']; ?>
        </div>
    </div>

    <script>
        const msg = document.getElementById('mensaje');
        const loader = document.getElementById('loader');

        // SECUENCIA DE TIEMPOS (Total: 10 segundos)

        // 1. A los 3 segundos: Simulamos espera del usuario
        setTimeout(() => {
            msg.innerText = "<?php echo $lang['bizum_js_esperando']; ?>";
            msg.style.color = "#00bfd3";
        }, 3000);

        // 2. A los 7 segundos: Verificando
        setTimeout(() => {
            msg.innerText = "<?php echo $lang['bizum_js_verificando']; ?>";
            msg.style.color = "#666";
        }, 7000);

        // 3. A los 9 segundos: ÉXITO
        setTimeout(() => {
            msg.innerText = "<?php echo $lang['bizum_js_exito']; ?>";
            msg.style.color = "#28a745"; // Verde
            msg.style.transform = "scale(1.1)";
            
            // Cambiamos el color del spinner a verde
            loader.style.borderTopColor = "#28a745"; 
            loader.style.borderRightColor = "#28a745"; 
            loader.style.borderBottomColor = "#28a745"; 
            loader.style.borderLeftColor = "#28a745"; 
            
            // Cambiamos el borde del teléfono a verde
            document.querySelector('.phone-box').style.borderColor = "#28a745";
            document.querySelector('.phone-box').style.color = "#28a745";
            document.querySelector('.phone-box').style.backgroundColor = "#e8f8ed";
        }, 9000);

        // 4. A los 11 segundos (2 segs después del éxito): REDIRECCIÓN
        setTimeout(() => {
            window.location.href = "factura.php?metodo=bizum";
        }, 11000);
    </script>
</body>
</html>