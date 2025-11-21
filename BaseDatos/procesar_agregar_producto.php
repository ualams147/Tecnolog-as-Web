<?php
include 'conexion.php';

$ref = $_POST['referencia'];
$nom = $_POST['nombre'];
$mat = $_POST['id_material'];
$cat = $_POST['id_categoria'];
$med = $_POST['medidas'];
$color = $_POST['color'];
$stock = $_POST['stock'];

$sql = "INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color)
        VALUES ('$ref', '$nom', $mat, $cat, '$med', $stock, '$color')";

if($conn->query($sql)){
    echo "✔ Producto añadido correctamente<br>";
    echo "<a href='agregar_producto.php'>Volver</a>";
} else {
    echo "❌ Error: " . $conn->error;
}