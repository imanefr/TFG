<?php
include 'conexion.php';
session_destroy();
header('Location: login.php');
exit;
?>
