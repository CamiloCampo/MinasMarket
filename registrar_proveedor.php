<?php
session_start(); 

if (!isset($_SESSION['username'])) {
    
    header("Location: login.php"); 
    exit();
}
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre = $_POST['nombre'];
    $contacto = $_POST['contacto'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    
    $sql = "INSERT INTO proveedores (nombre, contacto, telefono, direccion) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $contacto, $telefono, $direccion);

    if ($stmt->execute()) {
        $_SESSION['mensaje_proveedor'] = "Proveedor registrado exitosamente.";
    } else {
        $_SESSION['mensaje_proveedor'] = "Error al registrar el proveedor: " . $stmt->error; 
    }
    
    $stmt->close();
    
    
    header("Location: proveedores.php");
    exit(); 
}
?>
