<?php
session_start(); 


if (!isset($_SESSION['username'])) {
    
    header("Location: login.php"); 
    exit();
}


include 'conexion.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $contacto = $_POST['contacto'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    $sql = "UPDATE proveedores SET nombre=?, contacto=?, telefono=?, direccion=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nombre, $contacto, $telefono, $direccion, $id);

    if ($stmt->execute()) {
        $_SESSION['mensaje_proveedor'] = "Proveedor actualizado con éxito.";
        header("Location: proveedores.php");
    } else {
        echo "Error al actualizar el proveedor: " . $conn->error;
    }
    $stmt->close();
    $conn->close();
    exit;
}

$id = $_GET['id'];


$sql = "SELECT * FROM proveedores WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$proveedor = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proveedor - MinasMarket</title>
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
        input[type="text"], select {
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
        <h2>Editar Proveedor</h2>
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo $proveedor['id']; ?>">
            <label for="nombre">Nombre del Proveedor:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo $proveedor['nombre']; ?>" required>
            <label for="contacto">Contacto:</label>
            <input type="text" id="contacto" name="contacto" value="<?php echo $proveedor['contacto']; ?>" required>
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" value="<?php echo $proveedor['telefono']; ?>" required>
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" value="<?php echo $proveedor['direccion']; ?>" required>
            <button type="submit">Actualizar Proveedor</button>
            <button type="button" class="cancelar" onclick="window.location.href='proveedores.php'">Cancelar</button>
        </form>
    </div>
</body>
</html>
