<?php
session_start();

// 0. CARGAR IDIOMA (Manual, ya que no usamos CabeceraFooter aquí)
if (!isset($_SESSION['idioma'])) {
    $_SESSION['idioma'] = 'es';
}
$archivo_lang = "idiomas/" . $_SESSION['idioma'] . ".php";
if (file_exists($archivo_lang)) {
    include $archivo_lang;
} else {
    include "idiomas/es.php";
}

// 1. CONFIGURACIÓN
// ============================================
// ¡PEGA AQUÍ TU CLAVE SECRETA DE STRIPE (sk_test_...)!
$stripeSecretKey = 'sk_test_51SeYhvDH90LRmZQXOjIyMfl8PdSbnL44hUBJCGeynVahpxb8VPUfbNB7OFiN6B4u6ntEtrZu5f30NniXbw5AzqF6006UAd7QvW'; 
// ============================================

// Detectar el dominio actual para las redirecciones (localhost o tu web)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$domain = $protocol . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

// Si eligió Bizum Manual (opcional, si no quieres usar Stripe para Bizum)
if (isset($_POST['metodo_pago']) && $_POST['metodo_pago'] === 'bizum_manual') {
    header("Location: factura.php?metodo=bizum");
    exit;
}

// 2. PREPARAR DATOS DEL CARRITO PARA STRIPE
$total = $_SESSION['total_carrito'] ?? 100.00; // Valor por defecto si falla sesión
$cantidadCentimos = intval($total * 100); // Stripe trabaja en céntimos (10€ = 1000)

// Usamos la traducción para que en Stripe salga en el idioma correcto
$nombreProducto = $lang['stripe_prod_name']; 

// 3. CONEXIÓN DIRECTA A LA API DE STRIPE (cURL)
// Esto crea una "Checkout Session"
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/checkout/sessions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_USERPWD, $stripeSecretKey . ':' . ''); // Auth Basic

// Parámetros de la compra
$postFields = http_build_query([
    'payment_method_types' => ['card'], // Puedes añadir 'bizum' si lo tienes activado en Stripe Dashboard
    'line_items' => [[
        'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => $nombreProducto,
            ],
            'unit_amount' => $cantidadCentimos,
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => $domain . '/factura.php?session_id={CHECKOUT_SESSION_ID}', // Dónde vuelve si paga bien
    'cancel_url' => $domain . '/metodopago.php?error=cancelado', // Dónde vuelve si cancela
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

$result = curl_exec($ch);

if (curl_errno($ch)) {
    die($lang['stripe_err_connection'] . curl_error($ch));
}
curl_close($ch);

$response = json_decode($result, true);

// 4. REDIRECCIÓN A LA PASARELA
if (isset($response['id']) && isset($response['url'])) {
    // Si Stripe nos da OK, enviamos al usuario a la URL de pago de Stripe
    header("Location: " . $response['url']);
    exit;
} else {
    // Si hay error (ej: clave mal puesta)
    echo "<h1>" . $lang['stripe_err_init'] . "</h1>";
    echo "<pre>"; print_r($response); echo "</pre>";
}
?>