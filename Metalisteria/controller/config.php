<?php
require 'database.class.php';

$expireTime = 30; // tiempo de expiración de la sesión en minutos

$session_name = "fulsan"; // nombre de la sesion

$mysqli=Db::getInstance();

// Solo iniciar sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_name('fulsan');
    
    // Detectar si estamos en HTTPS o HTTP
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
                || $_SERVER['SERVER_PORT'] == 443
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isSecure,  // Solo secure en HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}