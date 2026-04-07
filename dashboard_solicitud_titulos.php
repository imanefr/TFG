<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$titulo_dashboard = "Gestión de Solicitud de Títulos";
$is_admin = ($_SESSION['usuario_rol'] === 'admin');

// PROCESAR ACCIONES
$mensaje = '';
if ($_POST && isset($_POST['accion'])) {
    switch ($_POST['accion']) {
        case 'eliminar':
            $id = (int)$_POST['id'];
            $stmt = $conexion->prepare("DELETE FROM solicitudes_titulos WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) $mensaje = 'Solicitud de título eliminada correctamente';
            $stmt->close();
            break;
            
        case 'nueva':
            $tipo = trim($_POST['tipo']);
            $titulo = trim($_POST['titulo']);
            $texto = trim($_POST['texto']);
            $enlace_normativa = trim($_POST['enlace_normativa']);
            $enlace_autorizacion = trim($_POST['enlace_autorizacion']);
            $horario = trim($_POST['horario']);
            $stmt = $conexion->prepare("INSERT INTO solicitudes_titulos (tipo, titulo, texto, enlace_normativa, enlace_autorizacion, horario, activo) VALUES (?, ?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("ssssss", $tipo, $titulo, $texto, $enlace_normativa, $enlace_autorizacion, $horario);
            if ($stmt->execute()) $mensaje = 'Solicitud de título añadida correctamente';
            $stmt->close();
            break;
            
        case 'editar':
            $id = (int)$_POST['id'];
            $tipo = trim($_POST['tipo']);
            $titulo = trim($_POST['titulo']);
            $texto = trim($_POST['texto']);
            $enlace_normativa = trim($_POST['enlace_normativa']);
            $enlace_autorizacion = trim($_POST['enlace_autorizacion']);
            $horario = trim($_POST['horario']);
            $stmt = $conexion->prepare("UPDATE solicitudes_titulos SET tipo=?, titulo=?, texto=?, enlace_normativa=?, enlace_autorizacion=?, horario=? WHERE id=?");
            $stmt->bind_param("ssssssi", $tipo, $titulo, $texto, $enlace_normativa, $enlace_autorizacion, $horario, $id);
            if ($stmt->execute()) $mensaje = 'Solicitud de título actualizada correctamente';
            $stmt->close();
            break;
    }
}

// CARGAR SOLICITUDES DE TÍTULOS
$stmt = $conexion->prepare("SELECT * FROM solicitudes_titulos WHERE activo = 1 ORDER BY fecha_creacion DESC");
$stmt->execute();
$resultado = $stmt->get_result();
$solicitudes = [];
while ($fila = $resultado->fetch_assoc()) {
    $solicitudes[] = $fila;
}
$stmt->close();

// EDITAR MODO
$modo_edit = false;
$solicitud_edit = null;
if (isset($_GET['editar'])) {
    $id_edit = (int)$_GET['editar'];
    $stmt = $conexion->prepare("SELECT * FROM solicitudes_titulos WHERE id = ? AND activo = 1");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $solicitud_edit = $result->fetch_assoc();
    $modo_edit = $solicitud_edit !== null;
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Solicitudes Títulos - Dashboard Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_dashboard.css">
</head>
<body>
    <div class="dashboard_solicitud_titulos_container">
        <!-- HEADER -->
        <?php include 'dashboard_head.php'; ?>

        <?php if (!$is_admin): ?>
            <div class="dashboard_solicitud_titulos_no_admin">
                <i class="fas fa-lock dashboard_inicio_no_admin_icono"></i>
                <h2>Solo administradores pueden gestionar las solicitudes de títulos</h2>
            </div>
        <?php else: ?>
            
            <?php if ($mensaje): ?>
                <div class="dashboard_avisos_alert dashboard_avisos_alert_success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <!-- FORMULARIO NUEVA / EDITAR -->
            <div class="dashboard_solicitud_titulos_seccion_form <?php echo $modo_edit ? 'dashboard_solicitud_titulos_modo_edit' : ''; ?>">
                <h2 style="color: var(--gris-oscuro); margin-bottom: 2rem;">
                    <?php if ($modo_edit): ?>
                        <i class="fas fa-edit"></i> Editar Solicitud Título (ID: <?php echo $solicitud_edit['id']; ?>)
                    <?php else: ?>
                        <i class="fas fa-plus"></i> Nueva Solicitud de Título
                    <?php endif; ?>
                </h2>
                <form method="POST" class="dashboard_solicitud_titulos_form_grid">
                    <?php if ($modo_edit): ?>
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" value="<?php echo $solicitud_edit['id']; ?>">
                    <?php else: ?>
                        <input type="hidden" name="accion" value="nueva">
                    <?php endif; ?>
                    
                    <div class="dashboard_solicitud_titulos_form_group">
                        <label class="dashboard_solicitud_titulos_form_label">Etapa Educativa *</label>
                        <select name="tipo" class="dashboard_solicitud_titulos_form_select" required>
                            <option value="ESO" <?php echo ($modo_edit && $solicitud_edit['tipo'] == 'ESO') ? 'selected' : ''; ?>>ESO</option>
                            <option value="BACHILLERATO" <?php echo ($modo_edit && $solicitud_edit['tipo'] == 'BACHILLERATO') ? 'selected' : ''; ?>>BACHILLERATO</option>
                            <option value="FP" <?php echo ($modo_edit && $solicitud_edit['tipo'] == 'FP') ? 'selected' : ''; ?>>FP</option>
                        </select>
                    </div>
                    
                    <div class="dashboard_solicitud_titulos_form_group">
                        <label class="dashboard_solicitud_titulos_form_label">Título de la Solicitud *</label>
                        <input type="text" name="titulo" class="dashboard_solicitud_titulos_form_input" required 
                               value="<?php echo htmlspecialchars($modo_edit ? $solicitud_edit['titulo'] : ($_POST['titulo'] ?? '')); ?>"
                               placeholder="Ej: Solicitud Título Bachillerato">
                    </div>
                    
                    <div class="dashboard_solicitud_titulos_form_group" style="grid-column: 1 / -1;">
                        <label class="dashboard_solicitud_titulos_form_label">Descripción e Instrucciones *</label>
                        <textarea name="texto" class="dashboard_solicitud_titulos_form_textarea" required><?php echo htmlspecialchars($modo_edit ? $solicitud_edit['texto'] : ($_POST['texto'] ?? '')); ?></textarea>
                    </div>
                    
                    <div class="dashboard_solicitud_titulos_form_group">
                        <label class="dashboard_solicitud_titulos_form_label">Enlace Normativa</label>
                        <input type="url" name="enlace_normativa" class="dashboard_solicitud_titulos_form_input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $solicitud_edit['enlace_normativa'] : ($_POST['enlace_normativa'] ?? '')); ?>"
                               placeholder="https://sede.comunidad.madrid/...">
                    </div>
                    
                    <div class="dashboard_solicitud_titulos_form_group">
                        <label class="dashboard_solicitud_titulos_form_label">Enlace Autorización (Descargable)</label>
                        <input type="url" name="enlace_autorizacion" class="dashboard_solicitud_titulos_form_input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $solicitud_edit['enlace_autorizacion'] : ($_POST['enlace_autorizacion'] ?? '')); ?>"
                               placeholder="https://site.educa.madrid.org/...">
                    </div>

                    <div class="dashboard_solicitud_titulos_form_group">
                        <label class="dashboard_solicitud_titulos_form_label">Horario de Secretaría</label>
                        <input type="text" name="horario" class="dashboard_solicitud_titulos_form_input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $solicitud_edit['horario'] : ($_POST['horario'] ?? '')); ?>"
                               placeholder="Ej: L-V 9:30-13:00">
                    </div>
                    
                    <div class="dashboard_solicitud_titulos_btn_group">
                        <button type="submit" class="dashboard_solicitud_titulos_btn dashboard_solicitud_titulos_btn_primary">
                            <i class="fas fa-save"></i> <?php echo $modo_edit ? 'Actualizar Solicitud' : 'Publicar Solicitud'; ?>
                        </button>
                        <a href="dashboard_solicitud_titulos.php" class="dashboard_solicitud_titulos_btn dashboard_solicitud_titulos_btn_secondary">
                            <i class="fas fa-times"></i> <?php echo $modo_edit ? 'Cancelar Edición' : 'Limpiar'; ?>
                        </a>
                    </div>
                </form>
            </div>

            <!-- LISTA DE SOLICITUDES -->
            <div class="dashboard_solicitud_titulos_seccion_lista">
                <h2 style="color: var(--gris-oscuro); margin-bottom: 2rem;"><i class="fas fa-list"></i> Trámites Publicados (<?php echo count($solicitudes); ?>)</h2>
                <?php if (!empty($solicitudes)): ?>
                    <div class="dashboard_solicitud_titulos_grid">
                        <?php foreach ($solicitudes as $solicitud): ?>
                            <div class="dashboard_solicitud_titulos_card">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <span class="dashboard_solicitud_titulos_tag" style="background: <?php echo $solicitud['tipo'] == 'FP' ? 'var(--verde-principal)' : ($solicitud['tipo'] == 'ESO' ? '#EF4444' : 'var(--naranja)'); ?>; padding: 0.4rem 1rem; border-radius: 20px; color: white; font-size: 0.8rem; font-weight: 800;">
                                        <?php echo htmlspecialchars($solicitud['tipo']); ?>
                                    </span>
                                    <div class="dashboard_solicitud_titulos_card_fecha">
                                        <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($solicitud['fecha_creacion'])); ?>
                                    </div>
                                </div>
                                <h3 class="dashboard_solicitud_titulos_card_titulo"><?php echo htmlspecialchars($solicitud['titulo']); ?></h3>
                                <?php if ($solicitud['horario']): ?>
                                    <div style="font-size: 0.85rem; color: var(--azul); font-weight: 700;">
                                        <i class="fas fa-clock"></i> <?php echo htmlspecialchars($solicitud['horario']); ?>
                                    </div>
                                <?php endif; ?>
                                <p class="dashboard_solicitud_titulos_card_desc"><?php echo htmlspecialchars(substr($solicitud['texto'], 0, 150)); ?>...</p>
                                
                                <div class="dashboard_solicitud_titulos_medios">
                                    <?php if ($solicitud['enlace_normativa']): ?>
                                        <a href="<?php echo htmlspecialchars($solicitud['enlace_normativa']); ?>" class="dashboard_solicitud_titulos_noticia_enlace" target="_blank">
                                            <i class="fas fa-external-link-alt"></i> Guía/Normativa
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($solicitud['enlace_autorizacion']): ?>
                                        <a href="<?php echo htmlspecialchars($solicitud['enlace_autorizacion']); ?>" class="dashboard_solicitud_titulos_noticia_enlace" target="_blank">
                                            <i class="fas fa-file-pdf"></i> Autorización
                                        </a>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="dashboard_solicitud_titulos_acciones">
                                    <a href="?editar=<?php echo $solicitud['id']; ?>" class="dashboard_solicitud_titulos_btn_small btn_edit" title="Editar">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar esta solicitud de título?')">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?php echo $solicitud['id']; ?>">
                                        <button type="submit" class="dashboard_solicitud_titulos_btn_small btn_delete" title="Eliminar">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="dashboard_erasmus_vacio">
                        <i class="fas fa-certificate"></i>
                        <h3>No hay solicitudes publicadas aún.</h3>
                        <p>Publica el primer trámite con el formulario de arriba.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="dashboard_secretaria.php" class="dashboard_universal_volver">
            <button type="submit" class="dashboard_universal_btn_volver">
                <i class="fas fa-arrow-left"></i> Volver a Secretaría
            </button>
        </form>
    </div>
</body>
</html>
