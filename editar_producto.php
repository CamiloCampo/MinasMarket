<?php
include 'conexion.php';

session_start(); 

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); 
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql = "SELECT * FROM productos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $producto = $result->fetch_assoc();
    } else {
        echo "Producto no encontrado.";
        exit;
    }
} else {
    echo "No se ha proporcionado un ID de producto.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $proveedor_id = $_POST['proveedor_id'];
    $precio = $_POST['precio'];
    $fecha_vencimiento = $_POST['fecha_vencimiento']; // Obtener la fecha de vencimiento del formulario

    // Validar la fecha
    $fecha_valida = DateTime::createFromFormat('Y-m-d', $fecha_vencimiento);
    if ($fecha_valida === FALSE) {
        echo "La fecha ingresada no es válida.";
        exit;
    }

    // Modificar la consulta de actualización para incluir la fecha de vencimiento
    $update_sql = "UPDATE productos SET nombre = ?, proveedor_id = ?, precio = ?, fecha_vencimiento = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssdsi", $nombre, $proveedor_id, $precio, $fecha_vencimiento, $id); // Incluir fecha de vencimiento en el bind_param

    if ($update_stmt->execute()) {
        $_SESSION['mensaje_producto'] = 'Producto actualizado con éxito.';
        header('Location: productos.php'); 
        exit;
    } else {
        echo "Error al actualizar el producto: " . $update_stmt->error;
    }
}

$sql_proveedores = "SELECT id, nombre FROM proveedores";
$result_proveedores = $conn->query($sql_proveedores);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0; 
            padding: 20px; 
            background-color: #f4f4f4;
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
        }
        .container {
            background: white; 
            padding: 40px; 
            border-radius: 5px; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
            width: 300px; 
            text-align: center; 
        }
        h2 {
            margin-bottom: 20px; 
        }
        label {
            display: block; 
            margin-bottom: 10px; 
            text-align: left; 
        }
        input[type="text"], input[type="date"], select {
            width: calc(100% - 22px); 
            padding: 10px; 
            margin-bottom: 20px; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
        }
        button {
            padding: 10px 15px; 
            background-color: #3498db; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            margin-bottom: 10px; 
            width: 100%; 
        }
        button:hover {
            background-color: #2980b9; 
        }
        .cancelar {
            background-color: #e74c3c; 
        }
        .cancelar:hover {
            background-color: #c0392b; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Producto</h2>
        <form method="POST" action="">
            <label for="nombre">Nombre del Producto:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>

            <label for="proveedor">Proveedor:</label>
            <select id="proveedor" name="proveedor_id" required>
                <option value="" disabled>Selecciona un proveedor</option>
                <?php
                if ($result_proveedores->num_rows > 0) {
                    while ($row = $result_proveedores->fetch_assoc()) {
                        $selected = $producto['proveedor_id'] == $row['id'] ? 'selected' : '';
                        echo "<option value='{$row['id']}' {$selected}>{$row['nombre']}</option>";
                    }
                }
                ?>
            </select>

            <label for="precio">Precio:</label>
            <input type="text" id="precio" name="precio" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>

            <label for="fecha_vencimiento">Fecha de Vencimiento:</label>
            <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" value="<?php echo htmlspecialchars($producto['fecha_vencimiento']); ?>" required>

            <button type="submit">Actualizar Producto</button>
            <button type="button" class="cancelar" onclick="window.location.href='productos.php'">Cancelar</button>
        </form>
    </div>
</body>
</html>
