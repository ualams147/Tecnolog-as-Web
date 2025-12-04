<?php

$servername = "localhost";   // Servidor local de XAMPP
$username   = "root";        // Usuario por defecto de MySQL en XAMPP
$password   = "";            // Contraseña vacía en XAMPP
$dbname     = "metalisteria";  // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar si hay errores
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

?>
