<?php
require_once "database.php";

$db = new Database();
$conn = $db->getConnection();

$sql = "SELECT p.referencia, p.nombre, p.medidas, p.stock, p.color, 
               m.nombre AS material, c.nombre AS categoria
        FROM productos p
        JOIN materiales m ON p.id_material = m.id
        JOIN categorias c ON p.id_categoria = c.id
        ORDER BY p.referencia";
$stmt = $conn->prepare($sql);
$stmt->execute();

$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Listado de productos</h1>

<?php foreach($productos as $p): ?>
        <div class="producto">
            <h2><?= $p['nombre'] ?></h2>
            <p><strong>Referencia:</strong> <?= $p['referencia'] ?></p>
            <p><strong>Material:</strong> <?= $p['material'] ?></p>
            <p><strong>Categoría:</strong> <?= $p['categoria'] ?></p>
            <p><strong>Medidas:</strong> <?= $p['medidas'] ?></p>
            <p><strong>Stock:</strong> <?= $p['stock'] ?></p>
            <p><strong>Color:</strong> <?= $p['color'] ?></p>
        </div>
    <?php endforeach; ?>

</body>
</html>