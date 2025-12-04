<?php
session_start();
include '../conexion.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_producto'])) {
    
    $id_producto = $_POST['id_producto'];
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

    // Buscamos el producto en la BD para obtener precio, foto, etc.
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id = :id");
    $stmt->execute([':id' => $id_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        // Inicializar carrito si no existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        // Si ya existe, sumamos cantidad; si no, lo creamos
        if (isset($_SESSION['carrito'][$id_producto])) {
            $_SESSION['carrito'][$id_producto]['cantidad'] += $cantidad;
        } else {
            $_SESSION['carrito'][$id_producto] = [
                'id' => $producto['id'],
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'imagen' => $producto['imagen_url'],
                'referencia' => $producto['referencia'],
                'color' => $producto['color'],
                'medidas' => $producto['medidas'],
                'cantidad' => $cantidad
            ];
        }
    }
}

// Volver a la página anterior
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'productos.php';
header("Location: $referer");
exit;
?>