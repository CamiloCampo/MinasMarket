<?php
include 'conexion.php';
require('fpdf/fpdf.php'); 


$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Productos a Vencer en menos de una semana', 0, 1, 'C');
$pdf->Ln(10); 


$sql = "SELECT p.nombre, pr.nombre AS proveedor_nombre, p.fecha_vencimiento 
        FROM productos p 
        LEFT JOIN proveedores pr ON p.proveedor_id = pr.id 
        WHERE p.fecha_vencimiento <= NOW() + INTERVAL 7 DAY";
$result = $conn->query($sql);


$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 10, 'Nombre', 1);
$pdf->Cell(60, 10, 'Proveedor', 1);
$pdf->Cell(60, 10, 'Fecha de Vencimiento', 1);
$pdf->Ln();


$pdf->SetFont('Arial', '', 10);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(60, 10, $row['nombre'], 1);
        $pdf->Cell(60, 10, $row['proveedor_nombre'], 1);
        
        $fecha_vencimiento = date('d/m/Y', strtotime($row['fecha_vencimiento']));
        $pdf->Cell(60, 10, $fecha_vencimiento, 1);
        $pdf->Ln(); 
    }
} else {
    
    $pdf->Cell(0, 10, 'No hay productos a vencer en menos de una semana.', 0, 1, 'C');
}


$conn->close();


header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="productos_a_vencer.pdf"');
$pdf->Output('D','productos_a_vencer.pdf'); 
exit; 
?>
