<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); 
    exit();
}

include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    // Primero, elimina las filas en la tabla venta_productos
    $sql_delete_venta_productos = "DELETE FROM venta_productos WHERE producto_id = ?";
    $stmt_venta_productos = $conn->prepare($sql_delete_venta_productos);
    $stmt_venta_productos->bind_param("i", $id);
    $stmt_venta_productos->execute();
    $stmt_venta_productos->close();

    // Luego, elimina el producto
    $sql = "DELETE FROM productos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['mensaje_producto'] = "Producto eliminado exitosamente.";
    } else {
        $_SESSION['mensaje_producto'] = "Error al eliminar el producto: " . $stmt->error;
    }

    $stmt->close();
    header("Location: productos.php");
    exit();
}

?>
