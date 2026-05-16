<?php
// 1. INICIO DE SESIÓN Y SEGURIDAD
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$titulo_dashboard = "Dashboard Resultados Académicos";
$is_admin = ($_SESSION['usuario_rol'] === 'admin' || $_SESSION['usuario_rol'] === 'profesor' || $_SESSION['usuario_rol'] === 'otro');
$mensaje = '';

// Capturar el nombre del profesor desde la sesión o buscarlo en la BD
$nombre_profesor = $_SESSION['usuario_nombre'] ?? '';

if ($nombre_profesor === '') {
    $stmt_profesor = $conexion->prepare("SELECT nombre FROM profesores WHERE usuario_id = ? LIMIT 1");
    $stmt_profesor->bind_param("i", $_SESSION['usuario_id']);
    $stmt_profesor->execute();
    $res_profesor = $stmt_profesor->get_result();
    if ($fila_profesor = $res_profesor->fetch_assoc()) {
        $nombre_profesor = $fila_profesor['nombre'];
    }
    $stmt_profesor->close();
}

// 2. PROCESAR ACCIONES DEL FORMULARIO (POST)
if ($_POST && isset($_POST['accion'])) {
    switch ($_POST['accion']) {

        case 'eliminar':
            $id = (int) $_POST['id'];
            $stmt = $conexion->prepare("DELETE FROM resultados_academicos WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?msg=Sección eliminada correctamente");
                exit;
            }
            $stmt->close();
            break;

        case 'nueva':
            $titulo = trim($_POST['titulo']);
            $texto = trim($_POST['texto']);
            $fecha = date('Y-m-d');
            $enlace = trim($_POST['enlace']);
            $texto_enlace = trim($_POST['texto_enlace']);
            $imagen = '';
            $video = trim($_POST['video']);
            $pdf = trim($_POST['pdf']);

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'img/';
                if (!is_dir($upload_dir))
                    mkdir($upload_dir, 0777, true);
                $file_extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $new_filename = 'bolsa_empleo_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                        $imagen = $upload_path;
                    }
                }
            }

            // CORRECCIÓN LÍNEA 77: Ahora inserta en 'ultima_edicion_nombre' en lugar de 'ultima_edicion_usuario_id'
            $stmt = $conexion->prepare("INSERT INTO resultados_academicos (titulo, texto, link, texto_link, imagen, video, pdf, fecha_publicacion, ultima_edicion_nombre, ultima_edicion_fecha, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 1)");
            $stmt->bind_param("sssssssss", $titulo, $texto, $enlace, $texto_enlace, $imagen, $video, $pdf, $fecha, $nombre_profesor);

            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?msg=Noticia añadida correctamente");
                exit;
            }
            $stmt->close();
            break;

        case 'editar':
            $id = (int) $_POST['id'];
            $titulo = trim($_POST['titulo']);
            $texto = trim($_POST['texto']);
            $fecha = date('Y-m-d');
            $enlace = trim($_POST['enlace']);
            $texto_enlace = trim($_POST['texto_enlace']);
            $imagen = isset($_POST['imagen_existente']) ? trim($_POST['imagen_existente']) : '';
            $video = trim($_POST['video']);
            $pdf = trim($_POST['pdf']);

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'img/';
                if (!is_dir($upload_dir))
                    mkdir($upload_dir, 0777, true);
                $file_extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $new_filename = 'bolsa_empleo_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                        $imagen = $upload_path;
                        if (isset($_POST['imagen_existente']) && file_exists($_POST['imagen_existente'])) {
                            unlink($_POST['imagen_existente']);
                        }
                    }
                }
            }

            // CORRECCIÓN UPDATE: Aseguramos que guarde el nombre de texto plano directamente
            $stmt = $conexion->prepare("UPDATE resultados_academicos SET titulo=?, texto=?, fecha_publicacion=?, link=?, texto_link=?, imagen=?, video=?, pdf=?, ultima_edicion_nombre=?, ultima_edicion_fecha=NOW() WHERE id=?");
            $stmt->bind_param("sssssssssi", $titulo, $texto, $fecha, $enlace, $texto_enlace, $imagen, $video, $pdf, $nombre_profesor, $id);

            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?msg=Noticia actualizada correctamente");
                exit;
            }
            $stmt->close();
            break;
    }
}

if (isset($_GET['msg'])) {
    $mensaje = $_GET['msg'];
}

// 3. CARGAR LISTADO DE NOTICIAS
$stmt = $conexion->prepare("SELECT * FROM resultados_academicos ORDER BY fecha_publicacion DESC");
$stmt->execute();
$resultado = $stmt->get_result();
$noticias = [];
while ($fila = $resultado->fetch_assoc()) {
    $noticias[] = $fila;
}
$stmt->close();

$modo_edit = false;
$noticia_edit = null;
if (isset($_GET['editar'])) {
    $id_edit = (int) $_GET['editar'];
    $stmt = $conexion->prepare("SELECT * FROM resultados_academicos WHERE id = ?");
    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $result = $stmt->get_result();
    $noticia_edit = $result->fetch_assoc();
    $modo_edit = $noticia_edit !== null;
    $stmt->close();
}
?>
<?php include 'dashboard_head.php'; ?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestión Resultados Académicos - Dashboard Admin</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="style_dashboard.css">
    </head>
    <body>
        <div class="dashboard_erasmus_container">
            

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
                                   value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['titulo'] : ''); ?>">
                        </div>

                        <div class="dashboard_erasmus_form_group">
                            <label class="dashboard_erasmus_form_label">Enlace (opcional)</label>
                            <input type="text" name="texto_enlace" class="dashboard_erasmus_form_input" 
                                   value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['texto_link'] : ''); ?>"
                                   placeholder="Texto del enlace">
                            <input type="url" name="enlace" class="dashboard_erasmus_form_input" 
                                   value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['link'] : ''); ?>"
                                   placeholder="https://...">
                        </div>

                        <div class="dashboard_erasmus_form_group">
                            <?php if ($modo_edit && $noticia_edit['imagen']): ?>
                                <label class="dashboard_erasmus_form_label">Imagen actual:</label>
                                <div class="dashboard_erasmus_imagen_actual">
                                    <img src="<?php echo htmlspecialchars($noticia_edit['imagen']); ?>" alt="Imagen" style="max-width: 150px; border-radius: 8px;">
                                </div>
                            <?php endif; ?>
                            <label class="dashboard_erasmus_form_label">Nueva Imagen</label>
                            <input type="file" name="imagen" class="dashboard_erasmus_form_input" accept="image/*">
                        </div>

                        <div class="dashboard_erasmus_form_group">
                            <label class="dashboard_erasmus_form_label">Video (opcional)</label>
                            <input type="url" name="video" class="dashboard_erasmus_form_input" 
                                   value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['video'] : ''); ?>">
                        </div>

                        <div class="dashboard_erasmus_form_group">
                            <label class="dashboard_erasmus_form_label">PDF (opcional)</label>
                            <input type="text" name="pdf" class="dashboard_erasmus_form_input" 
                                   value="<?php echo htmlspecialchars($modo_edit ? $noticia_edit['pdf'] : ''); ?>">
                        </div>

                        <div class="dashboard_erasmus_form_group" style="grid-column: 1 / -1;">
                            <label class="dashboard_erasmus_form_label">Contenido *</label>
                            <textarea name="texto" class="dashboard_erasmus_form_textarea" required><?php echo htmlspecialchars($modo_edit ? $noticia_edit['texto'] : ''); ?></textarea>
                        </div>

                        <div class="dashboard_erasmus_btn_group">
                            <button type="submit" class="dashboard_erasmus_btn dashboard_erasmus_btn_primary">
                                <i class="fas fa-save"></i> <?php echo $modo_edit ? 'Actualizar' : 'Añadir'; ?> Noticia
                            </button>
                        </div>
                    </form>
                </div>

                <div class="dashboard_erasmus_seccion_lista">
                    <h2><i class="fas fa-list"></i> Lista de Noticias (<?php echo count($noticias); ?>)</h2>
                    <?php if (!empty($noticias)): ?>
                        <div class="dashboard_erasmus_noticias_grid">
                            <?php foreach ($noticias as $noticia): ?>
                                <div class="dashboard_erasmus_noticia_card">
                                    <?php if ($noticia['imagen']): ?>
                                        <div class="dashboard_erasmus_noticia_imagen">
                                            <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" alt="">
                                        </div>
                                    <?php endif; ?>

                                    <h3 class="dashboard_erasmus_noticia_titulo"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>

                                    <div class="dashboard_erasmus_noticia_fecha">
                                        <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($noticia['fecha_publicacion'])); ?>

                                        <?php if (!empty($noticia['ultima_edicion_nombre'])): ?> <br> <small class="dashboard_ampa_fecha_editor">Editado por: <?php echo htmlspecialchars($noticia['ultima_edicion_nombre']); ?></small> <?php endif; ?>
                                    </div>

                                    <div class="dashboard_ampa_entrada_texto"> <?php echo htmlspecialchars(substr($noticia['texto'], 0, 150)); ?>... </div> <div class="dashboard_erasmus_acciones_botones">
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