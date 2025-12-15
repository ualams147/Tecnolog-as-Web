<?php
session_start();
include '../conexion.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_producto'])) {
    
    $usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
    $id_producto = $_POST['id_producto'];
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

    // 1. Obtener detalles del producto
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id = :id");
    $stmt->execute([':id' => $id_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        // --- A. ACTUALIZAR SESIÓN ---
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

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

        // --- B. GUARDAR EN BASE DE DATOS ---
        if ($usuario_id) {
            $check = $conn->prepare("SELECT id FROM carrito WHERE cliente_id = :uid AND producto_id = :pid");
            $check->execute([':uid' => $usuario_id, ':pid' => $id_producto]);
            $row = $check->fetch();

            if ($row) {
                $update = $conn->prepare("UPDATE carrito SET cantidad = cantidad + :cant WHERE id = :id");
                $update->execute([':cant' => $cantidad, ':id' => $row['id']]);
            } else {
                $insert = $conn->prepare("INSERT INTO carrito (cliente_id, producto_id, cantidad) VALUES (:uid, :pid, :cant)");
                $insert->execute([':uid' => $usuario_id, ':pid' => $id_producto, ':cant' => $cantidad]);
            }
        }
    }
}

// ---------------------------------------------------------
// CAMBIO REALIZADO AQUÍ:
// ---------------------------------------------------------
// Antes usabas HTTP_REFERER (volver atrás). 
// Ahora forzamos la ida a productos.php:

// CAMBIO AQUÍ: Añadimos #producto-ID al final de la URL
header("Location: productos.php#producto-" . $id_producto);
exit;
?>