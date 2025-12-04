<?php
session_start();
include '../conexion.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['accion']) && $input['accion'] === 'actualizar') {
    
    $id_prod = $input['id'];
    $modo = $input['modo']; // 'sumar' o 'restar'

    if (isset($_SESSION['carrito'][$id_prod])) {
        $cantidad = $_SESSION['carrito'][$id_prod]['cantidad'];

        // Consultar Stock real
        $stmt = $conn->prepare("SELECT stock FROM productos WHERE id = ?");
        $stmt->execute([$id_prod]);
        $stock = $stmt->fetchColumn();

        // Lógica
        if ($modo === 'sumar') {
            if ($cantidad < $stock) $cantidad++;
        } else {
            if ($cantidad > 1) $cantidad--;
        }

        // Guardar SOLO en sesión (Visitante no tiene BD de carrito)
        $_SESSION['carrito'][$id_prod]['cantidad'] = $cantidad;

        // Recalcular Totales
        $total_precio = 0;
        $total_items = 0;
        foreach ($_SESSION['carrito'] as $p) {
            $total_precio += $p['precio'] * $p['cantidad'];
            $total_items += $p['cantidad'];
        }

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