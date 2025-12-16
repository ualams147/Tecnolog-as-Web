<style>
    .cookie-banner {
        position: fixed;
        bottom: -100%; /* Empieza oculto */
        left: 0;
        width: 100%;
        background-color: #293661; /* Azul Fulsan */
        color: white;
        padding: 20px;
        box-shadow: 0 -4px 10px rgba(0,0,0,0.3);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 15px;
        transition: bottom 0.5s ease-in-out;
        font-family: 'Poppins', sans-serif; /* Fuente global del banner */
    }

    .cookie-content {
        display: flex;
        align-items: center;
        gap: 20px;
        max-width: 1200px;
        width: 100%;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .cookie-text {
        font-size: 14px;
        line-height: 1.5;
        flex: 1;
        min-width: 250px;
    }

    .cookie-text p {
        margin: 0; /* Quitar márgenes por defecto */
    }

    .cookie-text a {
        color: #a0d2ac;
        text-decoration: underline;
    }

    .cookie-buttons {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    /* BOTÓN ACEPTAR */
    .btn-cookie-accept {
        background-color: white;
        color: #293661;
        border: none;
        padding: 10px 25px;
        border-radius: 5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Poppins', sans-serif; /* <--- FUENTE FORZADA */
        font-size: 14px;
    }

    .btn-cookie-accept:hover {
        background-color: #f0f0f0;
        transform: scale(1.05);
    }

    /* BOTÓN RECHAZAR */
    .btn-cookie-reject {
        background-color: transparent;
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.6);
        padding: 8px 20px;
        border-radius: 5px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Poppins', sans-serif; /* <--- FUENTE FORZADA */
        font-size: 14px;
    }

    .btn-cookie-reject:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: white;
    }

    .cookie-banner.mostrar {
        bottom: 0;
    }

    .cookie-icon svg {
        width: 40px;
        height: 40px;
        stroke: white; /* Color del borde del icono */
    }

    @media (max-width: 768px) {
        .cookie-content {
            flex-direction: column;
            text-align: center;
        }
        .cookie-buttons {
            width: 100%;
            justify-content: center;
            flex-direction: column-reverse;
            gap: 10px;
        }
        .btn-cookie-accept, .btn-cookie-reject {
            width: 100%;
        }
    }
</style>

<div id="cookie-banner" class="cookie-banner">
    <div class="cookie-content">
        
        <div class="cookie-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                <polyline points="9 12 11 14 15 10"></polyline>
            </svg>
        </div>

        <div class="cookie-text">
            <p><strong>Privacidad y Cookies.</strong></p>
            <p>Utilizamos cookies propias para mejorar tu experiencia. Puedes aceptarlas o rechazar su uso. Lee nuestra <a href="cookies.php">política de cookies</a>.</p>
        </div>
        
        <div class="cookie-buttons">
            <button class="btn-cookie-reject" onclick="gestionarCookies(false)">Rechazar</button>
            <button class="btn-cookie-accept" onclick="gestionarCookies(true)">Aceptar todo</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const decisionTomada = localStorage.getItem('metalfulsan_cookies_decision');
        
        if (!decisionTomada) {
            setTimeout(function() {
                document.getElementById('cookie-banner').classList.add('mostrar');
            }, 1000);
        }
    });

    function gestionarCookies(aceptadas) {
        localStorage.setItem('metalfulsan_cookies_decision', 'tomada');
        localStorage.setItem('metalfulsan_cookies_aceptadas', aceptadas);
        document.getElementById('cookie-banner').classList.remove('mostrar');
    }
</script>