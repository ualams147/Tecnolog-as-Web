<?php
// Nombre del archivo: enviar_presupuesto.php
header('Content-Type: application/json');

// 1. Recibimos los datos enviados por el Javascript
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    // 2. CONFIGURACIÓN DEL CORREO
    // Cambia este correo por el tuyo real
    $destinatario = "metalfulsan@gmail.com"; 
    $asunto = "Nuevo Presupuesto Web - Producto a Medida";

    // 3. Construimos el mensaje que te llegará a ti
    $mensaje = "Has recibido una nueva solicitud de presupuesto desde la web:\n\n";
    $mensaje .= "-------------------------------------\n";
    $mensaje .= "Producto: " . $data['producto'] . "\n";
    $mensaje .= "Color: " . $data['color'] . "\n";
    $mensaje .= "Medidas: " . $data['medida'] . "\n";
    $mensaje .= "Detalles adicionales:\n" . $data['detalles'] . "\n";
    $mensaje .= "-------------------------------------\n\n";
    $mensaje .= "Fecha de solicitud: " . date('d/m/Y H:i:s');

    // 4. Cabeceras (Para evitar que vaya a Spam)
    $headers = "From: no-reply@metalfulsan.com\r\n";
    $headers .= "Reply-To: no-reply@metalfulsan.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // 5. Intentamos enviar el correo
    if (mail($destinatario, $asunto, $mensaje, $headers)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo enviar el email.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No llegaron datos.']);
}