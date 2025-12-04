<?php
session_start();

// 1. Destruir todas las variables de sesión (Server side)
session_unset();

// 2. Destruir la sesión completamente
session_destroy();

// 3. Redirigir al inicio (ajusta la ruta si tu index está en otra carpeta)
header("Location: index.php"); 
exit();
?>