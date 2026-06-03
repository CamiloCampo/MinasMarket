<?php
session_start(); 


if (!isset($_SESSION['username'])) {
    header("Location: login.php"); 
    exit();
}

include 'conexion.php'; 

$error_message = '';
$success_message = '';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION['username'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

   
    if (strlen($new_password) < 8) {
        $error_message = "La nueva contraseña debe tener al menos 8 caracteres.";
    } elseif (!preg_match('/[A-Z]/', $new_password)) {
        $error_message = "La nueva contraseña debe contener al menos una letra mayúscula.";
    } elseif (!preg_match('/[0-9]/', $new_password)) {
        $error_message = "La nueva contraseña debe contener al menos un número.";
    } else {
        
        $sql = "SELECT password FROM usuarios WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
           
            if (password_verify($current_password, $row['password'])) {
                
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET password = ? WHERE username = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $hashed_new_password, $username);

                if ($stmt->execute()) {
                    
                    $_SESSION['mensaje'] = "Contraseña cambiada con éxito";
                    header("Location: home.php");
                    exit();
                } else {
                    $error_message = "Error al actualizar la contraseña.";
                }
            } else {
                $error_message = "La contraseña actual es incorrecta.";
            }
        } else {
            $error_message = "Usuario no encontrado.";
        }

        $stmt->close();
        $conn->close(); 
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Contraseña - MinasMarket</title>
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
        input[type="password"] {
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
            background-color: #00000; 
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
        <h2>Editar Contraseña</h2>
        <form method="POST" action="editar_contrasena.php">
            <label for="current_password">Contraseña Actual:</label>
            <input type="password" id="current_password" name="current_password" required>
            <label for="new_password">Nueva Contraseña:</label>
            <input type="password" id="new_password" name="new_password" required>
            <button type="submit">Actualizar Contraseña</button>
            <button type="button" class="cancelar" onclick="window.location.href='home.php'">Cancelar</button>
        </form>
        
        <?php if ($error_message): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
