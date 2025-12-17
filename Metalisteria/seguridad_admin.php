<?php
// seguridad_admin.php

// 1. Iniciamos sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Lista de correos que son JEFES (Administradores)
$admins_permitidos = [
    'juan.garcia@email.com', // Pon aquí tu correo
];

// 3. RECUPERAR EL EMAIL DE LA SESIÓN
// Intentamos sacarlo de $_SESSION['usuario']['email'] (donde lo guarda el login habitualmente)
$email_actual = '';

if (isset($_SESSION['usuario']) && isset($_SESSION['usuario']['email'])) {
    $email_actual = $_SESSION['usuario']['email'];
} elseif (isset($_SESSION['email'])) {
    // Por si acaso lo guardaste suelto en alguna versión anterior
    $email_actual = $_SESSION['email'];
}

// 4. COMPROBACIONES
// A) ¿Tenemos un email identificado?
if (empty($email_actual)) {
    // No hay usuario logueado o no encontramos su email -> Al login
    header("Location: iniciarsesion.php"); 
    exit;
}

// B) ¿Su correo está en la lista de jefes?
if (!in_array($email_actual, $admins_permitidos)) {
    // Está logueado pero NO es admin -> A la web pública
    header("Location: index.php"); 
    exit;
}

// Si llega aquí, es que es admin y le dejamos pasar.
?>