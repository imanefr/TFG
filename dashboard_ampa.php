<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
$titulo_dashboard = "Dashboard AMPA";
$is_admin = ($_SESSION['usuario_rol'] === 'admin');

// PROCESAR ACCIONES
$mensaje = '';
if ($_POST && isset($_POST['accion'])) {
    switch ($_POST['accion']) {
        case 'eliminar':
            $id = (int) $_POST['id'];
            $stmt = $conexion->prepare("DELETE FROM ampa WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute())
                $mensaje = 'Entrada AMPA eliminada correctamente';
            $stmt->close();
            break;

        case 'activar':
            // Primero desactivar todas las entradas
            $stmt = $conexion->prepare("UPDATE ampa SET activo = 0");
            $stmt->execute();
            $stmt->close();
            
            // Activar solo la seleccionada
            $id = (int) $_POST['id'];
            $stmt = $conexion->prepare("UPDATE ampa SET activo = 1, ultima_edicion_fecha=NOW(), ultima_edicion_usuario_id=? WHERE id = ?");
            $stmt->bind_param("ii", $_SESSION['usuario_id'], $id);
            if ($stmt->execute())
                $mensaje = 'Entrada AMPA activada correctamente (única visible)';
            $stmt->close();
            break;

        case 'nueva':
            $titulo = trim($_POST['titulo']);
            $texto = trim($_POST['texto']);
            $enlace_formulario = trim($_POST['enlace_formulario']);
            $enlace_video = trim($_POST['enlace_video']);
            $imagen = isset($_POST['imagen_existente']) ? trim($_POST['imagen_existente']) : '';

            // SUBIDA DE IMAGEN
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'img/';
                if (!is_dir($upload_dir))
                    mkdir($upload_dir, 0777, true);

                $file_extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($file_extension, $allowed)) {
                    $new_filename = 'ampa_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                        $imagen = $upload_path;
                    }
                }
            }

            $stmt = $conexion->prepare("INSERT INTO ampa (titulo, texto, imagen, enlace_formulario, enlace_video, fecha_actualizacion, ultima_edicion_fecha, ultima_edicion_usuario_id, activo) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?, 0)");
            $stmt->bind_param("ssssi", $titulo, $texto, $imagen, $enlace_formulario, $enlace_video, $_SESSION['usuario_id']);
            if ($stmt->execute())
                $mensaje = 'Entrada AMPA añadida correctamente';
            $stmt->close();
            break;

        case 'editar':
            $id = (int) $_POST['id'];
            $titulo = trim($_POST['titulo']);
            $texto = trim($_POST['texto']);
            $enlace_formulario = trim($_POST['enlace_formulario']);
            $enlace_video = trim($_POST['enlace_video']);
            $imagen = isset($_POST['imagen_existente']) ? trim($_POST['imagen_existente']) : '';

            // SUBIDA DE IMAGEN NUEVA
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'img/';
                if (!is_dir($upload_dir))
                    mkdir($upload_dir, 0777, true);

                $file_extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($file_extension, $allowed)) {
                    $new_filename = 'ampa_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                        $imagen = $upload_path;
                        // Eliminar imagen anterior si existe
                        if (isset($_POST['imagen_existente']) && file_exists($_POST['imagen_existente'])) {
                            unlink($_POST['imagen_existente']);
                        }
                    }
                }
            }

            $stmt = $conexion->prepare("UPDATE ampa SET titulo=?, texto=?, imagen=?, enlace_formulario=?, enlace_video=?, fecha_actualizacion=NOW(), ultima_edicion_fecha=NOW(), ultima_edicion_usuario_id=? WHERE id=?");
            $stmt->bind_param("sssssii", $titulo, $texto, $imagen, $enlace_formulario, $enlace_video, $_SESSION['usuario_id'], $id);
            if ($stmt->execute())
                $mensaje = 'Entrada AMPA actualizada correctamente';
            $stmt->close();
            break;
    }
}

// CARGAR TODAS LAS ENTRADAS PARA GESTIÓN CON USUARIO - CONSULTA CORREGIDA ✅
$stmt = $conexion->prepare("
    SELECT a.*, u.nombre AS ultima_edicion_usuario_nombre
    FROM ampa a
    LEFT JOIN usuarios u ON a.ultima_edicion_usuario_id = u.id
    ORDER BY a.fecha_actualizacion DESC
");
$stmt->execute();
$resultado = $stmt->get_result();
$entradas = [];
while ($fila = $resultado->fetch_assoc()) {
    $entradas[] = $fila;
}
$stmt->close();

// MODO EDITAR
$modo_edit = false;
$entrada_edit = null;
if (isset($_GET['editar'])) {
    $id_edit = (int) $_GET['editar'];
    $stmt = $conexion->prepare("SELECT * FROM ampa WHERE id = ?");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $entrada_edit = $result->fetch_assoc();
    $modo_edit = $entrada_edit !== null;
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión AMPA - Dashboard Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_dashboard.css">
</head>
<body>
    <div class="dashboard_ampa_container">
        <!-- HEADER -->
        <?php include 'dashboard_head.php'; ?>

        <?php if (!$is_admin): ?>
            <div class="dashboard_ampa_no_admin">
                <i class="fas fa-lock"></i>
                <h2>Solo administradores pueden gestionar el contenido AMPA</h2>
            </div>
        <?php else: ?>

            <?php if ($mensaje): ?>
                <div class="dashboard_ampa_alert dashboard_ampa_alert_success">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <!-- FORMULARIO -->
            <div class="dashboard_ampa_seccion_form <?php echo $modo_edit ? 'dashboard_ampa_modo_edit' : ''; ?>">
                <h2>
                    <?php if ($modo_edit): ?>
                        <i class="fas fa-edit"></i> Editar Entrada (ID: <?php echo $entrada_edit['id']; ?>)
                    <?php else: ?>
                        <i class="fas fa-plus"></i> Nueva Entrada AMPA
                    <?php endif; ?>
                </h2>
                <form method="POST" class="dashboard_ampa_form_grid" enctype="multipart/form-data">
                    <?php if ($modo_edit): ?>
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" value="<?php echo $entrada_edit['id']; ?>">
                        <input type="hidden" name="imagen_existente" value="<?php echo htmlspecialchars($entrada_edit['imagen']); ?>">
                    <?php else: ?>
                        <input type="hidden" name="accion" value="nueva">
                    <?php endif; ?>

                    <div class="dashboard_ampa_form_group">
                        <label class="dashboard_ampa_form_label">Título *</label>
                        <input type="text" name="titulo" class="dashboard_ampa_form_input" required 
                               value="<?php echo htmlspecialchars($modo_edit ? $entrada_edit['titulo'] : ($_POST['titulo'] ?? '')); ?>"
                               placeholder="Ej: AMPA 2026 - Actividades Escolares">
                    </div>

                    <div class="dashboard_ampa_form_group">
                        <label class="dashboard_ampa_form_label">Enlace Formulario (opcional)</label>
                        <input type="url" name="enlace_formulario" class="dashboard_ampa_form_input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $entrada_edit['enlace_formulario'] : ($_POST['enlace_formulario'] ?? '')); ?>"
                               placeholder="https://docs.google.com/forms/...">
                    </div>

                    <div class="dashboard_ampa_form_group">
                        <label class="dashboard_ampa_form_label">Enlace Video (opcional)</label>
                        <input type="url" name="enlace_video" class="dashboard_ampa_form_input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $entrada_edit['enlace_video'] : ($_POST['enlace_video'] ?? '')); ?>"
                               placeholder="https://youtube.com/...">
                    </div>

                    <!-- INPUT FILE -->
                    <div class="dashboard_ampa_form_group">
                        <?php if ($modo_edit && $entrada_edit['imagen']): ?>
                            <label class="dashboard_ampa_form_label">Imagen actual:</label>
                            <div class="dashboard_ampa_imagen_actual">
                                <img src="<?php echo htmlspecialchars($entrada_edit['imagen']); ?>" alt="Imagen actual" style="max-width: 150px; max-height: 100px; border-radius: 8px;">
                                <p style="font-size: 0.9rem; color: var(--gris);"><?php echo htmlspecialchars(basename($entrada_edit['imagen'])); ?></p>
                            </div>
                        <?php endif; ?>
                        <label class="dashboard_ampa_form_label">Nueva Imagen (JPG, PNG, GIF, WEBP)</label>
                        <input type="file" name="imagen" class="dashboard_ampa_form_input" accept="image/*">
                        <small style="color: var(--gris);">Máx 5MB. Deja vacío para mantener la actual</small>
                    </div>

                    <div class="dashboard_ampa_form_group" style="grid-column: 1 / -1;">
                        <label class="dashboard_ampa_form_label">Texto *</label>
                        <textarea name="texto" class="dashboard_ampa_form_textarea" required><?php echo htmlspecialchars($modo_edit ? $entrada_edit['texto'] : ($_POST['texto'] ?? '')); ?></textarea>
                    </div>

                    <div class="dashboard_ampa_btn_group">
                        <button type="submit" class="dashboard_ampa_btn dashboard_ampa_btn_primary">
                            <i class="fas fa-save"></i> <?php echo $modo_edit ? 'Actualizar' : 'Añadir'; ?> Entrada
                        </button>
                    </div>
                </form>
            </div>

            <!-- LISTA DE ENTRADAS AMPA -->
            <div class="dashboard_ampa_seccion_lista">
                <h2><i class="fas fa-list"></i> Entradas AMPA (<?php echo count($entradas); ?>)</h2>
                <?php if (!empty($entradas)): ?>
                    <div class="dashboard_ampa_entradas_grid">
                        <?php foreach ($entradas as $entrada): ?>
                            <div class="dashboard_ampa_entrada_card <?php echo $entrada['activo'] ? 'dashboard_ampa_activa' : ''; ?>">
                                <?php if ($entrada['imagen']): ?>
                                    <div class="dashboard_ampa_entrada_imagen">
                                        <img src="<?php echo htmlspecialchars($entrada['imagen']); ?>" alt="<?php echo htmlspecialchars($entrada['titulo']); ?>">
                                    </div>
                                <?php endif; ?>
                                <h3 class="dashboard_ampa_entrada_titulo"><?php echo htmlspecialchars($entrada['titulo']); ?></h3>
                                <div class="dashboard_ampa_entrada_fecha">
                                    <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($entrada['fecha_actualizacion'])); ?>
                                    <?php if (!empty($entrada['ultima_edicion_usuario_nombre'])): ?>
                                        <br>
                                        <small style="color: #666; font-size: 0.85rem;">
                                            <?php echo htmlspecialchars($entrada['ultima_edicion_usuario_nombre']); ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <div class="dashboard_ampa_entrada_texto">
                                    <?php echo htmlspecialchars(substr($entrada['texto'], 0, 150)); ?>...
                                </div>
                                <div class="dashboard_ampa_entrada_enlaces">
                                    <?php if ($entrada['enlace_formulario']): ?>
                                        <a href="<?php echo htmlspecialchars($entrada['enlace_formulario']); ?>" class="dashboard_ampa_enlace_formulario" target="_blank">
                                            <i class="fas fa-file-alt"></i> Formulario
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($entrada['enlace_video']): ?>
                                        <a href="<?php echo htmlspecialchars($entrada['enlace_video']); ?>" class="dashboard_ampa_enlace_video" target="_blank">
                                            <i class="fas fa-video"></i> Video
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="dashboard_ampa_acciones_botones">
                                    <!-- BOTÓN ELEGIR/SELECCIONAR -->
                                    <form method="POST" style="display: contents;" onsubmit="return confirm('¿Seleccionar esta entrada como activa? Se desactivarán las demás.')" class="dashboard_ampa_activar_form">
                                        <input type="hidden" name="accion" value="activar">
                                        <input type="hidden" name="id" value="<?php echo $entrada['id']; ?>">
                                        <button type="submit" class="dashboard_ampa_btn_small dashboard_ampa_btn_activar <?php echo $entrada['activo'] ? 'dashboard_ampa_activo' : ''; ?>">
                                            <i class="fas fa-star <?php echo $entrada['activo'] ? 'fas' : 'far'; ?>"></i> 
                                            <?php echo $entrada['activo'] ? 'Activa' : 'Elegir'; ?>
                                        </button>
                                    </form>
                                    
                                    <a href="?editar=<?php echo $entrada['id']; ?>" class="dashboard_ampa_btn_small dashboard_ampa_btn_editar">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form method="POST" style="display: contents;" onsubmit="return confirm('¿Eliminar esta entrada AMPA?')" class="dashboard_ampa_eliminar_form">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?php echo $entrada['id']; ?>">
                                        <button type="submit" class="dashboard_ampa_btn_small dashboard_ampa_btn_delete">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="dashboard_ampa_vacio">
                        <i class="fas fa-users"></i>
                        <h3>No hay entradas AMPA</h3>
                        <p>Añade la primera entrada con el formulario de arriba</p>
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
