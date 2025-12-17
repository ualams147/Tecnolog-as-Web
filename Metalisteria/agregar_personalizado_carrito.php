<?php
// agregar_personalizado_carrito.php
require_once 'conexion.php';

// INICIO DE SESIÓN
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// 0. SEGURIDAD: Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

// 1. Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión.']);
    exit;
}

$id_cliente = $_SESSION['usuario_id'];

// 2. Recibir datos JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Datos no recibidos.']);
    exit;
}

// 3. LIMPIEZA DE DATOS (SANITIZACIÓN)
// Usamos trim() para quitar espacios.
// Usamos strip_tags() en 'detalles' para evitar que metan HTML o Scripts (XSS)
$categoria_nombre = trim($input['categoria'] ?? ''); 
$material_nombre  = trim($input['material'] ?? '');   
$color            = trim($input['color'] ?? '');                
$medida           = trim($input['medida'] ?? '');
$detalles_raw     = $input['detalles'] ?? '';
$detalles         = trim(strip_tags($detalles_raw)); // BLINDAJE ANTI-XSS

try {
    // INICIAR TRANSACCIÓN (Esto es perfecto, mantenlo)
    $conn->beginTransaction();

    // A. BUSCAR PRODUCTO BASE (Sentencia Preparada = SEGURO)
    $sql_buscar = "SELECT p.id 
                   FROM productos p
                   INNER JOIN categorias c ON p.id_categoria = c.id
                   INNER JOIN materiales m ON p.id_material = m.id
                   WHERE c.nombre = :cat AND m.nombre = :mat AND p.color = :col
                   LIMIT 1";
    
    $stmt = $conn->prepare($sql_buscar);
    $stmt->execute([':cat' => $categoria_nombre, ':mat' => $material_nombre, ':col' => $color]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fallback: búsqueda genérica si no hay color exacto
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

    // B. INSERTAR EN TABLA 'CARRITO' (Sentencia Preparada = SEGURO)
    $sql_cart = "INSERT INTO carrito (cliente_id, producto_id, cantidad, fecha_agregado, es_personalizado) 
                 VALUES (:cliente, :prod, 1, NOW(), 1)";
    
    $stmt_cart = $conn->prepare($sql_cart);
    $stmt_cart->execute([
        ':cliente' => $id_cliente,
        ':prod'    => $id_producto_base
    ]);

    $id_carrito_generado = $conn->lastInsertId();

    // C. INSERTAR EN TABLA 'CARRITO_PERSONALIZADOS' (Sentencia Preparada = SEGURO)
    $sql_custom = "INSERT INTO carrito_personalizados (carrito_id, medidas, color, detalles, estado) 
                   VALUES (:id_carr, :med, :col, :det, 'pendiente')";
    
    $stmt_custom = $conn->prepare($sql_custom);
    $stmt_custom->execute([
        ':id_carr' => $id_carrito_generado,
        ':med'     => $medida,
        ':col'     => $color, 
        ':det'     => $detalles // Aquí insertamos el texto limpio de virus/scripts
    ]);

    // CONFIRMAR TRANSACCIÓN
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Propuesta enviada correctamente.']);

} catch (Exception $e) {
    // Si algo falla, deshacemos todo
    $conn->rollBack();
    // En producción, podrías ocultar $e->getMessage() para no dar pistas al hacker
    echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud.']);
}
?>