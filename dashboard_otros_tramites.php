<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
$titulo_dashboard = "Dashboard Otros Trámites";

$is_admin = ($_SESSION['usuario_rol'] === 'admin');

// PROCESAR ACCIONES
$mensaje = '';
if ($_POST && isset($_POST['accion'])) {
    switch ($_POST['accion']) {
        case 'eliminar':
            $id = (int) $_POST['id'];
            $stmt = $conexion->prepare("DELETE FROM otros_tramites WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute())
                $mensaje = 'Sección eliminada correctamente';
            $stmt->close();
            break;

        case 'nueva':
            $titulo = trim($_POST['titulo']);
            $texto = trim($_POST['texto']);
            $fecha = $_POST['fecha'];
            $enlace = trim($_POST['enlace']);
            $texto_enlace = trim($_POST['texto_enlace']);
            $imagen = isset($_POST['imagen_existente']) ? trim($_POST['imagen_existente']) : '';
            $video = trim($_POST['video']);
            $pdf = trim($_POST['pdf']);
            // $nombre = $_SESSION['usuario_nombre'];

            // SUBIDA DE IMAGEN
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'img/';
                if (!is_dir($upload_dir))
                    mkdir($upload_dir, 0777, true);

                $file_extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($file_extension, $allowed)) {
                    $new_filename = 'bolsa_empleo_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                        $imagen = $upload_path;
                    }
                }
            }

            $stmt = $conexion->prepare("INSERT INTO otros_tramites (titulo, texto, link, texto_link, imagen, video, pdf, fecha_publicacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $titulo, $texto, $enlace, $texto_enlace, $imagen, $video, $pdf, $fecha);
            if ($stmt->execute())
                $mensaje = 'Noticia añadida correctamente';
            $stmt->close();
            break;

        case 'editar':
            $id = (int) $_POST['id'];
            $titulo = trim($_POST['titulo']);
            $texto = trim($_POST['texto']);
            $fecha = $_POST['fecha'];
            $enlace = trim($_POST['enlace']);
            $texto_enlace = trim($_POST['texto_enlace']);
            $imagen = isset($_POST['imagen_existente']) ? trim($_POST['imagen_existente']) : '';
            $video = trim($_POST['video']);
            $pdf = trim($_POST['pdf']);

            // SUBIDA DE IMAGEN NUEVA (reemplaza la anterior)
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'img/';
                if (!is_dir($upload_dir))
                    mkdir($upload_dir, 0777, true);

                $file_extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($file_extension, $allowed)) {
                    $new_filename = 'bolsa_empleo_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
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

            // ACTUALIZAR noticia existente
            $stmt = $conexion->prepare("
                UPDATE otros_tramites 
                SET titulo=?, texto=?, fecha_publicacion=?, link=?, texto_link=?, imagen=?, video=?, pdf=?,
                    ultima_edicion_usuario_id=?, ultima_edicion_fecha=NOW()
                WHERE id=?
            ");
            $stmt->bind_param("ssssssssii", $titulo, $texto, $fecha, $enlace, $texto_enlace, $imagen, $video, $pdf, $_SESSION['usuario_id'], $id);
            if ($stmt->execute())
                $mensaje = 'Noticia actualizada correctamente';
            $stmt->close();
            break;
    }
}

// CARGAR NOTICIAS CON NOMBRE DEL USUARIO - CONSULTA CORREGIDA ✅
$stmt = $conexion->prepare("
    SELECT n.*, u.nombre AS ultima_edicion_usuario_nombre
    FROM otros_tramites n
    LEFT JOIN usuarios u ON n.ultima_edicion_usuario_id = u.id
    ORDER BY n.fecha_publicacion DESC
");

$stmt->execute();
$resultado = $stmt->get_result();
$noticias = [];
while ($fila = $resultado->fetch_assoc()) {
    $noticias[] = $fila;
}
$stmt->close();

// EDITAR MODO
$modo_edit = false;
$noticia_edit = null;
if (isset($_GET['editar'])) {
    $id_edit = (int) $_GET['editar'];
    $stmt = $conexion->prepare("SELECT * FROM otros_tramites WHERE id = ?");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $noticia_edit = $result->fetch_assoc();
    $modo_edit = $noticia_edit !== null;
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Otros Trámites - Dashboard Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_dashboard.css">
</head>
<body>
    <div class="dashboard_erasmus_container">
        <!-- HEADER -->
        <?php include 'dashboard_head.php'; ?>

        <?php if (!$is_admin): ?>
            <div class="dashboard_erasmus_no_admin">
                <i class="fas fa-lock"></i>
                <h2>Solo administradores pueden gestionar el contenido</h2>
            </div>
        <?php else: ?>

            <?php if ($mensaje): ?>
                <div class="dashboard_erasmus_alert dashboard_erasmus_alert_success">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <!-- FORMULARIO -->
            <div class="dashboard_erasmus_seccion_form <?php echo $modo_edit ? 'dashboard_erasmus_modo_edit' : ''; ?>">
                <h2>
                    <?php if ($modo_edit): ?>
                        <i class="fas fa-edit"></i> Editar Noticia (ID: <?php echo $noticia_edit['id']; ?>)
                    <?php else: ?>
                        <i class="fas fa-plus"></i> Nueva Noticia
                    <?php endif; ?>
                </h2>
                <form method="POST" class="dashboard_erasmus_form_grid" enctype="multipart/form-data">
                    <?php if ($modo_edit): ?>
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" value="<?php echo $noticia_edit['id']; ?>">
                        <input type="hidden" name="imagen_existente" value="<?php echo htmlspecialchars($noticia_edit['imagen']); ?>">
                    <?php else: ?>
                        <input type="hidden" name="accion" value="nueva">
                    <?php endif; ?>

                    <div class="dashboard_erasmus_form_group">
                        <label class="dashboard_erasmus_form_label">Título *</label>
                        <input type="text" name="titulo" class="dashboard_erasmus_form_input" required 
                               value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['titulo'] : ($_POST['titulo'] ?? '')); ?>"
                               placeholder="Ej: Listado Admisiones 2025-26">
                    </div>

                    <div class="dashboard_erasmus_form_group">
                        <label class="dashboard_erasmus_form_label">Fecha *</label>
                        <input type="date" name="fecha" class="dashboard_erasmus_form_input" required 
                               value="<?php echo $modo_edit ? $noticia_edit['fecha_publicacion'] : ($_POST['fecha'] ?? date('Y-m-d')); ?>">
                    </div>

                    <div class="dashboard_erasmus_form_group">
                        <label class="dashboard_erasmus_form_label">Enlace (opcional)</label>
                        <input type="text" name="texto_enlace" class="dashboard_erasmus_form_input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['texto_link'] : ($_POST['texto_enlace'] ?? '')); ?>"
                               placeholder="Texto del enlace">
                        <input type="url" name="enlace" class="dashboard_erasmus_form_input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['link'] : ($_POST['enlace'] ?? '')); ?>"
                               placeholder="https://site.educa.madrid.org/...">
                    </div>

                    <!-- INPUT FILE NORMAL -->
                    <div class="dashboard_erasmus_form_group">
                        <?php if ($modo_edit && $noticia_edit['imagen']): ?>
                            <label class="dashboard_erasmus_form_label">Imagen actual:</label>
                            <div class="dashboard_erasmus_imagen_actual">
                                <img src="<?php echo htmlspecialchars($noticia_edit['imagen']); ?>" alt="Imagen actual" style="max-width: 150px; max-height: 100px; border-radius: 8px;">
                                <p style="font-size: 0.9rem; color: var(--gris);"><?php echo htmlspecialchars(basename($noticia_edit['imagen'])); ?></p>
                            </div>
                        <?php endif; ?>
                        <label class="dashboard_erasmus_form_label">Nueva Imagen (JPG, PNG, GIF, WEBP)</label>
                        <input type="file" name="imagen" class="dashboard_erasmus_form_input" accept="image/*">
                        <small style="color: var(--gris);">Máx 5MB. Deja vacío para mantener la actual</small>
                    </div>

                    <div class="dashboard_erasmus_form_group">
                        <label class="dashboard_erasmus_form_label">Video (opcional)</label>
                        <input type="url" name="video" class="dashboard_erasmus_form_input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['video'] : ($_POST['video'] ?? '')); ?>"
                               placeholder="https://youtube.com/...">
                    </div>

                    <div class="dashboard_erasmus_form_group">
                        <label class="dashboard_erasmus_form_label">PDF (opcional)</label>
                        <input type="text" name="pdf" class="dashboard_erasmus_form_input" 
                               value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['pdf'] : ($_POST['pdf'] ?? '')); ?>"
                               placeholder="pdfs/documento.pdf">
                    </div>

                    
                    <div class="dashboard_erasmus_form_group" style="grid-column: 1 / -1;">
                        <label class="dashboard_erasmus_form_label">Contenido *</label>
                        <textarea name="texto" class="dashboard_erasmus_form_textarea" required><?php echo htmlspecialchars($modo_edit ? $noticia_edit['texto'] : ($_POST['contenido'] ?? '')); ?></textarea>
                    </div>

                    <div class="dashboard_erasmus_btn_group">
                        <button type="submit" class="dashboard_erasmus_btn dashboard_erasmus_btn_primary">
                            <i class="fas fa-save"></i> <?php echo $modo_edit ? 'Actualizar' : 'Añadir'; ?> Noticia
                        </button>
                    </div>
                </form>
            </div>

            <!-- LISTA DE NOTICIAS -->
            <div class="dashboard_erasmus_seccion_lista">
                <h2><i class="fas fa-list"></i> Lista de Noticias (<?php echo count($noticias); ?>)</h2>
                <?php if (!empty($noticias)): ?>
                    <div class="dashboard_erasmus_noticias_grid">
                        <?php foreach ($noticias as $noticia): ?>
                            <div class="dashboard_erasmus_noticia_card">
                                <?php if ($noticia['imagen']): ?>
                                    <div class="dashboard_erasmus_noticia_imagen">
                                        <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                                    </div>
                                <?php endif; ?>
                                <h3 class="dashboard_erasmus_noticia_titulo"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                                <div class="dashboard_erasmus_noticia_fecha">
                                    <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($noticia['fecha_publicacion'])); ?>
                                    <?php if (!empty($noticia['ultima_edicion_usuario_nombre'])): ?>
                                        <br>
                                        <small style="color: #666; font-size: 0.85rem;">
                                            <?php echo htmlspecialchars($noticia['ultima_edicion_usuario_nombre']); ?>
                                        </small>
                                    <?php endif; ?>
                                </div>

                                <div class="dashboard_erasmus_noticia_contenido">
                                    <?php echo htmlspecialchars(substr($noticia['texto'], 0, 150)); ?>...
                                </div>
                                <div class="dashboard_erasmus_noticia_medios">
                                    <?php if ($noticia['link']): ?>
                                        <a href="solicitud_otros_tramites.php" class="dashboard_erasmus_noticia_enlace">
                                            <i class="fas fa-external-link-alt"></i> Ver completo
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($noticia['video']): ?>
                                        <a href="<?php echo htmlspecialchars($noticia['video']); ?>" class="dashboard_erasmus_noticia_video" target="_blank">
                                            <i class="fas fa-video"></i> Video
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($noticia['pdf']): ?>
                                        <a href="<?php echo htmlspecialchars($noticia['pdf']); ?>" class="dashboard_erasmus_noticia_pdf" target="_blank">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="dashboard_erasmus_acciones_botones">
                                    <a href="?editar=<?php echo $noticia['id']; ?>" class="dashboard_erasmus_btn_small dashboard_erasmus_btn_editar">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar esta noticia?')">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?php echo $noticia['id']; ?>">
                                        <button type="submit" class="dashboard_erasmus_btn_small dashboard_erasmus_btn_delete">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="dashboard_erasmus_vacio">
                        <i class="fas fa-plane"></i>
                        <h3>No hay ofertas de empleo.</h3>
                        <p>Añade la primera oferta con el formulario de arriba.</p>
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
