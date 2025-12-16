<?php
session_start();
include 'conexion.php';
header('Content-Type: application/json'); // Importante: Respondemos con JSON

// Leer los datos que vienen del Javascript
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['accion']) && $input['accion'] === 'actualizar') {
    
    $id_prod = $input['id'];
    $modo = $input['modo']; // 'sumar' o 'restar'
    $uid = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

    // 1. Verificar que el producto está en la sesión
    if (isset($_SESSION['carrito'][$id_prod])) {
        $cantidad = $_SESSION['carrito'][$id_prod]['cantidad'];

        // --- CORRECCIÓN AQUÍ: HEMOS QUITADO LA CONSULTA DE STOCK ---

        // Lógica de cambio
        if ($modo === 'sumar') {
            // Simplemente sumamos, ya no hay límite de stock
            $cantidad++;
        } else {
            // Para restar, validamos que no baje de 1
            if ($cantidad > 1) {
                $cantidad--;
            }
        }

        // 2. Guardar cambios en la SESIÓN
        $_SESSION['carrito'][$id_prod]['cantidad'] = $cantidad;

        // 3. Guardar cambios en la BASE DE DATOS (si es usuario registrado)
        if ($uid) {
            $sql = "UPDATE carrito SET cantidad = ? WHERE cliente_id = ? AND producto_id = ?";
            $stmtUpd = $conn->prepare($sql);
            $stmtUpd->execute([$cantidad, $uid, $id_prod]);
        }

        // 4. Recalcular Totales para actualizar la pantalla
        $total_precio = 0;
        $total_items = 0;
        foreach ($_SESSION['carrito'] as $p) {
            $total_precio += $p['precio'] * $p['cantidad'];
            $total_items += $p['cantidad'];
        }

        // 5. Responder a JavaScript con los nuevos datos
        echo json_encode([
            'ok' => true,
            'nuevaCantidad' => $cantidad,
            'nuevoTotal' => number_format($total_precio, 2),
            'totalItems' => $total_items
        ]);
        exit;
    }
}

// Si algo falla
echo json_encode(['ok' => false]);
?>