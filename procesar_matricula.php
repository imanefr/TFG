<?php
session_start();
require_once 'conexion.php';

if (!isset($_POST['guardar'])) {
    header('Location: dashboard_matricula.php');
    exit;
}

$id = intval($_POST['id'] ?? 0);
$etapa = trim($_POST['etapa'] ?? '');
$titulo = trim($_POST['titulo'] ?? '');
$fecha = trim($_POST['fecha'] ?? '');
$ruta_pdf = trim($_POST['ruta_pdf'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

if (empty($etapa) || empty($titulo) || empty($fecha) || empty($ruta_pdf) || empty($descripcion)) {
    $_SESSION['mensaje_error'] = 'Todos los campos son obligatorios';
    header('Location: dashboard_matricula.php');
    exit;
}

$tabla = match($etapa) {
    'eso' => 'matriculacion_eso',
    'bachillerato' => 'matriculacion_bachillerato',
    'fp' => 'matriculacion_fp',
    default => null
};

if (!$tabla) {
    $_SESSION['mensaje_error'] = 'Etapa no válida';
    header('Location: dashboard_matricula.php');
    exit;
}

// INSERT o UPDATE
if ($id > 0) {
    // EDITAR
    $sql = "UPDATE `$tabla` SET titulo=?, descripcion=?, ruta_pdf=?, fecha=?, activo=1 WHERE id=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssi", $titulo, $descripcion, $ruta_pdf, $fecha, $id);
    $mensaje = "Matrícula actualizada correctamente";
} else {
    // NUEVO
    $sql = "INSERT INTO `$tabla` (titulo, descripcion, ruta_pdf, fecha, activo) VALUES (?, ?, ?, ?, 1)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssss", $titulo, $descripcion, $ruta_pdf, $fecha);
    $mensaje = "Matrícula '$titulo' guardada correctamente";
}

if ($stmt->execute()) {
    $_SESSION['mensaje_ok'] = $mensaje;
} else {
    $_SESSION['mensaje_error'] = 'Error al guardar: ' . $stmt->error;
}

$stmt->close();
$conexion->close();

header('Location: dashboard_matricula.php');
exit;
?>
