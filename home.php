<?php
session_start(); 


if (!isset($_SESSION['username'])) {
    
    header("Location: login.php"); 
    exit();
}


include 'conexion.php'; 


$username = $_SESSION['username'];
$sql = "SELECT nombre_completo, email, telefono FROM usuarios WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$user_info = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - MinasMarket</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            padding: 20px;
            color: white;
            position: fixed;
            height: 100%;
            overflow-y: auto;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }
        .sidebar ul li {
            margin: 10px 0;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
        }
        .sidebar ul li a:hover {
            text-decoration: underline;
        }
        .main-content {
            flex-grow: 1;
          
            margin-left: 280px; 
        }
        .profile {
            
            display: block;
            align-items: center;
            margin-bottom: 20px;
            text-align: center; 
            padding-bottom: 10px;
            
        }
        .profile img {
            width: 100px; 
            height: 100px;
            border-radius: 50%; 
            margin-right: 15px;
            margin-left:250;
            padding: 40px;
            
        }
        .profile-info {
            margin-left:150;
            font-size: 1.2rem;
            margin-left:250;
            flex: 1; /* Ocupa el espacio restante */
        }

        h1 {
            font-size: 2.5rem; 
            font-weight: bold; 
            text-align: center; 
            margin-top: 40px; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2); 
            padding: 10px; 
        }
        .edit-password-button {
            padding: 15px 15px;
            background-color: #343a40;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 30px;
            text-decoration: none; 
            text-align: center; 
            width: auto;
            align-items: center;
            margin: 0 auto;
        }

        .edit-password-button:hover {
            background-color: #000000;

            font-size : 12px;
        }

        .backg{
            background-color: #D6C0B3;
            border-radius: 20px;
            width: 30%;
            padding: 15px 0;
            margin: 0 auto;
        }
        

        .link-button {
            padding: 10px 25px;
            background-color: #343a40;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            text-decoration: none; 
            text-align: center; 
            width: auto;
            margin: 20px auto ;
        }

        .link-button:hover {
            background-color: #000000; 
            font-size : 20px;
        }


        .mensaje {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

      
        .hov:hover{
            font-size:20px;
            text-decoration: none;
        }
        
       

    </style>
</head>
<body>
    <div class="sidebar">
            <a href="home.php">
                <img src="img/logo.jpeg" alt="MinasMarket Logo" class="logo">
            </a>
            <hr>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="productos.php">Productos</a></li>
                <li><a href="proveedores.php">Proveedores</a></li>
                <li><a href="ventas.php">Ventas</a></li>
                <li><a href="tendencias.php">Tendencias</a></li>

            </ul>
    </div>
    
    <div class="main-content">
        <h1>Bienvenido a MinasMarket</h1>

       

        <div class="backg">
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="mensaje">
                <?php echo $_SESSION['mensaje']; ?>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <div class="profile">
            <img src="img/icono.png" alt="Foto de perfil"> 
            <div class="profile-info">
                <strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?><br>
                <strong>Nombre Completo:</strong> <?php echo htmlspecialchars($user_info['nombre_completo']); ?><br>
                <strong>Email:</strong> <?php echo htmlspecialchars($user_info['email']); ?><br>
                <strong>Teléfono:</strong> <?php echo htmlspecialchars($user_info['telefono']); ?><br>
            </div>
    
        </div>
 
        
        <form action="editar_contrasena.php" method="get">
            <button type="submit" class="edit-password-button">Editar Contraseña</button>
            <a href="cerrar_sesion.php" class="link-button">Cerrar Sesión</a>
        </form>

        </div>
    </div>
</body>
</html>
