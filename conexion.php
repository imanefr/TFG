<?php

session_start();
$host = "localhost";
$user = "root";
$pass = "1234"; // Debe estar vacío
$db = "arboledatablas";

$conexion = new mysqli($host, $user, $pass, $db);

// Es vital añadir esto para ver errores si la conexión falla
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}$conexion->set_charset("utf8mb4");
// Configura el SQL para que el index lo use
$sql = "SELECT * FROM noticias ORDER BY fecha DESC";
?>