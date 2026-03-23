<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Título dinámico para el header global
$titulo_dashboard = "Dashboard Avisos";

$is_admin = ($_SESSION['usuario_rol'] === 'admin');
$mensaje = '';

// PROCESAR SOLO ADMIN + POST
if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    switch ($_POST['accion']) {
        case 'eliminar':
            $id = (int) $_POST['id'];
            $stmt = $conexion->prepare("DELETE FROM avisos WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $mensaje = 'Aviso eliminado correctamente';
            }
            $stmt->close();
            break;

        case 'nueva':
            $titulo = trim($_POST['titulo'] ?? '');
            $texto = trim($_POST['texto'] ?? '');
            $enlace = trim($_POST['enlace'] ?? '');
            $fecha = $_POST['fecha'] ?? date('Y-m-d H:i:s');
            $importante = isset($_POST['importante']) ? 1 : 0;

            if ($titulo && $texto) {
                $stmt = $conexion->prepare("
                    INSERT INTO avisos (titulo, texto, enlace, fecha, importante, ultima_edicion_fecha, ultima_edicion_usuario_id) 
                    VALUES (?, ?, ?, ?, ?, NOW(), ?)
                ");
                $stmt->bind_param("sssssi", $titulo, $texto, $enlace, $fecha, $importante, $_SESSION['usuario_id']);
                if ($stmt->execute()) {
                    $mensaje = 'Aviso añadido correctamente';
                }
                $stmt->close();
            } else {
                $mensaje = 'Título y texto son obligatorios';
            }
            break;

        case 'editar':
            $id = (int) $_POST['id'];
            $titulo = trim($_POST['titulo'] ?? '');
            $texto = trim($_POST['texto'] ?? '');
            $enlace = trim($_POST['enlace'] ?? '');
            $fecha = $_POST['fecha'] ?? date('Y-m-d H:i:s');
            $importante = isset($_POST['importante']) ? 1 : 0;

            $stmt = $conexion->prepare("
                UPDATE avisos 
                SET titulo=?, texto=?, enlace=?, fecha=?, importante=?, 
                    ultima_edicion_fecha=NOW(), ultima_edicion_usuario_id=? 
                WHERE id=?
            ");
            $stmt->bind_param("sssssii", $titulo, $texto, $enlace, $fecha, $importante, $_SESSION['usuario_id'], $id);
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $mensaje = 'Aviso actualizado correctamente';
            }
            $stmt->close();
            break;
    }
}

// CARGAR AVISOS (con JOIN usuario)
$stmt = $conexion->prepare("
    SELECT a.*, u.nombre as ultima_edicion_usuario_nombre
    FROM avisos a 
    LEFT JOIN usuarios u ON a.ultima_edicion_usuario_id = u.id
    ORDER BY a.importante DESC, a.fecha DESC
");
$stmt->execute();
$avisos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ✏️ MODO EDICIÓN
$modo_edit = false;
$aviso_edit = null;
if ($is_admin && isset($_GET['editar'])) {
    $id_edit = (int) $_GET['editar'];
    $stmt = $conexion->prepare("
        SELECT a.*, u.nombre as ultima_edicion_usuario_nombre
        FROM avisos a 
        LEFT JOIN usuarios u ON a.ultima_edicion_usuario_id = u.id
        WHERE a.id = ?
    ");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($aviso_edit = $result->fetch_assoc()) {
        $modo_edit = true;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestión Avisos - Dashboard Admin</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="style_dashboard.css">
    </head>
    <body>
        <div class="dashboard_avisos_container">
            <!-- HEADER -->
            <?php include 'dashboard_head.php'; ?>


            <?php if (!$is_admin): ?>
                <div class="dashboard_avisos_no_admin">
                    <i class="fas fa-lock" style="font-size: 4rem; color: var(--morado-claro); margin-bottom: 1rem;"></i>
                    <h2>Solo administradores pueden gestionar los avisos</h2>
                </div>
            <?php else: ?>

                <!-- ALERTA DE MENSAJE -->
                <?php if ($mensaje): ?>
                    <div class="dashboard_avisos_alert <?php echo strpos($mensaje, 'eliminado') !== false || strpos($mensaje, 'añadido') !== false || strpos($mensaje, 'actualizado') !== false ? 'dashboard_avisos_alert_success' : 'dashboard_avisos_alert_error'; ?>">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>

                <!-- FORMULARIO NUEVA / EDITAR -->
                <div class="dashboard_avisos_seccion_form <?php echo $modo_edit ? 'dashboard_avisos_modo_edit' : ''; ?>">
                    <h2>
                        <?php if ($modo_edit): ?>
                            <i class="fas fa-edit"></i> Editar Aviso (ID: <?php echo $aviso_edit['id']; ?>)
                        <?php else: ?>
                            <i class="fas fa-plus"></i> Nuevo Aviso
                        <?php endif; ?>
                    </h2>

                    <form method="POST" class="dashboard_avisos_form_grid">
                        <?php if ($modo_edit): ?>
                            <input type="hidden" name="accion" value="editar">
                            <input type="hidden" name="id" value="<?php echo $aviso_edit['id']; ?>">
                        <?php else: ?>
                            <input type="hidden" name="accion" value="nueva">
                        <?php endif; ?>

                        <div class="dashboard_avisos_form_group">
                            <label class="dashboard_avisos_form_label">Título *</label>
                            <input type="text" name="titulo" class="dashboard_avisos_form_input" required 
                                   value="<?php echo htmlspecialchars($modo_edit ? $aviso_edit['titulo'] : ($_POST['titulo'] ?? '')); ?>"
                                   placeholder="Ej: Reunión Consejo Escolar">
                        </div>

                        <div class="dashboard_avisos_form_group">
                            <label class="dashboard_avisos_form_label">Fecha y Hora *</label>
                            <input type="datetime-local" name="fecha" class="dashboard_avisos_form_input" required 
                                   value="<?php echo $modo_edit ? str_replace(' ', 'T', $aviso_edit['fecha']) : ($_POST['fecha'] ?? date('Y-m-d\TH:i')); ?>">
                        </div>

                        <div class="dashboard_avisos_form_group">
                            <label class="dashboard_avisos_form_label">Enlace (opcional)</label>
                            <input type="url" name="enlace" class="dashboard_avisos_form_input" 
                                   value="<?php echo htmlspecialchars($modo_edit ? $aviso_edit['enlace'] : ($_POST['enlace'] ?? '')); ?>"
                                   placeholder="documentos/matriculacion.pdf">
                        </div>

                        <div class="dashboard_avisos_form_group">
                            <label class="dashboard_avisos_form_label">
                                <input type="checkbox" name="importante" <?php echo ($modo_edit && $aviso_edit['importante']) || isset($_POST['importante']) ? 'checked' : ''; ?>>
                                Marcar como IMPORTANTE
                            </label>
                        </div>

                        <div class="dashboard_avisos_form_group" style="grid-column: 1 / -1;">
                            <label class="dashboard_avisos_form_label">Texto del Aviso *</label>
                            <textarea name="texto" class="dashboard_avisos_form_textarea" required><?php echo htmlspecialchars($modo_edit ? $aviso_edit['texto'] : ($_POST['texto'] ?? '')); ?></textarea>
                        </div>

                        <div class="dashboard_avisos_btn_group">
                            <button type="submit" class="dashboard_avisos_btn dashboard_avisos_btn_primary">
                                <i class="fas fa-save"></i> <?php echo $modo_edit ? 'Actualizar' : 'Añadir'; ?> Aviso
                            </button>
                            <?php if ($modo_edit): ?>
                                <a href="dashboard_avisos.php" class="dashboard_avisos_btn dashboard_avisos_btn_secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- LISTA DE AVISOS -->
                <div class="dashboard_avisos_seccion_form">
                    <h2><i class="fas fa-list"></i> Avisos Publicados (<?php echo count($avisos); ?>)</h2>
                    <?php if (!empty($avisos)): ?>
                        <div class="dashboard_avisos_noticias_grid">
                            <?php foreach ($avisos as $aviso): ?>
                                <div class="dashboard_avisos_noticia_card <?php echo $aviso['importante'] ? 'dashboard_avisos_importante' : ''; ?>">
                                    <h3 class="dashboard_avisos_noticia_titulo">
                                        <?php echo htmlspecialchars($aviso['titulo']); ?>
                                        <?php if ($aviso['importante']): ?>
                                            <span style="color: var(--rojo); font-weight: 700;">IMPORTANTE</span>
                                        <?php endif; ?>
                                    </h3>
                                    <div class="dashboard_avisos_noticia_fecha">
                                        <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($aviso['fecha'])); ?>
                                        <?php if (!empty($aviso['ultima_edicion_usuario_nombre'])): ?>
                                            <br><small style="color: #666; font-size: 0.85rem;"><?php echo htmlspecialchars($aviso['ultima_edicion_usuario_nombre']); ?></small>
                                        <?php endif; ?>
                                    </div>

                                    <div class="dashboard_avisos_noticia_contenido">
                                        <?php echo htmlspecialchars(substr($aviso['texto'], 0, 150)); ?>...
                                    </div>
                                    <?php if ($aviso['enlace']): ?>
                                        <a href="<?php echo htmlspecialchars($aviso['enlace']); ?>" class="dashboard_avisos_noticia_enlace" target="_blank">
                                            <i class="fas fa-external-link-alt"></i> Ver documento
                                        </a>
                                    <?php endif; ?>

                                    <div class="dashboard_avisos_acciones_botones">
                                        <a href="?editar=<?php echo $aviso['id']; ?>" class="dashboard_avisos_btn_small dashboard_avisos_btn_editar" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar este aviso?')">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?php echo $aviso['id']; ?>">
                                            <button type="submit" class="dashboard_avisos_btn_small dashboard_avisos_btn_delete" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem; color: var(--gris);">
                            <i class="fas fa-bell-slash" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                            <h3>No hay avisos</h3>
                            <p>Añade el primer aviso con el formulario de arriba</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- LOGOUT -->
            <form method="POST" action="dashboard_secretaria.php" class="dashboard_universal_volver">
                <button type="submit" class="dashboard_universal_btn_volver">
                    <i class="fas fa-arrow-left"> </i>  Volver
                </button>
            </form>
        </div>
    </body>
</html>
