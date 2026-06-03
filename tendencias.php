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
    <title>Productos Más Vendidos - MinasMarket</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

       
        .container {
            margin-left: 220px;
            padding: 20px;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        #tendencias-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        #tendencias-table th, #tendencias-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }

        #tendencias-table th {
            background-color: #3498db;
            color: white;
        }

        #tendencias-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: 'http://127.0.0.1:5000/predecir_mas_vendidos', // Ruta a la API de Flask
                type: 'GET',
                success: function(data) {
                    // Limpiar la tabla antes de insertar nuevos datos
                    $('#tendencias-table tbody').empty();

                    // Array con los nombres de los meses
                    const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

                    // Obtener el mes actual
                    const mesActual = new Date().getMonth() + 1; // Mes actual (1-12)

                    // Filtrar los datos para mostrar solo los meses posteriores al mes actual
                    const productosFuturos = data.filter(function(item) {
                        return item.Mes > mesActual; // Filtrar meses mayores al mes actual
                    });

                    // Recorrer los resultados filtrados y agregar filas a la tabla
                    productosFuturos.forEach(function(item) {
                        // Calculamos la recomendación de compra, por ejemplo, añadiendo un 20% sobre la predicción de ventas
                        const porcentajeAdicional = 0.20;
                        const cantidadRecomendada = Math.round(item.prediccion_ventas * (1 + porcentajeAdicional)); // Redondear a número entero

                        var fila = '<tr>';
                        fila += '<td>' + meses[item.Mes - 1] + '</td>'; // Convertir el número de mes a nombre
                        fila += '<td>' + item.NombreProducto + '</td>';
                        fila += '<td>' + cantidadRecomendada + '</td>'; // Mostrar la recomendación de compra como número entero
                        fila += '</tr>';
                        $('#tendencias-table tbody').append(fila);
                    });
                },
                error: function(error) {
                    console.log("Error al cargar los datos: ", error);
                }
            });
        });
    </script>
</head>
<body>
    <!-- Barra de navegación -->
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

    <!-- Contenido principal -->
    <div class="main-content">
        <h2 class="Tittle">Productos Más Vendidos (Predicción)</h2>

        <!-- Tabla donde se mostrarán los productos más vendidos -->
        <table id="tendencias-table">
            <thead>
                <tr>
                    <th>Mes</th>
                    <th>Producto</th>
                    <th>Recomendación de Compra</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos se llenarán con AJAX -->
            </tbody>
        </table>
    </div>
</body>
</html>
