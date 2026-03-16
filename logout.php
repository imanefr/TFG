<?php
// logout.php
session_start();
include 'conexion.php';
session_destroy();
header('Location: login.php');
exit;
?>
