<?php
// Inicia la sesión del usuario
session_start();

// Incluye conexión BD (opcional aquí pero mantiene consistencia)
include 'conexion.php';

// Borra todas las variables de sesión
session_unset();

// Destruye completamente la sesión
session_destroy();

// Redirige al login
header('Location: login.php');

// Detiene ejecución (importante después de header)
exit;
?>
