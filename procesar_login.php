<?php
include 'conexion.php'; 
session_start();

$username = "";
$password = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); 

    // Consulta para verificar al usuario
    $sql = "SELECT password FROM usuarios WHERE username = ?"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username); 
    $stmt->execute();
    $stmt->store_result(); 

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashedPassword); 
        $stmt->fetch(); 

        // Verificar contraseña
        if (password_verify($password, $hashedPassword)) { 
            // Guardar el username en la sesión
            $_SESSION['username'] = $username; 

            // Redirigir dependiendo del usuario
            if ($username === '108004267') {
                // Usuario especial
                $_SESSION['is_special_user'] = true; // Indicador adicional
                header("Location: ventas.php"); 
            } else {
                // Usuario normal
                $_SESSION['is_special_user'] = false; // Indicador adicional
                header("Location: home.php");
            }
            exit();
        } else {
            $error_message = "Contraseña inválida.";
        }
    } else {
        $error_message = "Usuario inválido.";
    }

    $stmt->close();
    $conn->close();

    // Redirigir con mensaje de error si ocurre algún problema
    if (!empty($error_message)) {
        header("Location: login.php?error=" . urlencode($error_message));
        exit();
    }
}
?>
