<?php
include 'conexion.php';

if (isset($_GET['codigo_barras'])) {
    $codigoBarras = $_GET['codigo_barras'];
    $sql = "SELECT id, nombre, precio FROM productos WHERE codigo_barras = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $codigoBarras);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $producto = $result->fetch_assoc();
        echo json_encode($producto);
    } else {
        echo json_encode(null); // No se encontró el producto
    }
}
?>
