<?php
include 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Stock</title>

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
    <h1 class="text-center mb-4">Modificar Stock</h1>

    <form action="procesar_modificar_stock.php" method="POST">

        <!-- PRODUCTO -->
        <div class="mb-3">
            <label for="id_producto" class="form-label">Selecciona un producto:</label>
            <select name="id_producto" id="id_producto" class="form-select" required>
                <option value="">Selecciona un producto</option>
                <?php
                $productos = $conn->query("SELECT id, nombre, referencia, stock FROM productos ORDER BY nombre ASC");
                while ($p = $productos->fetch_assoc()) {
                    echo "<option value='{$p['id']}'>
                            {$p['nombre']} (Ref: {$p['referencia']}) - Stock actual: {$p['stock']}
                          </option>";
                }
                ?>
            </select>
        </div>

        <!-- CANTIDAD -->
        <div class="mb-3">
            <label for="cantidad" class="form-label">Cantidad a añadir o restar</label>
            <input type="number" class="form-control" name="cantidad" id="cantidad" placeholder="Ej: 5 o -3" required>
            <small class="text-muted">Puedes usar valores negativos para restar stock.</small>
        </div>

        <!-- BOTONES -->
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary px-4">Actualizar Stock</button>
            <a href="index.php" class="btn btn-secondary px-4">Volver</a>
        </div>

    </form>
</div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
