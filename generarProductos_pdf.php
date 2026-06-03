<?php
require('fpdf/fpdf.php'); // Asegúrate de que la librería FPDF esté incluida

// Conexión a la base de datos
include('conexion.php'); // Incluye tu archivo de conexión a la base de datos

// Consulta SQL
$sql = "SELECT p.id, p.nombre, p.precio, p.cantidad_disponible, pr.nombre AS proveedor_nombre, p.fecha_vencimiento, p.codigo_barras 
        FROM productos p 
        LEFT JOIN proveedores pr ON p.proveedor_id = pr.id";
$result = $conn->query($sql);

// Crear instancia de FPDF en modo horizontal
$pdf = new FPDF('L', 'mm', 'A4'); // 'L' indica Landscape (horizontal)
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Añadir el logo
$logo = 'img/logo.jpeg'; // Reemplaza con la ruta de tu logo
$pdf->Image($logo, 10, 10, 40); // (Ruta, X, Y, Ancho)

// Añadir el título
$pdf->Cell(0, 10, '', 0, 1); // Espaciado después del logo
$pdf->Cell(0, 10, 'Informe de Productos', 0, 1, align: 'C'); // Título centrado

$pdf->Ln(25); // Ajusta el valor (20 mm) para aumentar o reducir el margen superior

// Encabezados de la tabla
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(20, 10, 'ID', 1);
$pdf->Cell(50, 10, 'Nombre', 1);
$pdf->Cell(50, 10, 'Proveedor', 1);
$pdf->Cell(30, 10, 'Precio', 1);
$pdf->Cell(40, 10, 'Cantidad', 1);
$pdf->Cell(50, 10, 'Vencimiento', 1);
$pdf->Cell(50, 10, 'Codigo Barras', 1);
$pdf->Ln();

// Verificar si hay resultados
$pdf->SetFont('Arial', '', 12);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(20, 10, $row['id'], 1);
        $pdf->Cell(50, 10, $row['nombre'], 1);
        $pdf->Cell(50, 10, $row['proveedor_nombre'], 1);
        $pdf->Cell(30, 10, $row['precio'], 1);
        $pdf->Cell(40, 10, $row['cantidad_disponible'], 1);
        $pdf->Cell(50, 10, $row['fecha_vencimiento'], 1);
        $pdf->Cell(50, 10, $row['codigo_barras'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, 'No hay productos registrados.', 1, 1, 'C');
}


// Datos de contacto de la empresa (se coloca arriba a la derecha)
$pdf->SetFont('Arial', 'I', 10);
$contact_info = "MinasMarket | Tel: 3145755093 | Email: minasmarketcontact@gmail.com";


// Posicionamos el texto en la parte inferior derecha de la página actual
$pdf->SetXY(180, 179 ); // Y=210 - 10 coloca el texto cerca del borde inferior
$pdf->Cell(0, 10, $contact_info, 0, 0, 'R'); // Escribimos el texto alineado a la derecha


// Salida del PDF
$pdf->Output('D', 'lista_productos.pdf'); // Descarga el archivo como 'lista_productos.pdf'
exit;
?>