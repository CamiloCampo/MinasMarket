<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - MinasMarket</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 1rem;
            text-align: center;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 1rem;
            text-align: center;
        }
    </style>
</head>
<body class="login-body">
    <div class="login-container">
        
        <div >
        <a href="home.php">
            <img src="img/logo.jpeg" alt="MinasMarket Logo" class="logo">
        </a>
        <h1>Iniciar Sesión</h1>
        
    </div>

        <?php if (isset($_GET['registro']) && $_GET['registro'] == 'exitoso') : ?>
            <p class="success-message">Usuario registrado con éxito. Ahora puedes iniciar sesión.</p>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <p class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>

        <form method="POST" action="procesar_login.php">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required autocomplete="off">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Iniciar Sesión</button>
        </form>

        <p>¿No tienes cuenta? <a href="registrar.php">Registrar</a></p>
    </div>
</body>
</html>
