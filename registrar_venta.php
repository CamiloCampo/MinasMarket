<?php
include 'conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productos_id = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'];
    $precio = $_POST['precio'];

    $total = 0;

    // Iniciar la transacción
    $conn->begin_transaction();

    try {
        // Registrar la venta en la tabla ventas
        $sql = "INSERT INTO ventas (fecha) VALUES (NOW())";
        if ($conn->query($sql) === TRUE) {
            $venta_id = $conn->insert_id;

            // Insertar los productos de la venta
            for ($i = 0; $i < count($productos_id); $i++) {
                $prod_id = $productos_id[$i];
                $prod_cantidad = $cantidad[$i];
                $prod_precio = $precio[$i];
                $total_producto = $prod_cantidad * $prod_precio;
                $total += $total_producto;

                $sql_venta_producto = "INSERT INTO venta_productos (venta_id, producto_id, cantidad, total) 
                                       VALUES ($venta_id, $prod_id, $prod_cantidad, $total_producto)";
                $conn->query($sql_venta_producto);
            }

            // Actualizar el stock de productos
            for ($i = 0; $i < count($productos_id); $i++) {
                $prod_id = $productos_id[$i];
                $prod_cantidad = $cantidad[$i];

                $sql_actualizar_stock = "UPDATE productos SET cantidad_disponible = cantidad_disponible - $prod_cantidad WHERE id = $prod_id";
                $conn->query($sql_actualizar_stock);
            }

            // Confirmar la transacción
            $conn->commit();
            $_SESSION['mensaje_venta'] = "Venta registrada con éxito. Total: $total";
        } else {
            throw new Exception('Error al registrar la venta.');
        }
    } catch (Exception $e) {
        // Revertir la transacción si algo falla
        $conn->rollback();
        $_SESSION['mensaje_venta'] = "Error al registrar la venta: " . $e->getMessage();
    }
}

header('Location: ventas.php');
?>
