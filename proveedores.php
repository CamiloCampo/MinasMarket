<?php
session_start();


if (!isset($_SESSION['username'])) {

    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores - MinasMarket</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        #busqueda {
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }
    </style>
    <script>
        function buscarProveedor() {
            const input = document.getElementById('busqueda').value.toLowerCase();
            const filas = document.querySelectorAll('#proveedores-lista tr');

            filas.forEach((fila) => {
                const nombreProveedor = fila.querySelector('td:nth-child(2)').textContent.toLowerCase();
                if (nombreProveedor.includes(input) || input === '') {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        }

        function permitirSoloLetras(event) {
            const key = event.key;
            const regex = /^[a-zA-Z\s]$/;

            if (!regex.test(key) && key !== "Backspace" && key !== "Tab") {
                event.preventDefault();
            }
        }

        function permitirSoloNumeros(event) {
            const key = event.key;
            const telefonoInput = document.getElementById('telefono');
            const numeroActual = telefonoInput.value.length;

            const regex = /^[0-9]$/;

            if (numeroActual >= 15 && regex.test(key)) {
                event.preventDefault();
            }

            if (!regex.test(key) && key !== "Backspace" && key !== "Tab") {
                event.preventDefault();
            }
        }
    </script>
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
        <h2 class="Tittle">Proveedores</h2>
        <hr style="height: 2px; background-color: black; border: none; margin-bottom : 30px">
        <?php

        if (isset($_SESSION['mensaje_proveedor'])) {
            echo "<div class='mensaje'>" . $_SESSION['mensaje_proveedor'] . "</div>";
            unset($_SESSION['mensaje_proveedor']);
        }
        ?>

        <form method="POST" action="registrar_proveedor.php">
            <label for="nombre">Nombre del Proveedor:</label>
            <input type="text" id="nombre" name="nombre" required>
            <label for="contacto">Contacto:</label>
            <input type="text" id="contacto" name="contacto" required onkeypress="permitirSoloLetras(event)">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required onkeypress="permitirSoloNumeros(event)">
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" required>
            <button type="submit">Registrar Proveedor</button>
        </form>

        <h2>Buscar Proveedor</h2>
        <input type="text" id="busqueda" onkeyup="buscarProveedor()" placeholder="Buscar por nombre de proveedor...">

        <h2>Lista de Proveedores</h2>
        <tr>
            <td colspan="8" style="text-align: center; padding-top: 5px;">
                <form action="generar_pdf_proveedores.php" method="POST">
                    <button type="submit" style="padding: 10px 20px; font-size: 16px;">Descargar Informe</button>
                </form>
            </td>
        </tr>
        <table>


            <th>Nombre</th>
            <th>Contacto</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Acciones</th>
            </tr>
            <tbody id="proveedores-lista">
                <?php
                include 'conexion.php';
                $sql = "SELECT * FROM proveedores";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                    <td>{$row['nombre']}</td>
                    <td>{$row['contacto']}</td>
                    <td>{$row['telefono']}</td>
                    <td>{$row['direccion']}</td>
                    <td>
                        <a href='editar_proveedor.php?id={$row['id']}' class='editar-boton'>Editar</a>
                    </td>
                  </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No hay proveedores registrados.</td></tr>";
                }

                $conn->close();
                ?>


            </tbody>
        </table>
    </div>
</body>

</html>