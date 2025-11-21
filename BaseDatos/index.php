<?php
$conn = new mysqli("localhost","root","","metalisteria");

// Filtrado
$materialFilter = isset($_GET['material']) ? $_GET['material'] : '';
$colorFilter = isset($_GET['color']) ? $_GET['color'] : '';

$query = "SELECT p.*, m.nombre AS material_nombre FROM productos p 
          JOIN materiales m ON p.id_material = m.id
          WHERE 1";

if ($materialFilter) {
    $query .= " AND p.id_material = " . intval($materialFilter);
}
if ($colorFilter) {
    $query .= " AND p.color = '" . $conn->real_escape_string($colorFilter) . "'";
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Productos</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1 class="mb-4">Catálogo de Productos de Metalistería</h1>

    <!-- Filtros -->
    <form method="get" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="material" class="form-label">Filtrar por material:</label>
            <select name="material" id="material" class="form-select">
                <option value="">Todos</option>
                <?php
                $materials = $conn->query("SELECT * FROM materiales");
                while($mat = $materials->fetch_assoc()) {
                    $selected = ($materialFilter == $mat['id_material']) ? 'selected' : '';
                    echo "<option value='{$mat['id_material']}' $selected>{$mat['nombre']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="color" class="form-label">Filtrar por color:</label>
            <select name="color" id="color" class="form-select">
                <option value="">Todos</option>
                <?php
                // COLORES DINÁMICOS
                $colors = $conn->query("SELECT DISTINCT color FROM productos ORDER BY color ASC");
                if ($colors) {
                    while ($col = $colors->fetch_assoc()) {
                        $color = $col['color'];
                        $selected = ($colorFilter == $color) ? 'selected' : '';
                        echo "<option value='$color' $selected>$color</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-4 align-self-end">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="index.php" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <!-- Productos -->
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php
        while($row = $result->fetch_assoc()) {
            echo "<div class='col'>";
            echo "  <div class='card h-100'>";
            echo "      <div class='card-body'>";
            echo "          <h5 class='card-title'>{$row['nombre']}</h5>";
            echo "          <p class='card-text'><strong>Medidas:</strong> {$row['medidas']}</p>";
            echo "          <p class='card-text'><strong>Color:</strong> {$row['color']}</p>";
            echo "          <p class='card-text'><strong>Material:</strong> {$row['material_nombre']}</p>";
            echo "          <p class='card-text'><strong>Stock:</strong> {$row['stock']}</p>";
            echo "      </div>";
            echo "  </div>";
            echo "</div>";
        }
        ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
