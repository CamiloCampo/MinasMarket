<?php
require 'fpdf/fpdf.php';


$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);


$id_venta = $_GET['id'];


include 'conexion.php';


$sql = "SELECT v.id, v.fecha, SUM(vp.cantidad) AS cantidad_total, SUM(vp.total) AS total 
        FROM ventas v 
        JOIN venta_productos vp ON v.id = vp.venta_id 
        WHERE v.id = ?
        GROUP BY v.id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_venta);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $venta = $result->fetch_assoc();


    // Añadir el logo
    $logo = 'img/logo.jpeg'; // Reemplaza con la ruta de tu logo
    $pdf->Image($logo, 10, 10, 40); // (Ruta, X, Y, Ancho)

    // Añadir el título
    $pdf->Cell(0, 10, '', 0, 1); // Espaciado después del logo
    $pdf->Cell(0, 10, 'Informe de Productos', 0, 1, align: 'C'); // Título centrado

    $pdf->Ln(25); // Ajusta el valor (20 mm) para aumentar o reducir el margen superior
    $pdf->Cell(0, 10, "ID de Venta: {$venta['id']}", 0, 1);
    $pdf->Cell(0, 10, "Fecha: {$venta['fecha']}", 0, 1);
    $pdf->Ln(10);


    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(80, 10, "Producto", 1);
    $pdf->Cell(40, 10, "Cantidad", 1);
    $pdf->Cell(30, 10, "Subtotal", 1);
    $pdf->Ln();


    $sql_productos = "SELECT p.nombre, vp.cantidad, vp.precio, vp.total 
                      FROM venta_productos vp 
                      JOIN productos p ON vp.producto_id = p.id 
                      WHERE vp.venta_id = ?";
    $stmt_productos = $conn->prepare($sql_productos);
    $stmt_productos->bind_param("i", $id_venta);
    $stmt_productos->execute();
    $result_productos = $stmt_productos->get_result();

    $pdf->SetFont('Arial', '', 12);

    while ($producto = $result_productos->fetch_assoc()) {
        $pdf->Cell(80, 10, $producto['nombre'], 1);
        $pdf->Cell(40, 10, $producto['cantidad'], 1);
        $pdf->Cell(30, 10, "$" . number_format($producto['total'], 2), 1);
        $pdf->Ln();
    }


    $pdf->Ln();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(160, 10, "Total:", 1);
    $pdf->Cell(30, 10, "$" . number_format($venta['total'], 2), 1);

} else {
    $pdf->Cell(0, 10, "Venta no encontrada.", 0, 1, 'C');
}

// Datos de contacto de la empresa (se coloca arriba a la derecha)
$pdf->SetFont('Arial', 'I', 10);
$contact_info = "MinasMarket | Tel: 3145755093 | Email: minasmarketcontact@gmail.com";


// Posicionamos el texto en la parte inferior derecha de la página actual
$pdf->SetXY(180, 265 ); // Y=210 - 10 coloca el texto cerca del borde inferior
$pdf->Cell(0, 10, $contact_info, 0, 0, 'R'); // Escribimos el texto alineado a la derecha

$pdf->Output('D', "factura_{$venta['id']}.pdf");

$stmt->close();
$stmt_productos->close();
$conn->close();
?>