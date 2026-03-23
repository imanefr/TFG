<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
// Título dinámico para el header global
$titulo_dashboard = "Dashboard Contacto";

$is_admin = ($_SESSION['usuario_rol'] === 'admin');

// CARGAR DATOS CONTACTO SECRETARÍA
$stmt = $conexion->prepare("SELECT * FROM contacto_secretaria WHERE id = 1");
$stmt->execute();
$resultado = $stmt->get_result();
$contacto_data = $resultado->fetch_assoc();
$stmt->close();

// PROCESAR ACCIONES
$mensaje = '';
if ($_POST && isset($_POST['accion'])) {
    $telefono = trim($_POST['telefono']);
    $fax = trim($_POST['fax']);
    $horario = trim($_POST['horario']);
    $correo = trim($_POST['correo']);
    $aviso = trim($_POST['aviso']);

    $stmt = $conexion->prepare("UPDATE contacto_secretaria SET telefono=?, fax=?, horario=?, correo=?, aviso=? WHERE id=1");
    $stmt->bind_param("sssss", $telefono, $fax, $horario, $correo, $aviso);

    if ($stmt->execute()) {
        $mensaje = 'Datos de contacto actualizados correctamente';
        header("Refresh:0");
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestión Contacto Secretaría - Dashboard Admin</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="style_dashboard.css">
    </head>
    <body>
        <div class="dashboard_contacto_container">
            <!-- HEADER CON BOTÓN VOLVER -->
            <?php include 'dashboard_head.php'; ?>


            <?php if (!$is_admin): ?>
                <div class="dashboard_contacto_no_admin">
                    <i class="fas fa-lock" style="font-size: 4rem; color: var(--morado-claro); margin-bottom: 1rem;"></i>
                    <h2>Solo administradores pueden gestionar el contenido</h2>
                </div>
            <?php else: ?>

                <?php if ($mensaje): ?>
                    <div class="dashboard_contacto_alert dashboard_contacto_alert_success">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>

                <!-- FORMULARIO EDITAR CONTACTO -->
                <div class="dashboard_contacto_seccion_form">
                    <h2><i class="fas fa-edit"></i> Editar Datos de Contacto</h2>
                    <form method="POST" class="dashboard_contacto_form_grid">
                        <input type="hidden" name="accion" value="editar">

                        <div class="dashboard_contacto_form_group">
                            <label class="dashboard_contacto_form_label">Teléfono</label>
                            <input type="text" name="telefono" class="dashboard_contacto_form_input" required 
                                   value="<?php echo htmlspecialchars($contacto_data['telefono'] ?? ''); ?>">
                        </div>

                        <div class="dashboard_contacto_form_group">
                            <label class="dashboard_contacto_form_label">Fax</label>
                            <input type="text" name="fax" class="dashboard_contacto_form_input" required 
                                   value="<?php echo htmlspecialchars($contacto_data['fax'] ?? ''); ?>">
                        </div>

                        <div class="dashboard_contacto_form_group">
                            <label class="dashboard_contacto_form_label">Horario</label>
                            <input type="text" name="horario" class="dashboard_contacto_form_input" required 
                                   value="<?php echo htmlspecialchars($contacto_data['horario'] ?? ''); ?>">
                        </div>

                        <div class="dashboard_contacto_form_group">
                            <label class="dashboard_contacto_form_label">Correo Electrónico</label>
                            <input type="email" name="correo" class="dashboard_contacto_form_input" required 
                                   value="<?php echo htmlspecialchars($contacto_data['correo'] ?? ''); ?>">
                        </div>

                        <div class="dashboard_contacto_form_group" style="grid-column: 1 / -1;">
                            <label class="dashboard_contacto_form_label">Aviso Importante</label>
                            <textarea name="aviso" class="dashboard_contacto_form_textarea" required rows="3"><?php echo htmlspecialchars($contacto_data['aviso'] ?? ''); ?></textarea>
                        </div>

                        <div class="dashboard_contacto_btn_group">
                            <button type="submit" class="dashboard_contacto_btn dashboard_contacto_btn_primary">
                                <i class="fas fa-save"></i> Actualizar Contacto
                            </button>
                            <a href="dashboard_inicio.php" class="dashboard_contacto_btn dashboard_contacto_btn_secondary">
                                <i class="fas fa-times"></i> Volver al Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <form method="POST" action="dashboard_secretaria.php" class="dashboard_universal_volver">
            <button type="submit" class="dashboard_universal_btn_volver">
                <i class="fas fa-arrow-left"> </i>  Volver
            </button>
        </form>
        </div>
    </body>
</html>
