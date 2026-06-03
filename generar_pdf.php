<?php
require('fpdf/fpdf.php'); 
include 'conexion.php';


$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Productos Sin Stock', 0, 1, 'C');
$pdf->Ln(10);


$sql = "SELECT p.nombre, pr.nombre AS proveedor_nombre FROM productos p LEFT JOIN proveedores pr ON p.proveedor_id = pr.id WHERE p.cantidad_disponible = 0";
$result = $conn->query($sql);


$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(90, 10, 'Nombre del Producto', 1);
$pdf->Cell(90, 10, 'Proveedor', 1);
$pdf->Ln();


$pdf->SetFont('Arial', '', 12);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(90, 10, $row['nombre'], 1);
        $pdf->Cell(90, 10, $row['proveedor_nombre'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(180, 10, 'No hay productos sin stock.', 1);
}


$pdf->Output('D', 'productos_sin_stock.pdf');
exit();
?>
