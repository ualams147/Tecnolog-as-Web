<?php
// Nombre del archivo: enviar_presupuesto.php

// 0. CARGAR SESIÓN E IDIOMA (Necesario para traducir los mensajes)
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

header('Content-Type: application/json');

// 1. RECIBIMOS LOS DATOS DEL FORMULARIO
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    // 2. CONFIGURACIÓN DEL CORREO
    // Cambia este correo por el tuyo real
    $destinatario = "metalfulsan@gmail.com"; 
    $asunto = $lang['mail_asunto'];

    // 3. Construimos el mensaje que te llegará a ti
    $mensaje = $lang['mail_intro'] . "\n\n";
    $mensaje .= "-------------------------------------\n";
    $mensaje .= $lang['mail_prod'] . " " . $data['producto'] . "\n";
    $mensaje .= $lang['mail_color'] . " " . $data['color'] . "\n";
    $mensaje .= $lang['mail_medida'] . " " . $data['medida'] . "\n";
    $mensaje .= $lang['mail_detalles'] . "\n" . $data['detalles'] . "\n";
    $mensaje .= "-------------------------------------\n\n";
    $mensaje .= $lang['mail_fecha'] . " " . date('d/m/Y H:i:s');

    // 4. Cabeceras (Para evitar que vaya a Spam)
    $headers = "From: no-reply@metalfulsan.com\r\n";
    $headers .= "Reply-To: no-reply@metalfulsan.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // 5. Intentamos enviar el correo
    if (mail($destinatario, $asunto, $mensaje, $headers)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $lang['mail_err_envio']]);
    }
} else {
    echo json_encode(['success' => false, 'error' => $lang['mail_err_datos']]);
}
?>