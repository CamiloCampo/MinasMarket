<?php
require('fpdf/fpdf.php'); // Asegúrate de que la librería FPDF esté incluida

// Conexión a la base de datos
include('conexion.php'); // Incluye tu archivo de conexión a la base de datos

// Consulta SQL
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

// Crear instancia de FPDF en modo horizontal
$pdf = new FPDF('L', 'mm', 'A4'); // 'L' indica Landscape (horizontal)
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Título del PDF
$logo = 'img/logo.jpeg'; // Reemplaza con la ruta de tu logo
$pdf->Image($logo, 10, 10, 40); // (Ruta, X, Y, Ancho)

// Añadir el título
$pdf->Cell(0, 10, '', 0, 1); // Espaciado después del logo
$pdf->Cell(0, 10, 'Informe de ventas', 0, 1, 'C'); // Título centrado

$pdf->Ln(25); // Ajusta el valor (20 mm) para aumentar o reducir el margen superior

// Ancho total de la tabla
$total_width = 20 + 50 + 30 + 40 + 40; // Ancho total de las celdas (ID + Productos + Cantidad + Total + Fecha)

// Posicionar la tabla en el centro
$x_offset = (297 - $total_width) / 2; // Calcular el margen izquierdo para centrar la tabla

// Encabezados de la tabla sin la columna "Acciones"
$pdf->SetXY($x_offset, $pdf->GetY()); // Posicionar el cursor en el inicio de la tabla
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(20, 10, 'ID', 1, 0, 'C');
$pdf->Cell(50, 10, 'Productos Vendidos', 1, 0, 'C');
$pdf->Cell(30, 10, 'Cantidad Vendida', 1, 0, 'C');
$pdf->Cell(40, 10, 'Total', 1, 0, 'C');
$pdf->Cell(40, 10, 'Fecha', 1, 1, 'C'); // Eliminada la columna "Acciones"

// Datos de la tabla
$pdf->SetFont('Arial', '', 12);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->SetXY($x_offset, $pdf->GetY()); // Reiniciar la posición horizontal para cada fila
        $pdf->Cell(20, 10, $row['id'], 1, 0, 'C');
        $pdf->Cell(50, 10, $row['productos'], 1, 0, 'L'); // Alineado a la izquierda para la columna de productos
        $pdf->Cell(30, 10, $row['cantidad'], 1, 0, 'C');
        $pdf->Cell(40, 10, number_format($row['total'], 2), 1, 0, 'C'); // Formato de número para el total
        $pdf->Cell(40, 10, $row['fecha'], 1, 1, 'C');
    }
} else {
    $pdf->SetXY($x_offset, $pdf->GetY()); // Posicionar la fila de "No se encontraron ventas"
    $pdf->Cell(0, 10, 'No se encontraron ventas.', 1, 1, 'C');
}

// Datos de contacto de la empresa (se coloca arriba a la derecha)
$pdf->SetFont('Arial', 'I', 10);
$contact_info = "MinasMarket | Tel: 3145755093 | Email: minasmarketcontact@gmail.com";


// Posicionamos el texto en la parte inferior derecha de la página actual
$pdf->SetXY(180, 179 ); // Y=210 - 10 coloca el texto cerca del borde inferior
$pdf->Cell(0, 10, $contact_info, 0, 0, 'R'); // Escribimos el texto alineado a la derecha
// Salida del PDF
$pdf->Output('D', 'informe_ventas.pdf'); // Descarga el archivo como 'informe_ventas.pdf'
exit;
?>
