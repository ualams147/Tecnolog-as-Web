<?php

$servidor = "localhost";   // Servidor local de XAMPP
$usuario = "root";        // Usuario por defecto de MySQL en XAMPP
$password = "";            // Contraseña vacía en XAMPP
$base_datos = "metalisteria";  // Nombre de tu base de datos

// Crear conexión
try {
    $conn = new PDO("mysql:host=$servidor;dbname=$base_datos;charset=utf8", $usuario, $password);
    //Configurar para que nos avise si hay errores SQL
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}



?>