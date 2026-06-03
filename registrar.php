<?php
session_start(); 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']); 
    $password = $_POST['password']; 
    $nombre_completo = trim($_POST['nombre_completo']); 
    $email = trim($_POST['email']); 
    $telefono = trim($_POST['telefono']); 

    include 'conexion.php'; 

    
    $sql = "SELECT * FROM usuarios WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $username); 
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            
            $error_message = "El nombre de usuario ya existe.";
        } else {
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT); 
            $sql = "INSERT INTO usuarios (username, password, nombre_completo, email, telefono) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sssss", $username, $hashedPassword, $nombre_completo, $email, $telefono); 
                if ($stmt->execute()) {
                    
                    header("Location: login.php?registro=exitoso");
                    exit(); 
                } else {
                    $error_message = "Error al registrar el usuario: " . $stmt->error;
                }
            } else {
                $error_message = "Error al preparar la consulta de registro.";
            }
        }

        $stmt->close();
    } else {
        $error_message = "Error al preparar la consulta de verificación.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="login-body">
    <div class="login-container">
        <h1>Registrar Usuario</h1>
        <form method="POST" action="registrar_usuario.php">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required>
            <label for="nombre_completo">Nombre Completo:</label>
            <input type="text" id="nombre_completo" name="nombre_completo" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Registrar</button>
        </form>
       
        <?php if (isset($error_message)) : ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <p>¿Ya tienes cuenta? <a href="login.php">Iniciar Sesión</a></p>
    </div>
</body>
</html>
