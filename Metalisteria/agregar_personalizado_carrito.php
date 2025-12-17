<?php
// agregar_personalizado_carrito.php
require_once 'conexion.php';

session_start();
header('Content-Type: application/json');

// 1. Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión.']);
    exit;
}

$id_cliente = $_SESSION['usuario_id'];

// 2. Recibir datos JSON desde el JS
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Datos no recibidos.']);
    exit;
}

$categoria_nombre = $input['categoria']; 
$material_nombre  = $input['material'];   
$color            = $input['color'];                
$medida           = $input['medida'];
$detalles         = $input['detalles'] ?? '';

try {
    // INICIAR TRANSACCIÓN
    $conn->beginTransaction();

    // A. BUSCAR PRODUCTO BASE
    // Necesitamos un ID de producto base para la tabla carrito (para la foto y nombre genérico)
    $sql_buscar = "SELECT p.id 
                   FROM productos p
                   INNER JOIN categorias c ON p.id_categoria = c.id
                   INNER JOIN materiales m ON p.id_material = m.id
                   WHERE c.nombre = :cat AND m.nombre = :mat AND p.color = :col
                   LIMIT 1";
    
    $stmt = $conn->prepare($sql_buscar);
    $stmt->execute([':cat' => $categoria_nombre, ':mat' => $material_nombre, ':col' => $color]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fallback: Si no encuentra el color exacto, busca solo por categoría y material
    if (!$producto) {
        $sql_fallback = "SELECT p.id 
                         FROM productos p
                         INNER JOIN categorias c ON p.id_categoria = c.id
                         INNER JOIN materiales m ON p.id_material = m.id
                         WHERE c.nombre = :cat AND m.nombre = :mat LIMIT 1";
        $stmt_fb = $conn->prepare($sql_fallback);
        $stmt_fb->execute([':cat' => $categoria_nombre, ':mat' => $material_nombre]);
        $producto = $stmt_fb->fetch(PDO::FETCH_ASSOC);
    }

    if (!$producto) {
        throw new Exception("No se encontró el producto base en el catálogo.");
    }

    $id_producto_base = $producto['id'];

    // B. INSERTAR EN TABLA 'CARRITO' (Principal)
    // Marcamos es_personalizado = 1
    $sql_cart = "INSERT INTO carrito (cliente_id, producto_id, cantidad, fecha_agregado, es_personalizado) 
                 VALUES (:cliente, :prod, 1, NOW(), 1)";
    
    $stmt_cart = $conn->prepare($sql_cart);
    $stmt_cart->execute([
        ':cliente' => $id_cliente,
        ':prod'   => $id_producto_base
    ]);

    // Recuperamos el ID recién creado
    $id_carrito_generado = $conn->lastInsertId();

    // C. INSERTAR EN TABLA 'CARRITO_PERSONALIZADOS' (Detalles)
    // El estado por defecto es 'pendiente'
    $sql_custom = "INSERT INTO carrito_personalizados (carrito_id, medidas, color, detalles, estado) 
                   VALUES (:id_carr, :med, :col, :det, 'pendiente')";
    
    $stmt_custom = $conn->prepare($sql_custom);
    $stmt_custom->execute([
        ':id_carr' => $id_carrito_generado,
        ':med'     => $medida,
        ':col'     => $color, 
        ':det'     => $detalles
    ]);

    // CONFIRMAR TRANSACCIÓN
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Propuesta enviada correctamente.']);

} catch (Exception $e) {
    // Si algo falla, deshacemos todo
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>