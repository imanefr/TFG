<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$titulo_dashboard = "Gestión de Documentos Institucionales";
$is_admin = ($_SESSION['usuario_rol'] === 'admin' || $_SESSION['usuario_rol'] === 'profesor' || $_SESSION['usuario_rol'] === 'otro');// PROCESAR ACCIONES

// PROCESAR ACCIONES
$mensaje = '';
if ($_POST && isset($_POST['accion'])) {
    switch ($_POST['accion']) {
        case 'eliminar':
            $id = (int)$_POST['id'];
            $stmt = $conexion->prepare("DELETE FROM documentos_institucionales WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) $mensaje = 'Documento eliminado correctamente';
            $stmt->close();
            break;
            
        case 'nueva':
            $titulo = trim($_POST['titulo']);
            $descripcion = trim($_POST['descripcion']);
            $fecha = $_POST['fecha_publicacion'];
            $tipo = trim($_POST['tipo_archivo']);
            $enlace = trim($_POST['url']);
            $orden = (int)$_POST['orden'];
            $stmt = $conexion->prepare("INSERT INTO documentos_institucionales (titulo, descripcion, fecha_publicacion, tipo_archivo, url, orden, activo) VALUES (?, ?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("sssssi", $titulo, $descripcion, $fecha, $tipo, $enlace, $orden);
            if ($stmt->execute()) $mensaje = 'Documento añadido correctamente';
            $stmt->close();
            break;
            
        case 'editar':
            $id = (int)$_POST['id'];
            $titulo = trim($_POST['titulo']);
            $descripcion = trim($_POST['descripcion']);
            $fecha = $_POST['fecha_publicacion'];
            $tipo = trim($_POST['tipo_archivo']);
            $enlace = trim($_POST['url']);
            $orden = (int)$_POST['orden'];
            $stmt = $conexion->prepare("UPDATE documentos_institucionales SET titulo=?, descripcion=?, fecha_publicacion=?, tipo_archivo=?, url=?, orden=? WHERE id=?");
            $stmt->bind_param("sssssii", $titulo, $descripcion, $fecha, $tipo, $enlace, $orden, $id);
            if ($stmt->execute()) $mensaje = 'Documento actualizado correctamente';
            $stmt->close();
            break;
    }
}

// CARGAR DOCUMENTOS
$stmt = $conexion->prepare("SELECT * FROM documentos_institucionales WHERE activo = 1 ORDER BY orden ASC, fecha_publicacion DESC");
$stmt->execute();
$resultado = $stmt->get_result();
$documentos = [];
while ($fila = $resultado->fetch_assoc()) {
    $documentos[] = $fila;
}
$stmt->close();

// EDITAR MODO
$modo_edit = false;
$documento_edit = null;
if (isset($_GET['editar'])) {
    $id_edit = (int)$_GET['editar'];
    $stmt = $conexion->prepare("SELECT * FROM documentos_institucionales WHERE id = ? AND activo = 1");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $documento_edit = $result->fetch_assoc();
    $modo_edit = $documento_edit !== null;
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
    <title>Gestión Documentos Institucionales - Dashboard Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_dashboard.css">
</head>
<body>
    <div class="dashboard_doc_inst_container">
      

        <?php if (!$is_admin): ?>
            <div class="dashboard_doc_inst_no_admin">
                <i class="fas fa-lock dashboard_inicio_no_admin_icono"></i>
                <h2>Solo administradores pueden gestionar los documentos institucionales</h2>
            </div>
        <?php else: ?>
            
            <?php if ($mensaje): ?>
                <div class="dashboard_avisos_alert dashboard_avisos_alert_success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <!-- FORMULARIO NUEVA / EDITAR -->
            <div class="dashboard_doc_inst_seccion_form <?php echo $modo_edit ? 'dashboard_doc_inst_modo_edit' : ''; ?>">
                <h2 style="color: var(--gris-oscuro); margin-bottom: 2rem;">
                    <?php if ($modo_edit): ?>
                        <i class="fas fa-edit"></i> Editar Documento (ID: <?php echo $documento_edit['id']; ?>)
                    <?php else: ?>
                        <i class="fas fa-plus"></i> Nuevo Documento Institucional
                    <?php endif; ?>
                </h2>
                <form method="POST" class="dashboard_doc_inst_form_grid">
                    <?php if ($modo_edit): ?>
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" value="<?php echo $documento_edit['id']; ?>">
                    <?php else: ?>
                        <input type="hidden" name="accion" value="nueva">
                    <?php endif; ?>
                    
                    <div class="dashboard_doc_inst_form_group">
                        <label class="dashboard_doc_inst_form_label">Título del Documento *</label>
                        <input type="text" name="titulo" class="dashboard_doc_inst_form_input" required 
                               value="<?php echo htmlspecialchars($modo_edit ? $documento_edit['titulo'] : ($_POST['titulo'] ?? '')); ?>"
                               placeholder="Ej: Proyecto Educativo del Centro">
                    </div>
                    
                    <div class="dashboard_doc_inst_form_group">
                        <label class="dashboard_doc_inst_form_label">Tipo de Archivo *</label>
                        <select name="tipo_archivo" class="dashboard_doc_inst_form_select" required>
                            <option value="pdf" <?php echo ($modo_edit && $documento_edit['tipo_archivo'] == 'pdf') ? 'selected' : ''; ?>>PDF</option>
                            <option value="docx" <?php echo ($modo_edit && $documento_edit['tipo_archivo'] == 'docx') ? 'selected' : ''; ?>>DOCX</option>
                            <option value="doc" <?php echo ($modo_edit && $documento_edit['tipo_archivo'] == 'doc') ? 'selected' : ''; ?>>DOC</option>
                        </select>
                    </div>

                    <div class="dashboard_doc_inst_form_group">
                        <label class="dashboard_doc_inst_form_label">Fecha de Publicación *</label>
                        <input type="date" name="fecha_publicacion" class="dashboard_doc_inst_form_input" required 
                               value="<?php echo $modo_edit ? $documento_edit['fecha_publicacion'] : ($_POST['fecha_publicacion'] ?? date('Y-m-d')); ?>">
                    </div>

                    <div class="dashboard_doc_inst_form_group">
                        <label class="dashboard_doc_inst_form_label">Orden de Visualización</label>
                        <input type="number" name="orden" class="dashboard_doc_inst_form_input" min="1" max="100"
                               value="<?php echo $modo_edit ? $documento_edit['orden'] : ($_POST['orden'] ?? '1'); ?>">
                    </div>
                    
                    <div class="dashboard_doc_inst_form_group" style="grid-column: 1 / -1;">
                        <label class="dashboard_doc_inst_form_label">Descripción del Contenido *</label>
                        <textarea name="descripcion" class="dashboard_doc_inst_form_textarea" required><?php echo htmlspecialchars($modo_edit ? $documento_edit['descripcion'] : ($_POST['descripcion'] ?? '')); ?></textarea>
                    </div>
                    
                    <div class="dashboard_doc_inst_form_group" style="grid-column: 1 / -1;">
                        <label class="dashboard_doc_inst_form_label">URL del Documento (EducaMadrid u otro) *</label>
                        <input type="url" name="url" class="dashboard_doc_inst_form_input" required 
                               value="<?php echo htmlspecialchars($modo_edit ? $documento_edit['url'] : ($_POST['url'] ?? '')); ?>"
                               placeholder="https://site.educa.madrid.org/...">
                    </div>
                    
                    <div class="dashboard_doc_inst_btn_group">
                        <button type="submit" class="dashboard_doc_inst_btn dashboard_doc_inst_btn_primary">
                            <i class="fas fa-save"></i> <?php echo $modo_edit ? 'Actualizar Documento' : 'Publicar Documento'; ?>
                        </button>
                        <a href="dashboard_doc_institucionales.php" class="dashboard_doc_inst_btn dashboard_doc_inst_btn_secondary">
                            <i class="fas fa-times"></i> <?php echo $modo_edit ? 'Cancelar Edición' : 'Limpiar'; ?>
                        </a>
                    </div>
                </form>
            </div>

            <!-- LISTA DE DOCUMENTOS -->
            <div class="dashboard_doc_inst_seccion_lista">
                <h2 style="color: var(--gris-oscuro); margin-bottom: 2rem;"><i class="fas fa-list"></i> Documentos Publicados (<?php echo count($documentos); ?>)</h2>
                <?php if (!empty($documentos)): ?>
                    <div class="dashboard_doc_inst_grid">
                        <?php foreach ($documentos as $documento): ?>
                            <div class="dashboard_doc_inst_card">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <span class="dashboard_doc_inst_tag">
                                        <i class="fas fa-file-<?php echo ($documento['tipo_archivo'] == 'pdf') ? 'pdf' : 'word'; ?>"></i> <?php echo strtoupper(htmlspecialchars($documento['tipo_archivo'])); ?>
                                    </span>
                                    <div class="dashboard_doc_inst_card_fecha">
                                        <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($documento['fecha_publicacion'])); ?>
                                    </div>
                                </div>
                                <div style="font-size: 0.8rem; color: var(--gris); font-weight: 700;">Posición: <?php echo $documento['orden']; ?></div>
                                <h3 class="dashboard_doc_inst_card_titulo"><?php echo htmlspecialchars($documento['titulo']); ?></h3>
                                <p class="dashboard_doc_inst_card_desc"><?php echo htmlspecialchars(substr($documento['descripcion'], 0, 150)); ?>...</p>
                                
                                <div class="dashboard_doc_inst_medios">
                                    <a href="<?php echo htmlspecialchars($documento['url']); ?>" class="dashboard_doc_inst_noticia_enlace" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> Abrir Documento
                                    </a>
                                </div>
                                
                                <div class="dashboard_doc_inst_acciones">
                                    <a href="?editar=<?php echo $documento['id']; ?>" class="dashboard_doc_inst_btn_small btn_edit" title="Editar">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar este documento?')">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?php echo $documento['id']; ?>">
                                        <button type="submit" class="dashboard_doc_inst_btn_small btn_delete" title="Eliminar">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="dashboard_erasmus_vacio">
                        <i class="fas fa-folder-open"></i>
                        <h3>No hay documentos publicados aún.</h3>
                        <p>Publica el primer documento institucional con el formulario de arriba.</p>
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
