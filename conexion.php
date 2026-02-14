<?php
// Variables para conectar a la base de datos MySQL
$host = "localhost"; // El servidor donde está la base de datos, normalmente localhost
$user = "root"; // Usuario de la base de datos, root es el por defecto
$pass = "1234"; // Contraseña del usuario, aquí pongo 1234 aunque dice que debe estar vacío
$db = "arboledatablas"; // Nombre de la base de datos que vamos a usar

// Creamos la conexión usando mysqli, que es para MySQL mejorado
$conexion = new mysqli($host, $user, $pass, $db);

// Comprobamos si hay algún error al conectar, si sí, paramos el programa con die()
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Ponemos el charset a utf8mb4 para que se vean bien los acentos y caracteres especiales
$conexion->set_charset("utf8mb4");

// Consulta SQL muy simple: sacamos todas las noticias ordenadas por fecha, las más nuevas primero
$sql = "SELECT * FROM noticias ORDER BY fecha DESC";
?>
