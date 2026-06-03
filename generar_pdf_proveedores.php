<?php
require('fpdf/fpdf.php');

// Conexión a la base de datos
include('conexion.php');

// Consulta SQL
$sql = "SELECT * FROM proveedores";
$result = $conn->query($sql);

// Crear instancia de FPDF con orientación horizontal
$pdf = new FPDF('L', 'mm', 'A4'); // 'L' indica Landscape (horizontal)
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

$logo = 'img/logo.jpeg'; // Reemplaza con la ruta de tu logo
$pdf->Image($logo, 10, 10, 40); // (Ruta, X, Y, Ancho)

// Obtener las dimensiones de la imagen
$logo_width = 40; // Ancho de la imagen
$logo_x = 10; // Posición X de la imagen

// Calcular la posición del título para centrarlo respecto al logo
$title = 'Lista de Proveedores';
$title_width = $pdf->GetStringWidth($title); // Obtener el ancho del texto del título
$start_x = $logo_x + $logo_width + 75; // Comienza después de la imagen con un pequeño margen
$pdf->SetXY($start_x, 20); // Ajusta la posición X y la Y para alinear el título
$pdf->Cell(0, 10, $title, 0, 1, 'L'); // Título alineado a la izquierda del resto del espacio

$pdf->Ln(30); // Salto de línea después del título

// Encabezados de la tabla
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Nombre', 1); // Ancho ajustado a 60
$pdf->Cell(60, 10, 'Contacto', 1); // Ancho ajustado a 60
$pdf->Cell(50, 10, 'Teléfono', 1); // Ancho ajustado a 50
$pdf->Cell(100, 10, 'Dirección', 1); // Ancho ajustado a 100
$pdf->Ln(); // Nueva línea

// Contenido de la tabla
$pdf->SetFont('Arial', '', 12);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(60, 10, $row['nombre'], 1);
        $pdf->Cell(60, 10, $row['contacto'], 1);
        $pdf->Cell(50, 10, $row['telefono'], 1);
        $pdf->Cell(100, 10, $row['direccion'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, 'No hay proveedores registrados.', 1, 1, 'C');
}

// Datos de contacto de la empresa (se coloca arriba a la derecha)
$pdf->SetFont('Arial', 'I', 10);
$contact_info = "MinasMarket | Tel: 3145755093 | Email: minasmarketcontact@gmail.com";


// Posicionamos el texto en la parte inferior derecha de la página actual
$pdf->SetXY(180, 179 ); // Y=210 - 10 coloca el texto cerca del borde inferior
$pdf->Cell(0, 10, $contact_info, 0, 0, 'R'); // Escribimos el texto alineado a la derecha

// Salida del PDF
$pdf->Output('D', 'lista_proveedores.pdf'); // Descarga el archivo como 'lista_proveedores.pdf'
exit;
?>
