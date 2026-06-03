<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $proveedor_id = $_POST['proveedor_id'];
    $precio = $_POST['precio'];
    $cantidad_disponible = $_POST['cantidad_disponible'];
    $codigo_barras = $_POST['codigo_barras'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'];

    // Asegurarse de que la fecha esté en el formato correcto (YYYY-MM-DD)
    if ($fecha_vencimiento != '') {
        $fecha_vencimiento = date('Y-m-d', strtotime($fecha_vencimiento));
    } else {
        $fecha_vencimiento = NULL; // Si no se proporciona fecha, guardarlo como NULL
    }

    // Preparar la consulta de inserción
    $sql = "INSERT INTO productos (nombre, proveedor_id, precio, cantidad_disponible, fecha_vencimiento, codigo_barras) 
            VALUES ('$nombre', '$proveedor_id', '$precio', '$cantidad_disponible', '$fecha_vencimiento', '$codigo_barras')";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['mensaje_producto'] = "Producto registrado exitosamente.";
    } else {
        $_SESSION['mensaje_producto'] = "Error al registrar el producto: " . $conn->error;
    }

    header("Location: productos.php");
    exit();
}
