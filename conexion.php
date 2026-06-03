<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "tienda_barrio"; 


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
