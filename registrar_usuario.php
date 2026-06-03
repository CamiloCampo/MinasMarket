<?php
include 'conexion.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $nombre_completo = $_POST['nombre_completo'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $password = $_POST['password'];

    
    $sql = "SELECT * FROM usuarios WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (username, password, nombre_completo, email, telefono) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $username, $hashedPassword, $nombre_completo, $email, $telefono);
        
        if ($stmt->execute()) {
            
            header("Location: login.php?registro=exitoso");
            exit(); 
        } else {
            echo "Error al registrar el usuario: " . $stmt->error;
        }
    } else {
        echo "El nombre de usuario ya existe.";
    }

    $stmt->close();
    $conn->close();
}
?>
