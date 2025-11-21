<?php
include 'conexion.php';

$id = $_POST['id_producto'];

$sql = "DELETE FROM productos WHERE id = $id";

if($conn->query($sql)){
    echo "✔ Producto eliminado<br>";
    echo "<a href='eliminar_producto.php'>Volver</a>";
} else {
    echo "❌ Error: " . $conn->error;
}
