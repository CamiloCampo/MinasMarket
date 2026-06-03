<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas - MinasMarket</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        let timeoutId = null;
        let primerProductoAgregado = false;

        document.addEventListener('DOMContentLoaded', function() {
            // Aseguramos que el primer campo de "Código de barras" tenga el foco al cargar la página
            const primerCodigoBarras = document.querySelector('#productos-container .producto:first-child input[name="codigo_barras[]"]');
            if (primerCodigoBarras) {
                setTimeout(() => {
                    primerCodigoBarras.focus();
                }, 100);
            }
        });

        // Función para escanear código de barras
        function escanearCodigoBarras(event) {
            event.preventDefault(); // Evitar que el formulario se envíe automáticamente

            clearTimeout(timeoutId); // Limpiar timeout anterior

            const codigoBarras = event.target.value.trim();
            if (codigoBarras) {
                timeoutId = setTimeout(() => {
                    fetch('buscar_producto.php?codigo_barras=' + codigoBarras)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.id) {
                                // Buscar si el producto ya está en la lista
                                const productosSeleccionados = document.querySelectorAll('#productos-container .producto select[name="producto_id[]"]');
                                let productoExistente = false;

                                productosSeleccionados.forEach(select => {
                                    if (select.value == data.id) {
                                        // Si el producto ya está en la lista, aumentar la cantidad
                                        const cantidadInput = select.closest('.producto').querySelector('input[name="cantidad[]"]');
                                        cantidadInput.value = parseInt(cantidadInput.value) + 1; // Aumentar la cantidad en 1
                                        productoExistente = true;
                                    }
                                });

                                if (!productoExistente) {
                                    // Si no existe, agregarlo como un nuevo producto
                                    agregarProducto(data);
                                }
                            } else {
                                alert('Producto no encontrado.');
                            }

                            // Limpiar el campo de código de barras después de escanear
                            event.target.value = ""; // Asegúrate de limpiar correctamente el campo
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Hubo un problema al buscar el producto.');
                        });
                }, 200);
            } else {
                // Limpiar el campo si no hay código de barras
                event.target.value = "";
            }
        }

        // Función para agregar un nuevo producto a la lista de productos
        function agregarProducto(data) {
            const container = document.getElementById('productos-container');

            // Si no se ha agregado ningún producto previamente
            if (!primerProductoAgregado) {
                const primerProducto = container.querySelector('.producto');

                // Completar los campos del primer formulario con la información del producto
                primerProducto.querySelector('select[name="producto_id[]"]').innerHTML = `<option value="${data.id}" selected>${data.nombre}</option>`;
                primerProducto.querySelector('input[name="precio[]"]').value = data.precio;
                primerProducto.querySelector('input[name="codigo_barras[]"]').value = data.codigo_barras;
                primerProducto.querySelector('input[name="cantidad[]"]').value = 1;

                primerProductoAgregado = true; // Indicamos que ya se completó el primer formulario
            } else {
                // Si ya se ha agregado un producto, crear un nuevo campo para agregar más productos
                const nuevoProducto = document.createElement('div');
                nuevoProducto.classList.add('producto');
                nuevoProducto.innerHTML =
                    `<label for="producto">Producto:</label>
                    <select name="producto_id[]" onchange="actualizarPrecio(this)" required>
                        <option value="${data.id}" data-precio="${data.precio}" selected>${data.nombre}</option>
                    </select>
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" name="cantidad[]" required value="1" oninput="validarCantidad(this)">
                    <label for="precio">Precio:</label>
                    <input type="text" name="precio[]" value="${data.precio}" readonly>
                    <label for="codigo_barras">Código de barras:</label>
                    <input type="text" name="codigo_barras[]" oninput="escanearCodigoBarras(event)">`;

                container.appendChild(nuevoProducto);

                // Enfocar automáticamente en el campo de código de barras del nuevo producto
                const inputCodigoBarras = nuevoProducto.querySelector('input[name="codigo_barras[]"]');
                inputCodigoBarras.focus();
            }
        }

        // Validar cantidad no negativa
        function validarCantidad(input) {
            if (input.value < 0) {
                alert('La cantidad no puede ser negativa.');
                input.value = '1'; // Volver a 1 si es negativo
            }
        }

        // Prevenir el envío automático del formulario con la tecla "Enter"
        document.addEventListener('keydown', function(event) {
            if (event.key === "Enter") {
                event.preventDefault(); // Evitar el envío automático
            }
        });

        // Asegurar que el formulario no se envíe automáticamente al presionar "Enter"
        document.querySelector('form').addEventListener('submit', function(event) {
            // Prevenir comportamiento de enviar el formulario automáticamente
            if (!validarFormulario()) {
                event.preventDefault();
            }
        });

        

        // Validar formulario antes de enviar
        function validarFormulario() {
            const productos = document.querySelectorAll('select[name="producto_id[]"]');
            if (productos.length === 0 || Array.from(productos).some(producto => !producto.value)) {
                alert('Por favor, asegúrate de seleccionar al menos un producto.');
                return false;
            }
            return true;
        }

        function buscarFecha() {
            const input = document.getElementById('fecha-busqueda').value;
            const filas = document.querySelectorAll('#ventas-lista tr');

            filas.forEach((fila) => {
                const fechaVenta = fila.querySelector('td:nth-child(5)').textContent;
                if (fechaVenta.split(' ')[0] === input || input === '') {
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
        <h2 class="Tittle">Ventas</h2>
        <hr style="height: 2px; background-color: black; border: none; margin-bottom : 30px">
        <?php
        include 'conexion.php';

        if (isset($_SESSION['mensaje_venta'])) {
            echo "<div class='mensaje'>{$_SESSION['mensaje_venta']}</div>";
            unset($_SESSION['mensaje_venta']);
        }
        ?>
       
        <form method="POST" action="registrar_venta.php" onsubmit="return validarFormulario();">
            <div id="productos-container">
                <!-- Aquí se generarán los productos -->
                <div class="producto">
                    <label for="producto">Producto:</label>
                    <select name="producto_id[]" onchange="actualizarPrecio(this)" required>
                        <option value="" disabled selected>Seleccione un producto</option>
                    </select>
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" name="cantidad[]" required value="1" oninput="validarCantidad(this)">
                    <label for="precio">Precio:</label>
                    <input type="text" name="precio[]" value="" readonly>
                    <label for="codigo_barras">Código de barras:</label>
                    <input type="text" name="codigo_barras[]" oninput="escanearCodigoBarras(event)">
                </div>
            </div>
            <button type="submit">Registrar Venta</button>
        </form>

        <h2>Buscar Ventas por Fecha</h2>
        <input type="date" id="fecha-busqueda" onchange="buscarFecha()" placeholder="Buscar por fecha...">

        <h2>Lista de Ventas</h2>
        <tr>
                    <td colspan="8" style="text-align: center; padding-top: 20px;">
                        <form action="generar_pdf_ventas.php" method="POST">
                            <button type="submit" style="padding: 10px 20px; font-size: 16px;">Descargar Informe</button>
                        </form>
                    </td>
                </tr>
        <table>
            <tr>
                <th>ID</th>
                <th>Productos Vendidos</th>
                <th>Cantidad Vendida</th>
                <th>Total</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
            <tbody id="ventas-lista">
            <?php
            $sql = "SELECT v.id,
                           GROUP_CONCAT(CONCAT(p.nombre, ' x', vp.cantidad, '') SEPARATOR ', ') AS productos,
                           SUM(vp.cantidad) AS cantidad,
                           SUM(vp.total) AS total,
                           v.fecha
                    FROM ventas v
                    JOIN venta_productos vp ON v.id = vp.venta_id
                    LEFT JOIN productos p ON vp.producto_id = p.id
                    GROUP BY v.id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['productos']}</td>
                            <td>{$row['cantidad']}</td>
                            <td>{$row['total']}</td>
                            <td>{$row['fecha']}</td>
                            <td><a href='generar_factura.php?id={$row['id']}' target='_blank'><button>Generar Factura</button></a></td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No se encontraron ventas.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</body>
</html>
