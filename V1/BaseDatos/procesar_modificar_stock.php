<?php
include 'conexion.php';

$id = $_POST['id_producto'];
$cantidad = $_POST['cantidad'];

$sql = "UPDATE productos SET stock = stock + $cantidad WHERE id = $id";

if($conn->query($sql)){
    echo "✔ Stock actualizado<br>";
    echo "<a href='modificar_stock.php'>Volver</a>";
} else {
    echo "❌ Error: " . $conn->error;
}
