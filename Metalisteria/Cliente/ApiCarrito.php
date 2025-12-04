<?php
session_start();
include '../conexion.php';
header('Content-Type: application/json'); // Importante: Respondemos con JSON

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['accion']) && $input['accion'] === 'actualizar') {
    
    $id_prod = $input['id'];
    $modo = $input['modo']; // 'sumar' o 'restar'
    $uid = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

    // 1. Verificar que el producto está en la sesión
    if (isset($_SESSION['carrito'][$id_prod])) {
        $cantidad = $_SESSION['carrito'][$id_prod]['cantidad'];

        // Consultar Stock
        $stmt = $conn->prepare("SELECT stock FROM productos WHERE id = ?");
        $stmt->execute([$id_prod]);
        $stock = $stmt->fetchColumn();

        // Lógica de cambio
        if ($modo === 'sumar') {
            if ($cantidad < $stock) $cantidad++;
        } else {
            if ($cantidad > 1) $cantidad--;
        }

        // 2. Guardar cambios
        $_SESSION['carrito'][$id_prod]['cantidad'] = $cantidad;

        if ($uid) {
            $sql = "UPDATE carrito SET cantidad = ? WHERE cliente_id = ? AND producto_id = ?";
            $stmtUpd = $conn->prepare($sql);
            $stmtUpd->execute([$cantidad, $uid, $id_prod]);
        }

        // 3. Recalcular Totales
        $total_precio = 0;
        $total_items = 0;
        foreach ($_SESSION['carrito'] as $p) {
            $total_precio += $p['precio'] * $p['cantidad'];
            $total_items += $p['cantidad'];
        }

        // 4. Responder a JavaScript
        echo json_encode([
            'ok' => true,
            'nuevaCantidad' => $cantidad,
            'nuevoTotal' => number_format($total_precio, 2),
            'totalItems' => $total_items
        ]);
        exit;
    }
}

echo json_encode(['ok' => false]);
?>