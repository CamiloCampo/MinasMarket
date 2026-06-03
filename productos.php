<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Muestra el mensaje de éxito o error si está establecido
$mensaje = isset($_SESSION['mensaje_producto']) ? $_SESSION['mensaje_producto'] : '';
unset($_SESSION['mensaje_producto']); // Limpiar el mensaje después de mostrarlo

include 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - MinasMarket</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Estilos para el formulario y otros elementos */
        .busqueda-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        #busqueda {
            flex: 1;
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            padding: 10px 15px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            white-space: nowrap;
            margin-left: 10px;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        button:hover {
            background-color: #000000;
        }

        .mensaje {
            color: green;
            margin-bottom: 20px;
        }

        .error {
            color: red;
            margin-bottom: 20px;
        }
    </style>
    <script>
        function validarPrecio(input) {
            if (isNaN(input.value) || input.value < 0) {
                alert('Por favor, ingresa un precio válido (número positivo).');
                input.value = '';
            }
        }

        function validarCantidad(input) {
            if (isNaN(input.value) || input.value < 0) {
                alert('Por favor, ingresa una cantidad válida (número positivo).');
                input.value = '';
            }
        }

        function buscarProducto() {
            const input = document.getElementById('busqueda').value.toLowerCase();
            const filas = document.querySelectorAll('#productos-lista tr');

            filas.forEach((fila) => {
                const nombreProducto = fila.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const nombreProveedor = fila.querySelector('td:nth-child(3)').textContent.toLowerCase();
                if (nombreProducto.includes(input) || nombreProveedor.includes(input) || input === '') {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
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
        <h2 class="Tittle">Productos</h2>

        <hr style="height: 2px; background-color: black; border: none; margin-bottom : 30px">

        <?php if ($mensaje): ?>
            <div class="mensaje"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <form method="POST" action="registrar_producto.php">
            <label for="nombre">Nombre del Producto:</label>
            <input type="text" id="nombre" name="nombre" required>
            <label for="proveedor">Proveedor:</label>
            <select id="proveedor" name="proveedor_id" required>
                <option value="" disabled selected>Selecciona un proveedor</option>
                <?php
                $sql_proveedores = "SELECT id, nombre FROM proveedores";
                $result_proveedores = $conn->query($sql_proveedores);

                if ($result_proveedores->num_rows > 0) {
                    while ($row = $result_proveedores->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
                    }
                }
                ?>
            </select>
            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" required oninput="validarPrecio(this)">
            <label for="cantidad_disponible">Cantidad Disponible:</label>
            <input type="number" id="cantidad_disponible" name="cantidad_disponible" required
                oninput="validarCantidad(this)">
            <label for="fecha_vencimiento">Fecha de Vencimiento:</label>
            <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" required>
            <label for="codigo_barras">Código de Barras:</label>
            <input type="text" id="codigo_barras" name="codigo_barras" required>
            <button type="submit">Registrar Producto</button>
        </form>

        <h2 class="h2_producto">Buscar Producto</h2>
        <div class="busqueda-container">
            <input type="text" id="busqueda" onkeyup="buscarProducto()"
                placeholder="Buscar por nombre de producto o proveedor...">
            <button onclick="window.location.href='productos_a_vencer.php'">Productos a Vencer</button>
            <!-- Botón para generar PDF de productos sin stock -->
            <button onclick="window.location.href='generar_pdf.php'">Generar PDF Productos Sin Stock</button>
        </div>

        <h2>Lista de Productos</h2>
        <!-- Botón para generar y descargar el PDF -->
        <tr>
            <td colspan="8" style="text-align: center; padding-top: 20px;">
                <form action="generarProductos_pdf.php" method="POST">
                    <button type="submit" style="padding: 10px 20px; font-size: 16px;">Descargar Informe</button>
                </form>
            </td>
        </tr>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Proveedor</th>
                    <th>Precio</th>
                    <th>Cantidad Disponible</th>
                    <th>Fecha de Vencimiento</th>
                    <th>Código de Barras</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="productos-lista">
                <?php
                $sql = "SELECT p.id, p.nombre, p.precio, p.cantidad_disponible, pr.nombre AS proveedor_nombre, p.fecha_vencimiento, p.codigo_barras 
            FROM productos p 
            LEFT JOIN proveedores pr ON p.proveedor_id = pr.id";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['nombre']}</td>
                            <td>{$row['proveedor_nombre']}</td>
                            <td>{$row['precio']}</td>
                            <td>{$row['cantidad_disponible']}</td>
                            <td>{$row['fecha_vencimiento']}</td>
                            <td>{$row['codigo_barras']}</td>
                            <td>
                                <form method='POST' action='eliminar_producto.php' style='display:inline;'>
                                    <input type='hidden' name='id' value='{$row['id']}' />";

                        // Botón "Eliminar"
                        if (isset($_SESSION['username']) && $_SESSION['username'] === '1080042678') {
                            echo "<button type='submit' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este producto?\");'>Eliminar</button>";
                        } else {
                            echo "<button type='button' disabled style='cursor: not-allowed; color: gray;'>Eliminar</button>";
                        }

                        echo "</form>";

                        // Botón "Editar"
                        if (isset($_SESSION['username']) && $_SESSION['username'] === '1080042678') {
                            echo "<a href='editar_producto.php?id={$row['id']}' class='editar-boton' style='margin-left: 10px;'>Editar</a>";
                        } else {
                            echo "<a href='#' class='editar-boton' style='margin-left: 10px; cursor: not-allowed; text-decoration: none; color: gray;'>Editar</a>";
                        }

                        echo "</td>
                          </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No hay productos registrados.</td></tr>";
                }
                ?>

            </tbody>

        </table>
    </div>
</body>

</html>