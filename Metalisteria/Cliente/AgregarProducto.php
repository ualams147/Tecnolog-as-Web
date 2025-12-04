<?php
session_start();
include '../conexion.php'; // Ajusta la ruta si es necesario

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_producto'])) {
    
    $usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
    $id_producto = $_POST['id_producto'];
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

    // 1. Obtener detalles del producto para la SESIÓN (Visualización inmediata)
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id = :id");
    $stmt->execute([':id' => $id_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        // --- A. ACTUALIZAR SESIÓN (Para que se vea al instante) ---
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        // Si ya existe en sesión, sumamos cantidad; si no, lo creamos
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

        // --- B. GUARDAR EN BASE DE DATOS (Persistencia real) ---
        if ($usuario_id) {
            // Comprobamos si ya está en la tabla carrito
            $check = $conn->prepare("SELECT id FROM carrito WHERE cliente_id = :uid AND producto_id = :pid");
            $check->execute([':uid' => $usuario_id, ':pid' => $id_producto]);
            $row = $check->fetch();

            if ($row) {
                // UPDATE
                $update = $conn->prepare("UPDATE carrito SET cantidad = cantidad + :cant WHERE id = :id");
                $update->execute([':cant' => $cantidad, ':id' => $row['id']]);
            } else {
                // INSERT
                $insert = $conn->prepare("INSERT INTO carrito (cliente_id, producto_id, cantidad) VALUES (:uid, :pid, :cant)");
                $insert->execute([':uid' => $usuario_id, ':pid' => $id_producto, ':cant' => $cantidad]);
            }
        }
    }
}

// Redirigir a la misma página donde estaba el usuario (o a productos si falla)
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'productos.php';
header("Location: $referer");
exit;
?>