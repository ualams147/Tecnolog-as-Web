<?php
// seguridad_admin.php

// 1. Iniciamos sesión si no está iniciada ya
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Lista de correos que son Administradores
$admins_permitidos = [
    'juan.garcia@email.com',

];

// 3. COMPROBACIONES
// A) ¿Está logueado?
if (!isset($_SESSION['email'])) {
    header("Location: iniciarsesion.php"); // Si no está logueado, al login
    exit;
}

// B) ¿Es admin?
if (!in_array($_SESSION['email'], $admins_permitidos)) {
    // Está logueado pero es un cliente normal -> Fuera
    header("Location: index.php"); 
    exit;
}
?>