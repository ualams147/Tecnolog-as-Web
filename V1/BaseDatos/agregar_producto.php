<?php
include 'conexion.php';

// Variable para almacenar el mensaje
$mensaje = "";
$tipoMensaje = ""; // success o danger

// Si se envía el formulario:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ref = $_POST['referencia'];
    $nom = $_POST['nombre'];
    $mat = $_POST['id_material'];
    $cat = $_POST['id_categoria'];
    $med = $_POST['medidas'];
    $color = $_POST['color'];
    $stock = $_POST['stock'];

    $sql = "INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color)
            VALUES ('$ref', '$nom', $mat, $cat, '$med', $stock, '$color')";

    if ($conn->query($sql)) {
        $mensaje = "Producto agregado correctamente.";
        $tipoMensaje = "success";
    } else {
        $mensaje = "Error al agregar el producto: " . $conn->error;
        $tipoMensaje = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f3f4f6;
        }
        .form-container {
            max-width: 650px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
        }
        h1 {
            font-size: 26px;
            font-weight: bold;
        }
        label {
            font-weight: 600;
        }
    </style>
</head>

<body>

<div class="form-container">

    <h1 class="text-center mb-4">Agregar Nuevo Producto</h1>

    <!-- MENSAJE FLOTANTE -->
    <?php if (!empty($mensaje)) : ?>
        <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show" role="alert">
            <?php echo $mensaje; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- FORMULARIO -->
    <form method="POST">

        <!-- REFERENCIA -->
        <div class="mb-3">
            <label class="form-label">Referencia</label>
            <input type="text" class="form-control" name="referencia" required>
        </div>

        <!-- NOMBRE -->
        <div class="mb-3">
            <label class="form-label">Nombre del Producto</label>
            <input type="text" class="form-control" name="nombre" required>
        </div>

        <!-- MATERIAL -->
        <div class="mb-3">
            <label class="form-label">Material</label>
            <select name="id_material" class="form-select" required>
                <option value="">Selecciona un material</option>
                <?php
                $mats = $conn->query("SELECT * FROM materiales");
                while ($m = $mats->fetch_assoc()) {
                    echo "<option value='{$m['id']}'>{$m['nombre']}</option>";
                }
                ?>
            </select>
        </div>

        <!-- CATEGORIA -->
        <div class="mb-3">
            <label class="form-label">Categoría</label>
            <select name="id_categoria" class="form-select" required>
                <option value="">Selecciona una categoría</option>
                <?php
                $cats = $conn->query("SELECT * FROM categorias");
                while ($c = $cats->fetch_assoc()) {
                    echo "<option value='{$c['id']}'>{$c['nombre']}</option>";
                }
                ?>
            </select>
        </div>

        <!-- MEDIDAS -->
        <div class="mb-3">
            <label class="form-label">Medidas</label>
            <input type="text" class="form-control" name="medidas">
        </div>

        <!-- COLOR -->
        <div class="mb-3">
            <label class="form-label">Color</label>
            <input type="text" class="form-control" name="color">
        </div>

        <!-- STOCK -->
        <div class="mb-3">
            <label class="form-label">Stock inicial</label>
            <input type="number" class="form-control" name="stock" value="0">
        </div>

        <!-- BOTONES -->
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success px-4">Guardar Producto</button>
            <a href="index.php" class="btn btn-secondary px-4">Volver</a>
        </div>

    </form>
</div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
