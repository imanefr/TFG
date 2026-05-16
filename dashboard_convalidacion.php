<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$titulo_dashboard = "Gestión de Convalidaciones";
$is_admin = ($_SESSION['usuario_rol'] === 'admin' || $_SESSION['usuario_rol'] === 'profesor' || $_SESSION['usuario_rol'] === 'otro');// PROCESAR ACCIONES

// PROCESAR ACCIONES
$mensaje = '';
if ($_POST && isset($_POST['accion'])) {
    switch ($_POST['accion']) {
        case 'eliminar':
            $id = (int)$_POST['id'];
            $stmt = $conexion->prepare("DELETE FROM convalidaciones WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) $mensaje = 'Convalidación eliminada correctamente';
            $stmt->close();
            break;
            
        case 'nueva':
            $tipo = trim($_POST['tipo']);
            $titulo = trim($_POST['titulo']);
            $texto = trim($_POST['texto']);
            $enlace_normativa = trim($_POST['enlace_normativa']);
            $enlace_formulario = trim($_POST['enlace_formulario']);
            $stmt = $conexion->prepare("INSERT INTO convalidaciones (tipo, titulo, texto, enlace_normativa, enlace_formulario, activo) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("sssss", $tipo, $titulo, $texto, $enlace_normativa, $enlace_formulario);
            if ($stmt->execute()) $mensaje = 'Convalidación añadida correctamente';
            $stmt->close();
            break;
            
        case 'editar':
            $id = (int)$_POST['id'];
            $tipo = trim($_POST['tipo']);
            $titulo = trim($_POST['titulo']);
            $texto = trim($_POST['texto']);
            $enlace_normativa = trim($_POST['enlace_normativa']);
            $enlace_formulario = trim($_POST['enlace_formulario']);
            $stmt = $conexion->prepare("UPDATE convalidaciones SET tipo=?, titulo=?, texto=?, enlace_normativa=?, enlace_formulario=? WHERE id=?");
            $stmt->bind_param("sssssi", $tipo, $titulo, $texto, $enlace_normativa, $enlace_formulario, $id);
            if ($stmt->execute()) $mensaje = 'Convalidación actualizada correctamente';
            $stmt->close();
            break;
    }
}

// CARGAR CONVALIDACIONES
$stmt = $conexion->prepare("SELECT * FROM convalidaciones WHERE activo = 1 ORDER BY fecha_creacion DESC");
$stmt->execute();
$resultado = $stmt->get_result();
$convalidaciones = [];
while ($fila = $resultado->fetch_assoc()) {
    $convalidaciones[] = $fila;
}
$stmt->close();

// EDITAR MODO
$modo_edit = false;
$convalidacion_edit = null;
if (isset($_GET['editar'])) {
    $id_edit = (int)$_GET['editar'];
    $stmt = $conexion->prepare("SELECT * FROM convalidaciones WHERE id = ? AND activo = 1");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $convalidacion_edit = $result->fetch_assoc();
    $modo_edit = $convalidacion_edit !== null;
    $stmt->close();
}
?>
<!-- HEADER -->
        <?php include 'dashboard_head.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Convalidaciones - Dashboard Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_dashboard.css">
</head>
<body>
    <div class="dashboard_convalidacion_container">
        

        <?php if (!$is_admin): ?>
            <div class="dashboard_convalidacion_no_admin">
                <i class="fas fa-lock dashboard_inicio_no_admin_icono"></i>
                <h2>Solo administradores pueden gestionar las convalidaciones</h2>
            </div>
        <?php else: ?>
            
            <?php if ($mensaje): ?>
                <div class="dashboard_avisos_alert dashboard_avisos_alert_success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <!-- FORMULARIO NUEVA / EDITAR -->
            <div class="dashboard_convalidacion_seccion_form <?php echo $modo_edit ? 'dashboard_convalidacion_modo_edit' : ''; ?>">
                <h2 style="color: var(--gris-oscuro); margin-bottom: 2rem;">
                    <?php if ($modo_edit): ?>
                        <i class="fas fa-edit"></i> Editar Convalidación (ID: <?php echo $convalidacion_edit['id']; ?>)
                    <?php else: ?>
                        <i class="fas fa-plus"></i> Nueva Convalidación
                    <?php endif; ?>
                </h2>
                <form method="POST" class="dashboard_convalidacion_form_grid">
                    <?php if ($modo_edit): ?>
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" value="<?php echo $convalidacion_edit['id']; ?>">
                    <?php else: ?>
                        <input type="hidden" name="accion" value="nueva">
                    <?php endif; ?>
                    
                    <div class="dashboard_convalidacion_form_group">
                        <label class="dashboard_convalidacion_form_label">Etapa Educativa *</label>
                        <select name="tipo" class="dashboard_convalidacion_form_select" required>
                            <option value="ESO" <?php echo ($modo_edit && $convalidacion_edit['tipo'] == 'ESO') ? 'selected' : ''; ?>>ESO</option>
                            <option value="BACHILLERATO" <?php echo ($modo_edit && $convalidacion_edit['tipo'] == 'BACHILLERATO') ? 'selected' : ''; ?>>BACHILLERATO</option>
                            <option value="FP" <?php echo ($modo_edit && $convalidacion_edit['tipo'] == 'FP') ? 'selected' : ''; ?>>FP</option>
                        </select>
                    </div>
                    
                    <div class="dashboard_convalidacion_form_group">
                        <label class="dashboard_convalidacion_form_label">Título de la Convalidación *</label>
                        <input type="text" name="titulo" class="dashboard_convalidacion_form_input" required 
                               value="<?php echo htmlspecialchars($modo_edit ? $convalidacion_edit['titulo'] : ($_POST['titulo'] ?? '')); ?>"
                               placeholder="Ej: Convalidación ESO">
                    </div>
                    
                    <div class="dashboard_convalidacion_form_group" style="grid-column: 1 / -1;">
                        <label class="dashboard_convalidacion_form_label">Descripción / Instrucciones *</label>
                        <textarea name="texto" class="dashboard_convalidacion_form_textarea" required><?php echo htmlspecialchars($modo_edit ? $convalidacion_edit['texto'] : ($_POST['texto'] ?? '')); ?></textarea>
                    </div>
                    
                    <div class="dashboard_convalidacion_form_group">
                        <label class="dashboard_convalidacion_form_label">Enlace Normativa (Boletín oficial)</label>
                        <input type="url" name="enlace_normativa" class="dashboard_convalidacion_form_input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $convalidacion_edit['enlace_normativa'] : ($_POST['enlace_normativa'] ?? '')); ?>"
                               placeholder="https://www.bocm.es/...">
                    </div>
                    
                    <div class="dashboard_convalidacion_form_group">
                        <label class="dashboard_convalidacion_form_label">Enlace Formulario (Solicitud)</label>
                        <input type="url" name="enlace_formulario" class="dashboard_convalidacion_form_input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $convalidacion_edit['enlace_formulario'] : ($_POST['enlace_formulario'] ?? '')); ?>"
                               placeholder="https://site.educa.madrid.org/...">
                    </div>
                    
                    <div class="dashboard_convalidacion_btn_group">
                        <button type="submit" class="dashboard_convalidacion_btn dashboard_convalidacion_btn_primary">
                            <i class="fas fa-save"></i> <?php echo $modo_edit ? 'Actualizar Cambios' : 'Publicar Convalidación'; ?>
                        </button>
                        <a href="dashboard_convalidacion.php" class="dashboard_convalidacion_btn dashboard_convalidacion_btn_secondary">
                            <i class="fas fa-times"></i> <?php echo $modo_edit ? 'Cancelar Edición' : 'Limpiar'; ?>
                        </a>
                    </div>
                </form>
            </div>

            <!-- LISTA DE CONVALIDACIONES -->
            <div class="dashboard_convalidacion_seccion_lista">
                <h2 style="color: var(--gris-oscuro); margin-bottom: 2rem;"><i class="fas fa-list"></i> Convalidaciones Publicadas (<?php echo count($convalidaciones); ?>)</h2>
                <?php if (!empty($convalidaciones)): ?>
                    <div class="dashboard_convalidacion_grid">
                        <?php foreach ($convalidaciones as $convalidacion): ?>
                            <div class="dashboard_convalidacion_card">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <span class="dashboard_convalidacion_tag tag_<?php echo strtolower($convalidacion['tipo']); ?>" style="background: <?php echo $convalidacion['tipo'] == 'FP' ? 'var(--verde-principal)' : ($convalidacion['tipo'] == 'ESO' ? '#EF4444' : 'var(--naranja)'); ?>; padding: 0.4rem 1rem; border-radius: 20px; color: white; font-size: 0.8rem; font-weight: 800;">
                                        <?php echo htmlspecialchars($convalidacion['tipo']); ?>
                                    </span>
                                    <div class="dashboard_convalidacion_card_fecha">
                                        <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($convalidacion['fecha_creacion'])); ?>
                                    </div>
                                </div>
                                <h3 class="dashboard_convalidacion_card_titulo"><?php echo htmlspecialchars($convalidacion['titulo']); ?></h3>
                                <p class="dashboard_convalidacion_card_desc"><?php echo htmlspecialchars(substr($convalidacion['texto'], 0, 150)); ?>...</p>
                                
                                <div class="dashboard_convalidacion_medios">
                                    <?php if ($convalidacion['enlace_normativa']): ?>
                                        <a href="<?php echo htmlspecialchars($convalidacion['enlace_normativa']); ?>" class="dashboard_convalidacion_noticia_enlace" target="_blank">
                                            <i class="fas fa-external-link-alt"></i> Normativa
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($convalidacion['enlace_formulario']): ?>
                                        <a href="<?php echo htmlspecialchars($convalidacion['enlace_formulario']); ?>" class="dashboard_convalidacion_noticia_enlace" target="_blank">
                                            <i class="fas fa-file-signature"></i> Formulario
                                        </a>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="dashboard_convalidacion_acciones">
                                    <a href="?editar=<?php echo $convalidacion['id']; ?>" class="dashboard_convalidacion_btn_small btn_edit" title="Editar">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar esta convalidación?')">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?php echo $convalidacion['id']; ?>">
                                        <button type="submit" class="dashboard_convalidacion_btn_small btn_delete" title="Eliminar">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="dashboard_erasmus_vacio">
                        <i class="fas fa-balance-scale"></i>
                        <h3>No hay convalidaciones publicadas aún.</h3>
                        <p>Añade el primer trámite con el formulario de arriba.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="dashboard.php" class="dashboard_universal_volver">
            <button type="submit" class="dashboard_universal_btn_volver">
                <i class="fas fa-arrow-left"></i> Volver 
            </button>
        </form>
    </div>
</body>
</html>

</body>
</html>
